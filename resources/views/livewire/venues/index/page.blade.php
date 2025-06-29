<?php

use App\Models\Venue;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\Traits\WithSortingAndSearching;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination, WithSortingAndSearching;

    public bool $showVenueModal = false;

    public Venue $updating;

    public bool $isCreating = false;

    protected array $messages = [
        'updating.venue.unique' => 'This venue already exists with this address.',
        'updating.address.unique' => 'This address is already registered with this venue name.',
    ];


    protected function rules(): array
    {
        return [
            'updating.venue' => [
                'required',
                Rule::unique('venues', 'venue')
                    ->where('address', $this->updating->address)
                    ->ignore($this->updating->id)
            ],

            'updating.address' => [
                'required',
                Rule::unique('venues', 'address')
                    ->where('venue', $this->updating->venue)
                    ->ignore($this->updating->id)
            ],

            'updating.city' => 'required',
            'updating.county' => 'required',
            'updating.post_code' => 'required',
        ];
    }


    /* create a blank venue model */
    public function mount(): void
    {
        $this->updating = $this->makeBlankVenue();
    }

    /* create a blank venue model */
    public function makeBlankVenue(): Venue
    {
        return Venue::make();
    }

    /* reset errors */
    public function updatedShowVenueModal($value): void
    {
        if (!$value) {
            $this->resetErrorBag();
        }
    }

    /* search filters */
    protected function applySearchFilters($query): mixed
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('venue', 'like', '%' . $this->search . '%')
                ->orWhere('city', 'like', '%' . $this->search . '%')
                ->orWhere('address', 'like', '%' . $this->search . '%');
        });
    }

    /* reset the form */
    private function resetForm(): void
    {
        $this->updating = $this->makeBlankVenue();

    }

    /* delete venue */
    public function delete($venueId): void
    {
        if (Auth::user() && Auth::user()->can('venue-delete')) {
            $venue = Venue::findOrFail($venueId);
            $venue->delete();

            Flux::toast(
                heading: 'Success',
                text: "Venue deleted successfully.",
                variant: 'success',
            );

        } else {
            abort(403, 'You are not authorised to delete venues!');
        }

    }

    /* show create modal */
    public function create(): void
    {
        $this->isCreating = true;
        $this->updating = $this->makeBlankVenue();
        $this->showVenueModal = true;

    }

    /* show modal with selected venue details */
    public function update(Venue $venue): void
    {
        $this->isCreating = false;

        $this->updating = $venue;

        $this->resetErrorBag();

        $this->showVenueModal = true;

    }

    /* update the venue */
    public function save(): void
    {
        if ($this->isCreating && !Auth::user()?->can('venue-create')) {
            abort(403, 'You are not authorised to create venues!');
        }

        if (!$this->isCreating && !Auth::user()?->can('venue-update')) {
            abort(403, 'You are not authorised to update venues!');
        }

        try {
            $this->validate();

            $exists = Venue::where('venue', $this->updating->venue)
                ->where('address', $this->updating->address)
                ->when(!$this->isCreating, function ($query) {
                    return $query->where('id', '!=', $this->updating->id);
                })
                ->exists();

            if ($exists) {
                $this->addError('updating.venue', 'A venue already exists with this address.');
                return;
            }

            $this->updating->save();

            $message = $this->isCreating ? 'created' : 'updated';

            $this->showVenueModal = false;
            $this->resetForm();
            $this->dispatch("venue-$message");

            Flux::toast(
                heading: 'Success',
                text: "Venue $message successfully.",
                variant: 'success',
            );

        } catch (Exception $e) {
            if ($e instanceof QueryException && str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->addError('updating.venue', 'A venue already exists with this address.');
            } else {
                Flux::toast(
                    heading: 'Error',
                    text: "A venue already exists with this address",
                    variant: 'error',
                );
                Log::error('Venue save error: ' . $e->getMessage());
            }
        }
    }


    /* get the venue table */
    public function with(): array
    {
        $query = Venue::query();

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(10);

        return [
            'venues' => $paginated,
        ];
    }
}; ?>

