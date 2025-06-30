<?php

use App\Models\Event;
use App\Models\Title;
use App\Models\User;
use App\Models\Venue;
use App\Models\Category;
use App\Models\EventSession;
use App\Models\FoodAllergy;
use App\Models\DrinkPreference;
use App\Notifications\EventCreatedNotification;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibraryPro\Livewire\Concerns\WithMedia;
use Flux\Flux;

new class extends Component {
    use WithMedia;

    // Event fields
    #[Validate]
    public string $type = '';

    #[Validate]
    public string $venue = '';

    #[Validate]
    public array $categories = [];

    #[Validate]
    public string $description = '';

    #[Validate]
    public string $event_start_date = '';

    #[Validate]
    public string $event_end_date = '';

    #[Validate]
    public string $close_rsvp_at = '';

    // Collections
    #[Validate('required')]
    public Collection $eventTypes;

    #[Validate]
    public Collection $eventLocations;

    #[Validate]
    public Collection $eventCategories;

    #[Validate]
    public array $files = [];

    // Sessions array - properly initialized
    public array $sessions = [];

    // Add session count property if needed
    public int $sessionCount = 0;


    public function mount(): void
    {
        $this->eventTypes = Title::orderBy('name')->get();
        $this->eventLocations = Venue::orderBy('name')->get();
        $this->eventCategories = Category::orderBy('name')->get();

        $this->addSession();
    }


    /**
     * Adds a session to the view
     */
    public function addSession(): void
    {
        $this->clearValidation();
        $this->sessions[] = $this->createEmptySession();
        $this->updateSessionCount();
        $this->dispatch('serial-added', ['index' => count($this->sessions) - 1]);
    }

    /**
     * Create an empty session from the mount function
     */
    private function createEmptySession(): array
    {
        return [
            'name' => '',
            'description' => '',
            'location' => '',
            'start_date' => '',
            'start_time' => '',
            'end_time' => '',
            'allow_guests' => false,
        ];
    }

    /**
     * Removes a session by its index if more than one session exists.
     *
     * @param int $index The index of the session to be removed.
     */
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
        $this->sessionCount = count($this->sessions);

    }

    public function rules(): array
    {
        return [
            'type' => 'required',
            'venue' => 'required',
            'categories' => 'required',
            'description' => 'required',
            'event_start_date' => [
                'required',
                Rule::date()->after(today()->addDays(7)),
            ],
            'event_end_date' => 'required|after_or_equal:event_start_date',
            'close_rsvp_at' => 'required|before:event_start_date',

            // Sessions validation
            'sessions.*.name' => 'required|string|max:255',
            'sessions.*.location' => 'required|string|max:255',
            'sessions.*.description' => 'required|string',
            'sessions.*.start_date' => 'required|date',
            'sessions.*.start_time' => 'required',
            'sessions.*.end_time' => 'required',
        ];
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () {
            $event = $this->createEvent();
            $this->attachCategories($event);
            $this->createEventSessions($event);
            $this->handleFileUploads($event);
            $this->sendNotificationEmail($event);
        });

        $this->showSuccessMessage();
        $this->reset();
        $this->addSession();
        /*$this->redirect(route('events.index'));*/
    }

    protected function createEvent(): Event
    {
        return Event::create([
            'user_id' => auth()->id(),
            'title_id' => $this->type,
            'venue_id' => $this->venue,
            'description' => $this->description,
            'start_date' => $this->event_start_date,
            'end_date' => $this->event_end_date,
            'rsvp_closes_at' => $this->close_rsvp_at,
        ]);
    }


    private function attachCategories(Event $event): void
    {
        $event->categories()->attach($this->categories);
    }

    protected function createEventSessions(Event $event): void
    {
        foreach ($this->sessions as $sessionData) {
            $event->eventSessions()->create([
                'name' => $sessionData['name'],
                'description' => $sessionData['description'],
                'location' => $sessionData['location'],
                'start_date' => $sessionData['start_date'],
                'start_time' => $sessionData['start_time'],
                'end_time' => $sessionData['end_time'],
                'allow_guests' => $sessionData['allow_guests'] ?? false,
            ]);
        }
    }

    protected function handleFileUploads(Event $event): void
    {
        if (!empty($this->files)) {
            // Add files from Media Library Pro request
            $event->addFromMediaLibraryRequest($this->files)
                ->toMediaCollection('event');
        }

    }

    private function sendNotificationEmail(Event $event): void
    {

        // Eager load relationships before sending notification
        $event->load(['title', 'venue']);

        // Get all the subscribed users
        $subscribedUsers = User::where('is_subscribed', true)
            ->where('is_approved', true)
            ->where('is_blocked', false)
            ->get();

        // Send a notification to each of the subscribed users
        Notification::send(
            $subscribedUsers,
            new EventCreatedNotification($event)
        );

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
<div
    class="flex flex-col translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750">

    {{-- Form start here --}}
    <form wire:submit.prevent="save">
        {{-- Event --}}
        <flux:card>
            <flux:heading size="xl" class="mb-6">Create New Community Event</flux:heading>
            <div class="mb-6">
                <flux:heading class="text-sm md:text-lg">Event Details</flux:heading>
                <flux:text variant="subtle" class="text-xs md:text-md">
                    Inform your potential attendees what this event is about; you can add more detail in the next
                    section.
                </flux:text>
            </div>

            <flux:separator variant="subtle" class="mb-6"/>

            <flux:fieldset>
                <div class="grid 2xl:grid-cols-3 gap-x-4">
                    <div>
                        <flux:select badge="required" required variant="listbox" searchable placeholder="Choose type..."
                                     label="Event type"
                                     wire:model.lazy="type">
                            @foreach ($eventTypes ?? [] as $type)
                                <flux:select.option value="{{ $type->id }}">{{ $type->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div>
                        <flux:select badge="required" required variant="listbox" searchable
                                     placeholder="Choose venue..." label="Event venue"
                                     wire:model.lazy="venue">
                            @foreach ($eventLocations ?? [] as $location)
                                <flux:select.option
                                    value="{{ $location->id }}">{{ $location->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div>
                        <flux:select badge="required" required variant="listbox" multiple searchable
                                     placeholder="Choose categories..."
                                     label="Event categories"
                                     wire:model.lazy="categories">
                            @foreach ($eventCategories ?? [] as $category)
                                <flux:select.option
                                    value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <div class="grid grid-cols-1 mt-6">
                    <flux:editor badge="required" required wire:model.lazy="description" label="Event description"
                                 class="**:data-[slot=content]:min-h-[100px]!"/>
                </div>

                <div class="grid 2xl:grid-cols-3 gap-4 space-y-6 mt-6">
                    <div>
                        <flux:date-picker badge="required" required wire:model.lazy="event_start_date"
                                          label="Start date"
                                          class="**:data-[slot=content]"/>
                    </div>

                    <div>
                        <flux:date-picker badge="required" required wire:model.lazy="event_end_date" label="End date"
                                          class="**:data-[slot=content]"/>
                    </div>

                    <div>
                        <flux:date-picker badge="required" required wire:model.lazy="close_rsvp_at"
                                          label="Close RSVP at"
                                          class="**:data-[slot=content]"/>
                    </div>

                </div>

                <div class="grid grid-cols-1 col-span-full">
                    <!-- File Upload Section -->
                    <flux:fieldset>
                        <flux:label badge="optional" class="mb-2!">Event Documents</flux:label>
                        <livewire:media-library
                            wire:model="files"
                            multiple
                            max-size="10240"
                            max-items="20"
                        />
                        <flux:description class="mt-2!">
                            Upload documents and images for your event.
                            Supported formats: JPG, PNG, DOC, DOCX, PPT, PPTX, XLS, XLSX, PDF (Max: 10MB per file)
                        </flux:description>
                        <flux:error name="files"/>
                    </flux:fieldset>
                </div>
            </flux:fieldset>

            <div class="flex flex-col max-h-screen overflow-hidden mt-6">
                <flux:heading class="text-sm md:text-lg">Event Sessions</flux:heading>
                <flux:text variant="subtle" class="text-xs md:text-md">
                    Add your sessions (sub events) to this event. You must have at least one and a
                    maximum
                    of 40 sessions.
                </flux:text>
                <flux:separator variant="subtle" class="my-6"/>

                <div class="flex justify-between items-center">
                    <flux:text size="sm">Sessions ({{ count($sessions) }}/40)</flux:text>
                    <flux:button icon="calendar-days" wire:click="addSession" variant="primary"
                                 :disabled="count($sessions) >= 40">
                        Add Session
                    </flux:button>
                </div>
            </div>
        </flux:card>

        {{-- Sessions --}}
        <div class="flex flex-col max-h-fit overflow-hidden space-y-6 mt-6">
            <main class="flex-1 overflow-y-scroll">
                <div class="grid grid-cols-1  gap-4">
                    @foreach ($sessions as $index => $session)
                        <flux:card class="gap-4 p-4!">
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

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 space-y-6">
                                <div>
                                    <flux:input badge="required" required label="Title"
                                                wire:model.lazy="sessions.{{ $index }}.name"
                                                class="**:data-[slot=content]"/>
                                </div>

                                <div>
                                    <flux:input badge="required" required label="Location"
                                                wire:model.lazy="sessions.{{ $index }}.location"
                                                class="**:data-[slot=content]"/>
                                </div>

                                <div>
                                    <flux:input badge="optional" label="Capacity"
                                                wire:model.lazy="sessions.{{ $index }}.capacity"
                                                class="**:data-[slot=content]"/>
                                </div>

                                <div class="grid col-span-full">
                                    <flux:textarea badge="required" rows="3" required label="Description"
                                                   wire:model.lazy="sessions.{{ $index }}.description"
                                                   class="**:data-[slot=content]"/>
                                </div>

                                <div>
                                    <flux:date-picker badge="required" required label="Start date"
                                                      wire:model.lazy="sessions.{{ $index }}.start_date"
                                                      class="**:data-[slot=content]"/>
                                </div>

                                <div>
                                    <flux:input badge="required" required type="time" label="Start time"
                                                wire:model.lazy="sessions.{{ $index }}.start_time"
                                                class="**:data-[slot=content]"/>
                                </div>

                                <div>
                                    <flux:input badge="required" required type="time" label="End time"
                                                wire:model.lazy="sessions.{{ $index }}.end_time"
                                                class="**:data-[slot=content]"/>
                                </div>



                                <div>
                                    <flux:checkbox label="Allow guests"
                                                   wire:model.lazy="sessions.{{ $index }}.allow_guests"/>
                                </div>
                            </div>
                        </flux:card>
                    @endforeach

                </div>
            </main>
            <footer class="w-full">
                {{-- Buttons --}}
                <flux:card>
                    <div class="flex justify-end mt-6 gap-4">
                        <flux:button type="button" variant="filled" href="{{ route('events.index') }}">Back
                        </flux:button>
                        <flux:button type="submit" variant="primary">Save</flux:button>
                        <flux:button icon="calendar-days" wire:click="addSession" variant="primary"
                                     :disabled="count($sessions) >= 40">
                            Add Session
                        </flux:button>
                    </div>
                </flux:card>

            </footer>
        </div>

    </form>
</div>


