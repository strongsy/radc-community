<?php

use App\Mail\ReplyToSenderMail;
use App\Models\Email;
use App\Models\Reply;
use App\Models\User;
use App\Traits\WithSortingAndSearching;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination, WithSortingAndSearching;

    public bool $showReplyFormId = false;

    public bool $showResponseModal = false;

    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public string $parsedMessage = '';

    public string $emailId = '';

    /**
     * Apply search filters to a query.
     *
     * @param $query
     * @return mixed
     */
    protected function applySearchFilters($query): mixed
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('sender_email', 'like', '%' . $this->search . '%')
                ->orWhere('sender_name', 'like', '%' . $this->search . '%')
                ->orWhere('message', 'like', '%' . $this->search . '%');

            $q->orWhereHas('reply.user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });

            if (strtolower(trim($this->search)) === 'no replies') {
                $q->orWhereDoesntHave('replies');
            }
        });
    }

    public function initials($name): string
    {
        return Str::of($name)
            ->explode(' ')
            ->map(fn(string $segment) => Str::of($segment)->substr(0, 1))
            ->implode('');
    }

    public function unarchiveEmail($emailId): void
    {
        if (Auth::user() && Auth::user()->can('mail-restore')) {
            // Find the trashed email by ID
            $mail = Email::onlyTrashed()->findOrFail($emailId);

            // Authorize the user (if you have a policy for this)
            $this->authorize('restore', $mail);

            // Restore the email
            $mail->restore();

            // Show a success message
            Flux::toast(
                heading: 'Mail Restored.',
                text: 'The email has been restored successfully.',
                variant: 'success',
            );
        } else {
            abort(403, 'You are not authorized to restore emails!');
        }

    }

    public function deleteEmail($emailId): void
    {
        if (Auth::user() && Auth::user()->can('email-destroy')) {
            $email = Email::withTrashed()->find($emailId);
            $email->forceDelete();

            activity()
                ->performedOn($mail)
                ->event('deleted')
                ->log('The user has deleted the email.');

            // Show a success message
            Flux::toast(
                heading: 'Mail Deleted.',
                text: 'The email has been deleted successfully.',
                variant: 'success',
            );

            //activity()->log('email permanently deleted');
        } else {
            abort(403, 'You are not authorised to permanently delete emails!');
        }
    }


    /**
     * Retrieve mail records with their associated replies and users.
     * Applies search filters, sorting, and pagination to the query.
     *
     * @return array
     */
    public function with(): array
    {
        $query = Email::query()->with('reply.user')->onlyTrashed();

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(5);

        return ['mails' => $paginated];
    }
}
?>

