<?php

use App\Models\Event;
use App\Traits\WithSortingAndSearching;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Features\SupportValidation;

new class extends Component {
    use WithPagination, WithSortingAndSearching;

    public string $title = '';
    public string $description = '';
    public string $start_datetime = '';
    public string $end_datetime = '';
    public string $location = '';
    public float $cost_for_members = 0;
    public float $cost_for_guests = 0;
    public int $min_participants = 0;
    public int $max_participants = 0;
    public bool $guests_allowed = false;
    public int $max_guests_per_member = 0;
    public string $is_active = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;

    public array $categoryColors = [
        'Curry' => 'yellow',
        'Food' => 'red',
        'Achievements' => 'blue',
        'Awards' => 'green',
        'Sport' => 'purple',
        'Promotion' => 'amber',
        'Commission' => 'slate',
        'Birth' => 'rose',
        'Death' => 'black',
        'Marriage' => 'white',
        'Social' => 'emerald',
        'Other' => 'indigo',
    ];


    protected function rules(): array
    {
        return [
            'title' => 'required|min:6|max:255|string',
            'description' => 'required|min:10|string',
            'start_datetime' => 'required',
            'end_datetime' => 'required',
            'location' => 'required|min:10|string',
            'cost_for_members' => 'required|decimal:3,2',
            'cost_for_guests' => 'required|decimal:3,2',
            'min_participants' => 'required|int',
            'max_participants' => 'required|int',
            'guests_allowed' => 'bool',
            'max_guests_per_member' => 'required|int',
            'is_active' => 'required|string',
        ];
    }


    protected function applySearchFilters($query): mixed
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->whereHas('organizer', function ($userQuery) {
                $userQuery->where('name', 'like', '%' . $this->search . '%');
            })
                ->orWhere('event_title', 'like', '%' . $this->search . '%');
        });
    }

    public function showCreate(): void
    {
        $this->showCreateModal = true;
    }


    public function create(): void
    {
        $this->validate();
    }

    public function with(): array
    {
        $query = Event::query()->with('organizer')->with('categories')->with('title')->orderBy('event_date');

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(10);

        return [
            'events' => $paginated,
        ];
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

    <flux:table :paginate="$events">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'organizer'" :direction="$sortDirection"
                               wire:click="sort('organizer')">Creator
            </flux:table.column>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'event_date'" :direction="$sortDirection"
                               wire:click="sort('event_date')">Date
            </flux:table.column>
            <flux:table.column>Time</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'category_id'" :direction="$sortDirection"
                               wire:click="sort('category_id')">Category
            </flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($events as $event)
                <flux:table.row :key="$event->id">
                    <flux:table.cell>{{ $event->organizer->name ?? 'Unknown' }}</flux:table.cell>

                    <flux:table.cell>{{ $event->title->title ?? 'Unknown'}}</flux:table.cell>

                    <flux:table.cell>{{ $event->event_date->format('d M Y') }}</flux:table.cell>
                    <flux:table.cell>{{ $event->event_time->format('g:i A') }}</flux:table.cell>
                    <flux:table.cell>
                        @foreach($event->categories as $category)
                            <flux:badge size="sm"
                                        color="{{ $categoryColors[$category->name] ?? 'Unknown' }}">
                                {{ $category->name ?? 'Unknown' }}
                            </flux:badge>
                        @endforeach
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>
                                @can('event-read')
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
                            <flux:heading size="lg">No Events Found...</flux:heading>
                        </flux:badge>
                    </div>
                </flux:table.cell>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
