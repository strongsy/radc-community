<?php

use App\Models\Event;
use App\Models\Title;
use App\Models\Venue;
use App\Models\Category;
use App\Models\EventSession;
use App\Models\FoodAllergy;
use App\Models\DrinkPreference;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibraryPro\Livewire\Concerns\WithMedia;
use Flux\Flux;

new class extends Component {
    use WithMedia;

    // Modal states
    public bool $showTitleModal = false;
    public bool $showVenueModal = false;
    public bool $showCategoryModal = false;

    // Event properties
    public string $title = '';
    public string $description = '';
    public string $venue = '';
    public string $start_date = '';
    public string $end_date = '';
    public string $close_rsvp_date = '';
    public array $eventFiles = [];
    public array $sessions = [];
    public array $sessionCount = [];

    // Data collections
    public Collection $titles;
    public Collection $venues;
    public Collection $categories;

    public function mount(): void
    {
        $this->initializeData();
        $this->addSession();
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'description' => 'required',
            'venue' => 'required',
            'categories' => 'required|array|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'close_rsvp_date' => 'required|date|before:start_date',
            'sessions.*.event_session_title_id' => 'required',
            'sessions.*.categories' => 'required',
            'sessions.*.description' => 'required',
            'sessions.*.event_session_venue_id' => 'required',
            'sessions.*.start_date' => 'required|after_or_equal:start_date',
            'sessions.*.start_time' => 'required',
            'sessions.*.end_time' => 'required|after:sessions.*.start_time',
        ];
    }

    private function initializeData(): void
    {
        $this->venues = $this->loadVenues();
        $this->titles = $this->loadTitles();
        $this->categories = $this->loadCategories();
    }

    private function loadVenues(): Collection
    {
        return Venue::orderBy('city')->get() ?? collect();
    }

    private function loadTitles(): Collection
    {
        return Title::orderBy('name')->get() ?? collect();
    }

    private function loadCategories(): Collection
    {
        return Category::orderBy('name')->get() ?? collect();
        /*return Category::all()
            ->map(fn($category) => [
                'id' => $category->id,
                'name' => $category->name
            ])
            ->toArray();*/
    }

    public function addSession(): void
    {
        $this->clearValidation();
        $this->sessions[] = $this->createEmptySession();
        $this->updateSessionCount();
        $this->dispatch('serial-added', ['index' => count($this->sessions) - 1]);
    }

    private function createEmptySession(): array
    {
        return [
            'event_session_title_id' => '',
            'description' => '',
            'event_session_venue_id' => '',
            'start_date' => '',
            'start_time' => '',
            'end_time' => '',
            'allow_guests' => false,
        ];
    }

    public function removeSession(int $index): void
    {
        if (count($this->sessions) > 1) {
            $this->clearValidation();
            unset($this->sessions[$index]);
            $this->sessions = array_values($this->sessions);
            $this->updateSessionCount();
        }
    }

    private function updateSessionCount(): void
    {
        $this->sessionCount = $this->sessions;
    }

    // Modal management methods
    public function toggleModal(string $modalName, bool $state = null): void
    {
        $property = "show{$modalName}Modal";
        if (property_exists($this, $property)) {
            $this->$property = $state ?? !$this->$property;
        }
    }

    public function openTitleModal(): void
    {
        $this->toggleModal('Title', true);
    }

    public function closeTitleModal(): void
    {
        $this->toggleModal('Title', false);
    }

    public function openVenueModal(): void
    {
        $this->toggleModal('Venue', true);
    }

    public function closeVenueModal(): void
    {
        $this->toggleModal('Venue', false);
    }

    public function openCategoryModal(): void
    {
        $this->toggleModal('Category', true);
    }

    public function closeCategoryModal(): void
    {
        $this->toggleModal('Category', false);
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () {
            $event = $this->createEvent();
            $this->attachCategories($event);
            $this->createEventSessions($event);
            $this->handleFileUploads($event);
        });

        $this->showSuccessMessage();
        $this->redirect(route('events.index'));
    }

    private function createEvent(): Event
    {
        return Event::create([
            'user_id' => auth()->id(),
            'title_id' => $this->title,
            'venue_id' => $this->venue,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'rsvp_closes_at' => $this->close_rsvp_date,
        ]);
    }

    private function attachCategories(Event $event): void
    {
        $event->categories()->attach($this->categories);
    }

    private function createEventSessions(Event $event): void
    {
        foreach ($this->sessions as $sessionData) {
            $session = $event->eventSessions()->create([
                'event_session_title_id' => $sessionData['event_session_title_id'],
                'description' => $sessionData['description'],
                'event_session_venue_id' => $sessionData['event_session_venue_id'],
                'start_date' => $sessionData['start_date'],
                'start_time' => $sessionData['start_time'],
                'end_time' => $sessionData['end_time'],
                'allow_guests' => $sessionData['allow_guests'] ?? false,
            ]);

            if (isset($sessionData['categories']) && is_array($sessionData['categories'])) {
                $session->categories()->attach($sessionData['categories']);
            }
        }
    }

    private function handleFileUploads(Event $event): void
    {
        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $event->addMedia($file->getRealPath())
                    ->usingName($file->getClientOriginalName())
                    ->toMediaCollection('event_files');
            }
        }
    }

    private function showSuccessMessage(): void
    {
        Flux::toast(
            text: 'Event saved successfully',
            heading: 'Event Saved',
            variant: 'success',
        );
    }
};
?>


