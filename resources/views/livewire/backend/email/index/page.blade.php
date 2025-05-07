<?php

use App\Mail\ReplyToSenderMail;
use App\Models\Email;
use App\Models\Reply;
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


    public function showReplyModal($emailId): void
    {
        $this->emailId = $emailId;

        $mailUser = Email::query()->with('reply.user')->findOrFail($emailId);

        $this->name = $mailUser->sender_name;
        $this->email = $mailUser->sender_email;
        $this->subject = $mailUser->email_subject;


        $this->showReplyFormId = true;
    }

    public function cancelReplyModal(): void
    {
        $this->showReplyFormId = false;
        $this->name = '';
        $this->email = '';
        $this->subject = '';
        $this->message = '';
    }

    public function sendReply(): void
    {
        if (Auth::user() && Auth::user()->can('mail-reply')) {

            // Check if email ID is valid
            if (!$this->emailId) {
                Flux::toast(
                    'Error',
                    'Missing email reference. Please try again.',
                    'error',
                );
                return;
            }

            // Validate reply content
            $validated = $this->validate([
                'name' => ['required'],
                'email' => ['required'],
                'subject' => ['required', 'string', 'min:5', 'max:250'],
                'message' => ['required', 'string', 'min:10', 'max:1000'],
            ]);

            // Create a new reply
            Reply::create([
                'email_id' => $this->emailId,
                'user_id' => auth()->id(),
                'reply_subject' => $this->subject,
                'reply_content' => $this->message,
            ]);

            $emailModel = Email::findOrFail($this->emailId);
            Mail::to($this->email)->queue(new ReplyToSenderMail($emailModel));


            //toast message
            Flux::toast(
                'Reply Sent.',
                'Your reply has been sent to the originator.',
                'success',
            );

            // Reset state
            $this->showReplyFormId = false;
            $this->name = '';
            $this->email = '';
            $this->subject = '';
            $this->message = '';
            $this->emailId = 0; // Reset the email ID
        } else {
            abort(403, 'You are not authorised to reply to emails!');
        }
    }


    public function archiveMail(int $mailId): void
    {
        if (Auth::user() && Auth::user()->can('mail-destroy')) {
            $mail = Email::findOrFail($mailId);

            $this->authorize('delete', $mail);

            $mail->delete();

            Flux::toast(
                heading: 'Mail Archived.',
                text: 'The email has been archived successfully.',
                variant: 'success',
            );
        } else {
            abort(403, 'You are not authorised to archive emails!');
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
        $query = Email::query()->with('reply.user');

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
            <flux:heading size="xl" level="1">{{ __('Received Emails') }}</flux:heading>
            <flux:subheading
                size="lg">{{ __('Messages submitted from the Contact Us form.') }}</flux:subheading>
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
                                <flux:heading size="sm" level="3">Subject: {{ $mail->email_subject ?? 'N/A' }}</flux:heading>
                                <flux:text class="prose">
                                    <x-markdown>{!! $mail->email_content !!}</x-markdown>
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
                                        Subject: {{ $mail->reply->reply_subject ?? 'N/A' }}</flux:heading>
                                    <flux:text>
                                        <x-markdown>{!! $mail->reply->reply_content !!}</x-markdown>
                                    </flux:text>
                                @else
                                    <div class="flex justify-center items-center">
                                        <flux:badge size="xl" color="rose" variant="subtle">
                                            <flux:heading size="lg">Please reply</flux:heading>
                                        </flux:badge>
                                    </div>
                                @endif

                                <div class="flex w-full items-center justify-end gap-2 mt-2">
                                    @can('mail-reply')
                                        @if(!$mail->reply || $mail->reply->count() === 0)
                                            <flux:button variant="primary" size="sm" icon="envelope"
                                                         wire:click="showReplyModal({{ $mail->id ?? 'N/A' }})">Reply
                                            </flux:button>
                                        @endif
                                    @endcan

                                    @can('mail-archive')
                                        <flux:button variant="danger" size="sm" icon="archive-box"
                                                     wire:click="archiveMail({{ $mail->id ?? 'N/A' }})" wire:confirm.prompt="Are you sure you want to archive this email?\n\nType ARCHIVE to confirm|ARCHIVE">Archive
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

            <!--reply modal-->
            <flux:modal wire:model.self="showReplyFormId" title="Message" size="lg" class="max-w-lg w-auto">
                <form wire:submit.prevent="sendReply()">
                    @csrf
                    <div class="flex flex-col w-full mt-10 gap-5">
                        <flux:input
                            wire:model="name"
                            name="name"
                            label="To"
                            disabled="true"
                            required="true"
                            value="{{ $mail->name ?? 'N/A' }}"
                            placeholder="Enter your reply subject here..."
                            class="w-full h-full"/>

                        <flux:input
                            wire:model="email"
                            name="email"
                            label="Email"
                            disabled="true"
                            required="true"
                            value="{{ $mail->user->email ?? 'N/A'}}"
                            placeholder="Enter your reply subject here..."
                            class="w-full h-full"/>

                        <flux:input
                            wire:model="subject"
                            name="subject"
                            label="Subject"
                            required="true"
                            value="{{ $mail->email_subject ?? 'N/A'}}"
                            placeholder="Enter your reply subject here..."
                            class="w-full h-full"/>

                        <flux:editor
                            wire:model="message"
                            name="message"
                            label="Content"
                            rows="20"
                            required="true"
                            placeholder="Enter your reply here..."
                            class="w-full h-full"/>

                    </div>
                    <div class="flex w-full items-end justify-end gap-4 mt-4">
                        <flux:button type="button" variant="primary" wire:click="cancelReplyModal()">Cancel
                        </flux:button>
                        <flux:button type="submit" variant="danger" class="mt-4">Send</flux:button>
                    </div>
                </form>
            </flux:modal>
        </flux:table>
    </div>
</div>
