<?php

use App\Mail\UserAuthorisedConfirmationMail;
use App\Models\User;
use App\Notifications\UserAccountApprovedNotification;
use App\Traits\WithSortingAndSearching;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Flux\Flux;

new class extends Component {
    use WithPagination, WithSortingAndSearching;

    public function update(User $user, $approvedBy): void
    {
        if (Auth::user() && Auth::user()->can('user-activate')) {

            $user->update([
                'is_approved' => true,
                'approved_at' => Carbon::now(),
                'approved_by' => Auth::user()->id,
            ]);

            if (!$user->roles) {
                $user->assignRole('user');
            }

            $approvedBy = Auth::user()->name;

            activity()->log(Auth::user()->name . ' activated ' . $user->name);

            $user->notify(new UserAccountApprovedNotification($user, $approvedBy));

            Flux::toast(
                text: 'User activated successfully.',
                heading: 'Success',
                variant: 'success',
            );


        } else {
            abort(403, 'You are not authorised to activate users!');
        }
    }

    protected function applySearchFilters($query): mixed
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('email', 'like', '%' . $this->search . '%')
                ->orWhere('name', 'like', '%' . $this->search . '%');
        });
    }


    public function with(): array
    {
        $query = User::query()->with('community')->with('membership')->where('is_active', false)->where('is_blocked', false);

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(10);

        return [
            'users' => $paginated,
        ];
    }

} ?>

<div class="flex flex-col gap-5 w-full">
    <div>
        <div class="relative w-full">
            <flux:heading size="xl" level="1">{{ __('New Registrations') }}</flux:heading>
            <flux:subheading
                size="lg">{{ __('List of users that registered for access and are awaiting approval.') }}</flux:subheading>
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

    <!-- users table -->
    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>Affiliation</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                               wire:click="sort('name')">Name
            </flux:table.column>

            <flux:table.column>Email</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'community'" :direction="$sortDirection"
                               wire:click="sort('community')">Community
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'membership'" :direction="$sortDirection"
                               wire:click="sort('membership')">Membership
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                               wire:click="sort('created_at')">Authorised At
            </flux:table.column>

            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)

                <flux:table.row :key="$user->id">
                    <flux:table.cell class="flex items-center gap-3">

                        <flux:dropdown hover="true" position="bottom center">
                            <flux:avatar icon="eye" as="button" size="sm"></flux:avatar>

                            <flux:popover class="relative max-w-[15rem]">

                                <flux:heading class="mt-2">{{ $user->name ?? 'N/A' }}</flux:heading>

                                <flux:separator variant="subtle" class="mt-2"></flux:separator>

                                <flux:text class="mt-3">
                                    {{ $user->affiliation ?? 'N/A' }}
                                </flux:text>

                            </flux:popover>
                        </flux:dropdown>
                    </flux:table.cell>

                    <flux:table.cell variant="strong">{{ $user->name ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>{{ $user->email ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $user->community->colour ?? 'N/A' }}">{{ $user->community->name ?? 'N/A' }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $user->membership->colour ?? 'N/A' }}">{{ $user->membership->name ?? 'N/A' }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>{{ $user['created_at']->format('d M Y, g:i A') ?? 'N/A' }}</flux:table.cell>

                    <!--actions-->
                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>
                                @can('user-activate')
                                    <flux:menu.item icon="user-plus" wire:click="update({{ $user->id ?? 'N/A' }})"
                                                    wire:confirm="Are you sure you want to activate this user?">
                                        Activate
                                    </flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.cell colspan="8">
                    <div class="flex w-full justify-center items-center gap-3">
                        <flux:badge size="xl" color="teal" variant="subtle" class="my-3">
                            <flux:heading size="lg">No Users Found...</flux:heading>
                        </flux:badge>
                    </div>

                </flux:table.cell>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- <div class="flex justify-start items-center gap-3 mt-10">
         <flux:select size="sm" wire-model="perPage" label="Per Page">
             <flux:select.option>10</flux:select.option>
             <flux:select.option>20</flux:select.option>
             <flux:select.option>30</flux:select.option>
             <flux:select.option>40</flux:select.option>
             <flux:select.option>50</flux:select.option>
             <flux:select.option>100</flux:select.option>
         </flux:select>
     </div>--}}
</div>