<div class="flex flex-col gap-5 w-full">
    <div>
        <div class="relative w-full">
            <flux:heading size="xl" level="1">{{ __('Venues') }}</flux:heading>
            <flux:subheading
                size="lg">{{ __('Venue addresses for events.') }}</flux:subheading>
        </div>
    </div>

    <!-- search field -->
    <div class="flex flex-1/2 items-center justify-between">
        <div class="flex items-center">
            <flux:input icon="magnifying-glass" placeholder="Search..." type="text" class="w-full"
                        wire:model.live.debounce.500ms="search"/>

        </div>

        <div class="flex items-center gap-2">
            <flux:button icon="plus" variant="primary" class="ml-2" wire:click="create">New</flux:button>
        </div>

    </div>

    <x-search-and-sort
        :search="$search"
        :sortBy="$sortBy"
        :sortDirection="$sortDirection"
    />

    <flux:separator variant="subtle"/>

    <flux:table :paginate="$venues">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'venue'" :direction="$sortDirection"
                               wire:click="sort('venue')">Venue
            </flux:table.column>
            <flux:table.column>Address</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'city'" :direction="$sortDirection"
                               wire:click="sort('city')">City
            </flux:table.column>
            <flux:table.column>County</flux:table.column>
            <flux:table.column>Post Code</flux:table.column>
            <flux:table.column>Created At</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($venues as $venue)
                <flux:table.row>
                    <flux:table.cell>{{ $venue->venue ?? 'Unknown' }}</flux:table.cell>
                    <flux:table.cell>{{ $venue->address ?? 'Unknown' }}</flux:table.cell>
                    <flux:table.cell>{{ strtoupper($venue->city) ?? 'Unknown' }}</flux:table.cell>
                    <flux:table.cell>{{ $venue->county ?? 'Unknown' }}</flux:table.cell>
                    <flux:table.cell>{{ strtoupper($venue->post_code) ?? 'Unknown' }}</flux:table.cell>
                    <flux:table.cell>{{ $venue->created_at->format('d M Y, g:i A') ?? 'No date' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>
                                @can('event-update')
                                    <flux:menu.item icon="pencil-square" wire:click="update({{ $venue->id ?? 'N/A' }})">
                                        Edit
                                    </flux:menu.item>
                                @endcan

                                @can('event-destroy')
                                    <flux:menu.item icon="trash" wire:click="delete({{ $venue->id ?? 'N/A' }})"
                                                    wire:confirm.prompt="Are you sure you want to delete this venue?\n\nType DELETE to confirm|DELETE"
                                    >
                                        Delete
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
                            <flux:heading size="lg">No Venues Found...</flux:heading>
                        </flux:badge>
                    </div>
                </flux:table.cell>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <!-- venue modal -->
    <form wire:submit.prevent="save">
        <flux:modal wire:model="showVenueModal" title="{{ $isCreating ? 'Create' : 'Edit' }} Venue"
                    size="lg" class="max-w-sm w-auto">
            <div class="flex flex-col gap-4">
                <flux:input wire:model="updating.venue" label="Venue" placeholder="Venue" type="text"
                            class="w-full"/>
                <flux:input wire:model="updating.address" label="Address" placeholder="Address" type="text"
                            class="w-full"/>
                <flux:input wire:model="updating.city" label="City" placeholder="City" type="text" class="w-full"/>
                <flux:input wire:model="updating.county" label="County" placeholder="County" type="text"
                            class="w-full"/>
                <flux:input wire:model="updating.post_code" label="Post Code" placeholder="Post Code" type="text"
                            class="w-full"/>
            </div>
            <div class="flex w-full items-end justify-end gap-4 mt-4">
                <flux:button type="button" variant="primary" wire:click="$set('showVenueModal', false)"
                >Close
                </flux:button>
                <flux:button type="submit" variant="danger">Save</flux:button>
            </div>

        </flux:modal>
    </form>
</div>

