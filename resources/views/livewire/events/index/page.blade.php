
<?php

use App\Models\Event;
use App\Models\Category;
use App\Models\Venue;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Spatie\MediaLibraryPro\Livewire\Concerns\WithMedia;
use Flux\Flux;

new class extends Component {
    use WithPagination, WithMedia;

    public string $search = '';
    public string $selectedCategories = '';
    public string $selectedVenue = '';
    public string $dateFilter = '';
    public string $sortBy = 'start_date';
    public string $sortDirection = 'asc';
    public bool $myEventsOnly = false;
    public array $event_files = [];

    protected array $queryString = [
        'search' => ['except' => ''],
        'selectedCategories' => ['except' => ''],
        'selectedVenue' => ['except' => ''],
        'dateFilter' => ['except' => ''],
        'sortBy' => ['except' => 'start_date'],
        'sortDirection' => ['except' => 'asc'],
        'myEventsOnly' => ['except' => false],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategories(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedVenue(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function updatingMyEventsOnly(): void
    {
        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategories = '';
        $this->selectedVenue = '';
        $this->dateFilter = '';
        $this->myEventsOnly = false;
        $this->resetPage();
    }

    public function sortByDate(): void
    {
        $this->sortBy('start_date');
    }

    public function sortByTitle(): void
    {
        $this->sortBy('title');
    }

    public function sortByVenue(): void
    {
        $this->sortBy('venue');
    }


    public function with(): array
    {
        $query = Event::with(['title', 'venue', 'categories', 'user', 'eventSessions']);

        // Apply search filter - Fixed logic
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('title', function ($titleQuery) {
                    $titleQuery->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('venue', function ($venueQuery) {
                        $venueQuery->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Apply category filter
        if (!empty($this->selectedCategories)) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->selectedCategories);
            });
        }

        // Apply venue filter
        if ($this->selectedVenue) {
            $query->where('venue_id', $this->selectedVenue);
        }

        // Apply date filter
        if ($this->dateFilter) {
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('start_date', today());
                    break;
                case 'tomorrow':
                    $query->whereDate('start_date', today()->addDay());
                    break;
                case 'this_week':
                    $query->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('start_date', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'upcoming':
                    $query->where('start_date', '>=', now());
                    break;
                case 'past':
                    $query->where('end_date', '<', now());
                    break;
            }
        }

        // Apply my events filter
        if ($this->myEventsOnly) {
            $query->where('user_id', auth()->id());
        }

        // Apply sorting - Fixed to avoid duplicate joins
        if ($this->sortBy === 'title') {
            $query->leftJoin('titles', 'events.title_id', '=', 'titles.id')
                ->orderBy('titles.name', $this->sortDirection)
                ->select('events.*');
        } elseif ($this->sortBy === 'venue') {
            $query->leftJoin('venues', 'events.venue_id', '=', 'venues.id')
                ->orderBy('venues.name', $this->sortDirection)
                ->select('events.*');
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return [
            'events' => $query->paginate(9),
            'categories' => Category::orderBy('name')->get(),
            'venues' => Venue::orderBy('name')->get(),
            'totalEvents' => Event::count(),
            'myEvents' => auth()->check() ? Event::where('user_id', auth()->id())->count() : 0,
        ];
    }
}; ?>

<div class="flex  flex-col translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750">
    <!-- Header -->
    <flux:card class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <flux:heading size="xl">
                Community Events
            </flux:heading>
            <flux:text>
                Discover and participate in events in your community
            </flux:text>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <flux:button icon="plus" variant="danger" href="{{ route('events.create') }}">Create Event</flux:button>
        </div>
    </flux:card>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <flux:card>
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="calendar"/>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <div>
                            <flux:heading size="sm">Total Events</flux:heading>
                            <flux:heading size="xl">{{ $totalEvents }}</flux:heading>
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="user"/>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <div>
                            <flux:heading size="sm">My Events</flux:heading>
                            <flux:heading size="xl">{{ $myEvents }}</flux:heading>
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="clock"/>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <div>
                            <flux:heading size="sm">Upcoming Events</flux:heading>
                            <flux:heading
                                size="xl">{{ Event::where('start_date', '>=', now())->count() }}</flux:heading>
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Filters -->
    <flux:card>
        <div>
            <flux:heading size="sm">Filters</flux:heading>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="space-y-3">
                    <flux:input type="text" label="Search" icon="magnifying-glass"
                                placeholder="Search events, venues..." wire:model.live.debounce.300ms="search"/>
                </div>

                <!-- Categories -->
                <div>
                    <flux:select searchable label="Categories" placeholder="Select categories..."
                                 wire:model.lazy="selectedCategories">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Venue -->
                <div>
                    <flux:select searchable label="Venue" placeholder="Select venue..." wire:model.lazy="selectedVenue">
                        <option value="">All Venues</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Date Filter -->
                <div>
                    <flux:select label="Date" placeholder="Select date..." wire:model.lazy="dateFilter">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="tomorrow">Tomorrow</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="past">Past Events</option>
                    </flux:select>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-4">
                <flux:checkbox label="Show my events only" wire:model.lazy="myEventsOnly"/>
                <flux:button icon="x-mark" variant="primary" size="sm" wire:click="clearFilters">Clear All
                    Filters
                </flux:button>
            </div>
        </div>
    </flux:card>

    <!-- Sorting -->
    <div class="flex items-center justify-between my-10">
        <div class="flex items-center space-x-4">
            <flux:text>Sort by:</flux:text>

            <flux:button.group>
                <flux:button
                    icon="calendar"
                    variant="filled"
                    size="sm"
                    wire:click="sortByDate"
                >
                    Date
                    @if($sortBy === 'start_date')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </flux:button>
                <flux:button
                    icon="pencil-square"
                    variant="filled"
                    size="sm"
                    wire:click="sortByTitle"
                >
                    Title
                    @if($sortBy === 'title')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </flux:button>
                <flux:button
                    icon="map-pin"
                    variant="filled"
                    size="sm"
                    wire:click="sortByVenue"
                >
                    Venue
                    @if($sortBy === 'venue')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </flux:button>
            </flux:button.group>
            <flux:badge size="sm" icon="document-magnifying-glass" color="red" variant="solid">{{ $events->total() }} events</flux:badge>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        @forelse($events as $event)
            <flux:card>
                <!-- Event Image or Placeholder -->
                <div class="h-48 bg-gradient-to-r from-teal-500 to-teal-800 relative">
                    @php
                        $media = $event->getFirstMedia('event');
                    @endphp

                    @if($media)
                        <img
                            src="{{ $media->getUrl('event') }}"
                            alt="{{ $event->title->name }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-full">
                            <flux:icon.photo class="w-16 h-16"/>
                        </div>
                    @endif

                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        @if($event->start_date->isFuture())
                            <flux:badge icon="calendar-date-range" size="sm" color="teal" variant="solid">
                                Upcoming
                            </flux:badge>
                        @elseif($event->start_date->isToday())
                            <flux:badge icon="calendar" size="sm" color="amber" variant="solid">
                                Today
                            </flux:badge>
                        @else
                            <flux:badge icon="lock-closed" size="sm" color="red" variant="solid">
                                Past
                            </flux:badge>
                        @endif
                    </div>
                </div>

                <div>
                    <!-- Categories -->
                    @if($event->categories->count() > 0)
                        <div class="flex flex-wrap gap-1 my-3">
                            @foreach($event->categories->take(3) as $category)
                                <flux:badge size="sm" color="{{ $category->colour }}">{{ $category->name }}</flux:badge>
                            @endforeach
                            @if($event->categories->count() > 3)
                                <flux:badge size="sm" icon="plus">
                                    {{ $event->categories->count() - 3 ?? 0 }} more
                                </flux:badge>
                            @endif
                        </div>
                    @endif

                    <!-- Title -->
                    <div class="my-2">
                        <flux:heading size="lg">{{ $event->title->name }}</flux:heading>
                    </div>


                    <!-- Description -->
                    @if($event->description)
                        <flux:text size="sm" class="mb-3 line-clamp-3">
                            {!! Str::limit(strip_tags($event->description), 150) !!}
                        </flux:text>
                    @endif


                    <!-- Event Details -->
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <flux:icon name="calendar" class="mr-2 w-4 h-4"/>
                            {{ $event->start_date->format('M d, Y g:i A') }}
                        </div>

                        <div class="flex items-center">
                            <flux:icon.map-pin class="mr-2 w-4 h-4"/>
                            {{ $event->venue->name }}
                        </div>

                        <div class="flex items-center">
                            <flux:icon.user class="mr-2 w-4 h-4"/>
                            {{ $event->user->name }}
                        </div>

                        <div class="flex items-center">
                            <flux:icon.square-3-stack-3d class="mr-2 w-4 h-4"/>
                            {{ $event->eventSessions->count() }}
                            session{{ $event->eventSessions->count() !== 1 ? 's' : '' }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between my-3">
                        <div class="flex space-x-2">
                            <flux:button icon="eye" variant="primary" size="sm" href="{{ route('events.create', $event) }}">Show
                            </flux:button>

                            @can('event-edit')
                                <flux:button icon="pencil-square" variant="danger" size="sm"
                                             href="{{ route('events.create', $event) }}">Edit
                                </flux:button>
                            @endcan
                        </div>

                        @if($event->rsvp_closes_at->isFuture())
                            <flux:badge icon="arrow-right-end-on-rectangle" size="sm" color="blue" variant="solid">RSVP Open</flux:badge>
                        @else
                            <flux:badge icon="arrow-left-start-on-rectangle" size="sm" color="red" variant="solid">RSVP Closed</flux:badge>
                        @endif
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <flux:icon name="calendar" size="xl" class="mb-4 w-16 h-16 mx-auto text-zinc-400"/>
                    <flux:heading size="lg">No events found</flux:heading>
                    <flux:text>
                        @if($search || !empty($selectedCategories) || $selectedVenue || $dateFilter || $myEventsOnly)
                            Try adjusting your filters to see more events.
                        @else
                            Get started by creating your first event.
                        @endif
                    </flux:text>
                    <div class="mt-6">
                        @if($search || !empty($selectedCategories) || $selectedVenue || $dateFilter || $myEventsOnly)
                            <flux:button variant="primary" wire:click="clearFilters">Clear Filters</flux:button>
                        @else
                            <flux:button href="{{ route('events.create') }}" variant="primary" wire:click="createEvent">
                                Create Event
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <!-- $orders = Order::paginate() -->
    <flux:pagination :paginator="$events"/>
</div>