{{-- Component layout --}}
<div>
    <form wire:submit.prevent="save">
        <div class="grid gap-6">

            {{-- Heading card --}}
            <flux:card>
                <flux:heading class="text-sm md:text-lg">
                    Create New Community Event
                </flux:heading>
                <flux:text variant="subtle" class="text-xs md:text-md">
                    Fill out the information below to create your community event.
                </flux:text>
            </flux:card>
            {{-- Main event card --}}
            <flux:card
                class="translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750">
                <div class="mb-6">
                    <flux:heading class="text-sm md:text-lg">Event Details</flux:heading>
                    <flux:text variant="subtle" class="text-xs md:text-md">
                        Inform your potential attendees what this event is about; you can add more detail in the next
                        section.
                    </flux:text>
                </div>


                {{-- Input fields --}}
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <flux:select wire:model.lazy="title" placeholder="Choose name..." label="Event Name"
                                     badge="required">
                            @foreach($this->titles as $title)
                                <option value="{{ $title->id }}">{{ $title->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div>
                        <flux:select wire:model.lazy="venue" placeholder="Choose venue..." label="Event Venue"
                                     badge="required">
                            @foreach($this->venues as $venue)
                                <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div>
                        {{-- Fixed: Use object properties instead of array access --}}
                        <flux:select wire:model.lazy="categories" variant="listbox" multiple
                                     placeholder="Choose categories..."
                                     label="Event Categories" badge="required">
                            @foreach($this->categories as $category)
                                <flux:select.option
                                    value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <div class="grid gap-6 mt-6">
                    <div>
                        <flux:editor wire:model.lazy="description" label="Event Description" badge="required"
                                     toolbar="heading | bold italic underline | align"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div>
                        <flux:date-picker wire:model.lazy="start_date" label="Start Date" badge="required"/>
                    </div>

                    <div>
                        <flux:date-picker wire:model.lazy="end_date" label="End Date" badge="required"/>
                    </div>

                    <div>
                        <flux:date-picker wire:model.lazy="close_rsvp_date" label="Close RSVP Date" badge="required"/>
                    </div>

                    <div class="grid grid-cols-1 col-span-full gap-4 mt-6">
                        <!-- File Upload Section -->
                        <flux:fieldset>
                            <flux:label badge="optional" class="mb-2!">Event Documents</flux:label>
                            <livewire:media-library
                                wire:model="eventFiles"
                                multiple
                                max-items="20"
                            />
                            <flux:description class="mt-2!">
                                Upload documents and images for your event.
                                Supported formats: JPG, PNG, DOC, DOCX, PPT, PPTX, XLS, XLSX, PDF (Max: 10MB per file)
                            </flux:description>
                            <flux:error name="eventFiles"/>
                        </flux:fieldset>
                    </div>
                </div>
            </flux:card>

            {{-- Sessions card --}}
            <flux:card
                class="translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750">

                <div class="flex flex-col max-h-screen overflow-hidden">
                    <header class="w-full border-b mb-2 border-zinc-50 dark:border-zinc-600">
                        <div class="mb-6">
                            <flux:heading class="text-sm md:text-lg">Event Sessions</flux:heading>
                            <flux:text variant="subtle" class="text-xs md:text-md">
                                This is where you can create sessions (sub-events) for your main event. You can add more
                                sessions
                                later
                                by
                                clicking the "Add Session" button.
                            </flux:text>
                        </div>

                        <div class="flex justify-between items-center my-4">
                            <flux:text size="sm">Sessions ({{ count($sessions) }}/40)</flux:text>
                            <flux:button icon="calendar-days" size="xs" wire:click="addSession" variant="primary"
                                         :disabled="count($sessions) >= 40">
                                Add Session
                            </flux:button>
                        </div>
                    </header>
                    <main
                        class="flex-1 overflow-y-scroll translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750">
                        <div class="min-h-svh mt-6">
                            <div class="grid grid-cols-1 2xl:grid-cols-3 gap-4">
                                @foreach($this->sessions as $index => $session)
                                    <div class="grid grid-cols- gap-4">
                                        <div id="session-{{ $index }}"
                                             class="grid grid-cols-1 translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750">
                                            <div class="flex justify-between items-center my-6">
                                                <flux:badge size="sm" color="amber" variant="solid" size="sm">
                                                    Session {{ $index + 1 }}</flux:badge>
                                                @if(count($sessions) > 1)
                                                    <flux:button icon="trash" wire:click="removeSession({{ $index }})"
                                                                 variant="danger" size="xs">
                                                        Remove Session
                                                    </flux:button>
                                                @endif

                                            </div>

                                            <flux:card
                                                class="grid md:grid-cols-1 xl:grid-cols-2  gap-4 p-4! translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750">
                                                <div>
                                                    <flux:input size="sm"
                                                                wire:model="sessions.{{ $index }}.event_session_title_id"
                                                                placeholder="Enter session name"
                                                                label="Session Name" badge="required"/>
                                                </div>

                                                <div>
                                                    <flux:input size="sm"
                                                                wire:model="sessions.{{ $index }}.event_session_venue_id"
                                                                placeholder="Enter session location"
                                                                label="Session Location" badge="required"/>
                                                </div>

                                                <div>
                                                    <flux:select aria-live="assertive" size="sm"
                                                                 wire:model.lazy="categories" variant="listbox" multiple
                                                                 placeholder="Choose categories..."
                                                                 label="Event Categories" badge="required">
                                                        @if(is_array($categories))
                                                            @foreach($categories as $category)
                                                                <flux:select.option
                                                                    value="{{ $category['id'] }}">{{ $category['name'] }}</flux:select.option>
                                                            @endforeach
                                                        @endif
                                                    </flux:select>

                                                </div>

                                                <div>
                                                    <div>
                                                        <flux:date-picker size="sm" wire:model.lazy="start_date"
                                                                          label="Session Date" badge="required"/>
                                                    </div>
                                                </div>

                                                <div class="col-span-2">
                                                    <flux:textarea rows="3" size="sm" label="Session Description"
                                                                   wire:model="sessions.{{ $index }}.description"
                                                                   placeholder="Enter session description"
                                                                   badge="required"/>
                                                </div>

                                                <div>
                                                    <flux:input type="time" size="sm"
                                                                wire:model.lazy="sessions.{{ $index }}.start_time"
                                                                label="Session Start Time" badge="required"/>
                                                </div>

                                                <div>
                                                    <flux:input type="time" size="sm"
                                                                wire:model.lazy="sessions.{{ $index }}.end_time"
                                                                label="Session End Time" badge="required"/>
                                                </div>

                                                <div class="col-span-2">
                                                    <flux:input size="sm"
                                                                wire:model="sessions.{{ $index }}.max_attendees"
                                                                label="Max Attendees" placeholder="Enter max attendees"
                                                                badge="optional"/>
                                                </div>

                                                <div>
                                                    <flux:checkbox size="sm"
                                                                   wire:model="sessions.{{ $index }}.allow_guests"
                                                                   label="Allow Guests"/>
                                                </div>
                                            </flux:card>

                                        </div>
                                    </div>

                                @endforeach
                            </div>
                        </div>
                    </main>
                </div>

                <!-- Submit Button -->
                <footer class="flex-1 mt-2 w-full overflow-y-scroll">
                    <div class="space-x-4 py-6 border-t border-zinc-50 dark:border-zinc-600">
                        <flux:button href="{{ route('events.index') }}" variant="danger">Cancel</flux:button>
                        <flux:button type="submit" variant="primary">Save</flux:button>
                    </div>
                </footer>
            </flux:card>

        </div>
    </form>
</div>


