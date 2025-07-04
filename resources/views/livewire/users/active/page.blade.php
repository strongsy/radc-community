<?php

use App\Models\User;
use App\Traits\WithSortingAndSearching;
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

    public bool $viewUserModal = false;

    public bool $showUserRolesModal = false;

    public array $selectedRoles = [];

    public array $availableRoles = [];

    public array $user = [];

    public User $selectedUser;

    public array $details = [];

    public array $roleColors = [
        'super-admin' => 'yellow',
        'admin' => 'red',
        'editor' => 'blue',
        'moderator' => 'green',
        'user' => 'purple',
    ];


    protected array $rules = [
        'selectedRoles' => 'array',
        'selectedRoles.*' => 'in_array:availableRoles', // Validates all items against $availableRoles
    ];

    public function mount(User $user): void
    {
        $this->selectedUser = $user;
    }


    #[NoReturn] public function assignRolesToUser(mixed $user_id): void
    {
        if (Auth::user() && Auth::user()->can('user-update')) {


            $user = User::with('roles')->findOrFail($user_id);

            $userRoles = $user->roles?->pluck('name')->toArray() ?? [];

            // Now convert to array
            $this->user = [
                'id' => $user->id ?? 'No ID',
                'name' => $user->name ?? 'No Name',
                'roles' => $userRoles,
            ];

            // Load all available roles (hardcoded or fetched from a Role model)
            $this->availableRoles = ['admin', 'editor', 'moderator', 'user'];

            // Pre-select roles already assigned to the user
            $this->selectedRoles = $userRoles;

            $this->showUserRolesModal = true;
        } else {
            abort(403, 'You are not authorised to assign roles to users!');
        }
    }


    public function saveRoles(): void
    {
        if (Auth::user() && Auth::user()->can('user-update')) {
            $this->validate([
                'selectedRoles' => 'array',
                'selectedRoles.*' => 'in:' . implode(',', $this->availableRoles),
            ]);

            // Re-fetch the user model
            $user = User::findOrFail($this->user['id']);
            $user->syncRoles($this->selectedRoles); // Spatie's method to sync roles

            activity()->log($user->name . ' roles were changed.');

            // Close modal and reset state
            $this->showUserRolesModal = false;
            $this->user = [];
            $this->selectedRoles = [];

            Flux::toast(
                text: 'User roles updated successfully.',
                heading: 'Success',
                variant: 'success',
            );
        } else {
            abort(403, 'You are not authorised to update user roles!');
        }
    }

    public function delete(User $user): void
    {
        if (Auth::user() && Auth::user()->can('user-destroy')) {

            // Check if the user being deleted is a super-admin
            if ($user->hasRole('super-admin')) {

                Flux::toast(
                    text: 'Super-admin users cannot be deleted.',
                    heading: 'Cannot proceed',
                    variant: 'danger',
                );
                return;
            }

            $user->delete();

            Flux::toast(
                text: 'User deleted successfully.',
                heading: 'Success',
                variant: 'success',
            );

        } else {
            abort(403, 'You are not authorised to delete users!');
        }
    }

    public function update(User $user): void
    {
        if (Auth::user() && Auth::user()->can('user-update')) {

            // Check if the user being blocked is a super-admin
            if ($user->hasRole('super-admin')) {

                Flux::toast(
                    text: 'Super-admin users cannot be blocked.',
                    heading: 'Cannot proceed',
                    variant: 'danger',
                );
                return;
            }

            $user->update(
                [
                    'is_blocked' => true,
                ]
            );

            activity()->log($user->name . ' was blocked.');

            Flux::toast(
                text: 'User blocked successfully.',
                heading: 'Success',
                variant: 'success',
            );

        } else {
            abort(403, 'You are not authorised to block users!');
        }
    }

    public function read(mixed $userId): array
    {
        $details = $this->selectedUser::with('roles:name');

        // Set the user property with the fetched details

        $this->viewUserModal = true;

        return [
            'selectedUser' => $details,
        ];

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
        $query = User::query()->with('roles:name')->with('entitlements')->with('community')->with('membership')->where('is_approved', true)->where('is_blocked', false);


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
            <flux:heading size="xl" level="1">{{ __('Active Users') }}</flux:heading>
            <flux:subheading
                size="lg">{{ __('List of users that have been authorised for access by admins') }}</flux:subheading>
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

            <flux:table.column>Roles</flux:table.column>

            <flux:table.column>Entitlements</flux:table.column>

            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)

                <flux:table.row :key="$user->id">
                    {{--<flux:table.cell class="flex items-center gap-3">

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
                    </flux:table.cell>--}}

                    <flux:table.cell variant="strong">{{ $user->name ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:link href="mailto:{{ $user->email }}">{{ $user->email ?? 'N/A' }}</flux:link>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $user->community->colour ?? 'N/A' }}">{{ $user->community->name ?? 'N/A' }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $user->membership->colour ?? 'N/A' }}">{{ $user->membership->name ?? 'N/A' }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>{{ $user['created_at']->format('d M Y, g:i A') ?? 'N/A' }}</flux:table.cell>

                    <flux:table.cell>
                        @foreach ($user->roles as $role)
                            <flux:badge size="sm"
                                        color="{{ $roleColors[$role->name] ?? 'N/A' }}">{{ ucfirst($role->name) ?? 'N/A' }}</flux:badge>
                        @endforeach
                    </flux:table.cell>

                    <flux:table.cell>
                        @foreach ($user->entitlements as $entitlement)
                            <flux:text>{{ $entitlement->name ?? 'None' }}</flux:text>
                        @endforeach
                    </flux:table.cell>

                    <!--actions-->
                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>
                                @can('user-read')
                                    <flux:menu.item icon="eye"
                                                    wire:click="read({{ $user->id ?? 'N/A' }})">View
                                    </flux:menu.item>
                                @endcan

                                @can('user-update')
                                    <flux:menu.item icon="shield-check"
                                                    wire:click="assignRolesToUser({{ $user->id ?? 'N/A' }})">Roles
                                    </flux:menu.item>
                                @endcan

                                @can('user-destroy')
                                    <flux:menu.item icon="user-minus" wire:click="delete({{ $user->id ?? 'N/A' }})"
                                                    wire:confirm.prompt="Are you sure you want to delete this user?\n\nType DELETE to confirm|DELETE">
                                        Delete
                                    </flux:menu.item>
                                @endcan

                                @can('user-block')
                                    <flux:menu.item icon="no-symbol" wire:click="update({{ $user->id ?? 'N/A' }})"
                                                    wire:confirm.prompt="Are you sure you want to block this user?\n\nType BLOCK to confirm|BLOCK">
                                        Block
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

        <!--roles modal-->
        <flux:modal wire:model.self="showUserRolesModal" title="Assign Roles" size="lg" class="max-w-sm w-auto">
            <form wire:submit.prevent="saveRoles">

                <div class="grid grid-cols-2 items-center justify-between gap-4 mt-5">
                    @foreach ($availableRoles as $role)
                        <label class="block">
                            <flux:checkbox wire:model="selectedRoles" label="{{$role}}" value="{{ $role }}"
                                           :disabled="$role === 'user' && in_array('user', $selectedRoles, true)"
                            />
                            {{--<input type="checkbox" wire:model="selectedRoles" value="{{ $role }}"/>
                            <span class="ml-2">{{ ucfirst($role) ?? 'N/A' }}</span>--}}
                        </label>
                    @endforeach

                </div>
                <div class="flex w-full items-end justify-end gap-4 mt-4">
                    <flux:button type="button" variant="primary" wire:click="showUserRolesModal = false">Cancel
                    </flux:button>
                    <flux:button type="submit" variant="danger" class="mt-4">Save</flux:button>
                </div>
            </form>
        </flux:modal>

        <!--view modal-->
        <flux:modal wire:model.self="viewUserModal" title="View User" size="lg" class="max-w-sm w-auto">
            <div class="flex flex-col gap-4">
                <flux:heading size="lg">{{ $user->name ?? 'Unknown' }}</flux:heading>
                <flux:separator variant="subtle"/>
                <flux:heading size="sm">Affiliation</flux:heading>
                <flux:text>{{ $user->affiliation ?? 'Not found' }}</flux:text>
                <flux:heading size="sm">Roles</flux:heading>
                {{--@foreach ($this->selectedUser as $role)

                    <flux:badge size="sm">{{ ucfirst($role->name) ?? 'N/A' }}</flux:badge>
                @endforeach

                @foreach ($selectedUser->entitlements as $entitlement)
                    <flux:text>{{ $entitlement ?? 'N/A' }}</flux:text>
                @endforeach--}}
            </div>
            <div class="flex w-full items-end justify-end gap-4 mt-4">
                <flux:button type="button" variant="primary" wire:click="viewUserModal = false">Close
                </flux:button>
            </div>
        </flux:modal>
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