<div class="flex flex-col gap-5 w-full">
    <div>
        <div class="relative w-full">
            <flux:heading size="xl" level="1">{{ __('Archived Emails') }}</flux:heading>
            <flux:subheading
                size="lg">{{ __('Archived messages submitted from the contact us form.') }}</flux:subheading>
        </div>
    </div>

    <!-- search field -->
    <div class="flex flex-1/2 items-center justify-between">
        <div class="flex items-center">
            <flux:input icon="magnifying-glass" placeholder="Search..." type="text" class="w-full"
                        wire:model.live.debounce.500ms="search"/>
        </div>

    </div>

    <x-search-and-sort
        :search="$search"
        :sortBy="$sortBy"
        :sortDirection="$sortDirection"
    />

    <flux:separator variant="subtle"/>

    <div class="flex flex-col w-full mx-auto max-w-5xl gap-5">
        <flux:table :paginate="$mails" class="table-auto">
            @if ($mails->count() > 0)
                <flux:table.columns>
                    <flux:table.column>
                    </flux:table.column>
                </flux:table.columns>
            @endif

            <flux:table.rows>
                @forelse ($mails as $mail)
                    <flux:table.row :key="$mail->id">
                        <flux:table.cell class="max-w-md text-wrap">
                            <flux:card class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <flux:badge size="md" color="primary" variant="subtle"
                                                class="w-8 h-8 flex items-center justify-center rounded-full">
                                        <span class="text-sm font-bold">{{ $this->initials($mail->sender_name) }}</span>
                                    </flux:badge>
                                    <flux:heading size="sm" level="3">{{ $mail->sender_name ?? 'N/A' }}</flux:heading>
                                </div>

                                <flux:heading size="sm" level="3">
                                    Email: {{ $mail->sender_email ?? 'N/A' }}</flux:heading>
                                <flux:text
                                    size="sm">{{ $mail->created_at->format('d M Y, g:i A') ?? 'N/A' }}</flux:text>
                                <flux:heading size="sm" level="3">Subject: {{ $mail->subject ?? 'N/A' }}</flux:heading>
                                <flux:text class="prose">
                                    <x-markdown>{!! $mail->message !!}</x-markdown>
                                </flux:text>

                                <flux:separator variant="subtle" class="my-2"/>

                                <flux:heading size="sm" level="3" class="mb-3">Response</flux:heading>


                                @if($mail->reply && $mail->reply->count() > 0)
                                    <div class="flex items-center gap-2">
                                        <flux:badge size="md" color="primary" variant="subtle"
                                                    class="w-8 h-8 flex items-center justify-center rounded-full">
                                            <span
                                                class="text-sm font-bold">{{ $this->initials($mail->reply->user->name) }}</span>
                                        </flux:badge>
                                        <flux:heading size="sm"
                                                      level="3">{{ $mail->reply->user->name ?? 'N/A' }}</flux:heading>
                                    </div>

                                    <flux:heading size="sm" level="3">
                                        Email: {{ $mail->reply->user->email ?? 'N/A' }}</flux:heading>
                                    <flux:text
                                        size="sm">{{ $mail->reply->created_at->format('d M Y, g:i A') ?? 'N/A' }}</flux:text>
                                    <flux:heading size="sm" level="3">
                                        Subject: {{ $mail->reply->subject ?? 'N/A' }}</flux:heading>
                                    <flux:text>
                                        <x-markdown>{!! $mail->reply->message !!}</x-markdown>
                                    </flux:text>
                                @else
                                    <div class="flex justify-center items-center">
                                        <flux:badge size="xl" color="rose" variant="subtle">
                                            <flux:heading size="lg">Please reply</flux:heading>
                                        </flux:badge>
                                    </div>
                                @endif

                                <flux:separator variant="subtle" class="my-2"/>
                                <flux:heading size="sm" level="3" class="mb-3">Archive details</flux:heading>
                                <flux:text size="sm">
                                    Archived on: {{ $mail->deleted_at->format('d M Y, g:i A') ?? 'N/A' }}
                                </flux:text>

                                <!-- actions -->
                                <div class="flex w-full items-center justify-end gap-2 mt-2">

                                    @can('mail-restore')
                                        <flux:button variant="primary" size="sm" icon="archive-box"
                                                     wire:click="unarchiveEmail({{ $mail->id ?? 'N/A' }})"
                                                     wire:confirm="Are you sure you want to unarchive this email?">
                                            Unarchive
                                        </flux:button>
                                    @endcan

                                    @can('mail-destroy')
                                        <flux:button variant="danger" size="sm" icon="trash"
                                                     wire:click="deleteEmail({{ $mail->id ?? 'N/A' }})"
                                                     wire:confirm.prompt="Are you sure you want to delete this email?\n\nType DELETE to confirm|DELETE">
                                            Delete
                                        </flux:button>
                                    @endcan
                                </div>
                            </flux:card>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.cell colspan="8">
                        <div class="flex w-full justify-center items-center">
                            <flux:badge size="xl" color="teal" variant="subtle">
                                <flux:heading size="lg">No Emails Found...</flux:heading>
                            </flux:badge>
                        </div>
                    </flux:table.cell>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
