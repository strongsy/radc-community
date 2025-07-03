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
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\MediaLibraryPro\Livewire\Concerns\WithMedia;
use Flux\Flux;

new class extends Component {
    use WithMedia;

    // Event fields
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

    #[Validate]
    public array $files = [];

    // Sessions array - properly initialized with the default value
    public array $sessions = [];

    // Venues array for modal
    public array $saving = [];
    public array $updating = [];

    // Add session count property if needed
    public int $sessionCount = 0;

    // Venue modal
    public bool $showVenueModal = false;

    public bool $isCreating = false;

    public function mount(): void
    {
        // Initialize a session array if it's empty
        if (empty($this->sessions)) {
            $this->sessions = [];
            $this->addSession();
        }
    }

    #[Computed]
    public function eventTypes()
    {
        return Cache::remember('event_types', 3600, static function () {
            return Title::orderBy('name')->get();
        });
    }

    #[Computed]
    public function eventLocations()
    {
        return Cache::remember('event_locations', 3600, static function () {
            return Venue::orderBy('name')->get();
        });
    }

    #[Computed]
    public function eventCategories()
    {
        return Cache::remember('event_categories', 3600, static function () {
            return Category::orderBy('name')->get();
        });
    }


    public function addSession(): void
    {
        $this->clearValidation();
        $this->sessions[] = $this->createEmptySession();
        $this->updateSessionCount();
        $this->dispatch('serial-added', ['index' => count($this->sessions) - 1]);
    }

    public function removeSession(int $index): void
    {
        if (count($this->sessions) <= 1) {
            return;
        }

        $this->clearValidation();
        unset($this->sessions[$index]);
        $this->sessions = array_values($this->sessions);
        $this->updateSessionCount();
    }

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

    private function updateSessionCount(): void
    {
        $this->sessionCount = count($this->sessions);
    }

    public function rules(): array
    {
        return [
            'type' => 'required',
            'venue' => 'required',
            'categories' => 'required|array|min:1',
            'description' => 'required',
            'event_start_date' => 'required|date|after:' . Carbon::now()->addDays(7),
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'close_rsvp_at' => 'required|date|before:event_start_date',

            // Sessions validation
            'sessions' => 'required|array|min:1',
            'sessions.*.name' => 'required|string|max:255',
            'sessions.*.location' => 'required|string|max:255',
            'sessions.*.description' => 'required|string|max:1000',
            'sessions.*.start_date' => 'required|date|before_or_equal:event_end_date',
            'sessions.*.start_time' => 'required',
            'sessions.*.end_time' => 'required|after:sessions.*.start_time',
            'sessions.*.grant' => 'nullable|numeric|regex:/^\d+(\.\d{0,2})?$/',
            'sessions.*.cost' => 'nullable|numeric|regex:/^\d+(\.\d{0,2})?$/',
            'sessions.*.capacity' => 'nullable|numeric',
        ];
    }

    private function convertToFloat($value): ?float
    {
        if (empty($value)) {
            return null;
        }
        return (float)$value;
    }


    public function venueModal(): void
    {
        $this->isCreating = true;
        $this->showVenueModal = true;
    }

    public function resetVenueModal(): void
    {
        $this->clearValidation();
        $this->showVenueModal = false;
    }

    public function saveVenue(): void
    {
        $this->validate([
            'saving.name' => 'required|string|max:255',
            'saving.address' => 'required|string|max:255',
            'saving.city' => 'required|string|max:255',
            'saving.county' => 'required|string|max:255',
            'saving.post_code' => 'required|string|max:10',
        ]);

        try {
            // Auto-format the name to include the city if not already present
            $venueName = $this->saving['name'];
            $city = $this->saving['city'];

            // Check if the name already contains the city pattern
            if (!str_contains($venueName, ' - ')) {
                $venueName .= ' - ' . $city;
            }

            $venue = Venue::create([
                'name' => $venueName,
                'address' => $this->saving['address'],
                'city' => strToUpper($this->saving['city']),
                'county' => $this->saving['county'],
                'post_code' => strtoupper($this->saving['post_code']),
            ]);

            $this->venues = Venue::all();
            $this->saving['venue_id'] = $venue->id;


            // Reset and show success...
            $this->saving = [];
            $this->isCreating = false;
            $this->showVenueModal = false;
            $this->clearValidation();

            Flux::toast(
                text: 'Venue created successfully',
                heading: 'Success',
                variant: 'success',
            );

        } catch (\Exception $e) {
            Flux::toast(
                text: 'Failed to create venue: ' . $e->getMessage(),
                heading: 'Error',
                variant: 'danger',
            );
        }
    }

    public function save()
    {
        try {
            // Debug: Log the form data
            Log::info('Save method called', [
                'type' => $this->type,
                'venue' => $this->venue,
                'categories' => $this->categories,
                'sessions_count' => count($this->sessions),
                'sessions' => $this->sessions
            ]);

            // Validate sessions before proceeding
            if (empty($this->sessions)) {
                return $this->addError('sessions', 'At least one session is required.');
            }

            // Validate all fields
            $this->validate();

            Log::info('Validation passed, starting transaction');

            DB::transaction(function () {
                $event = $this->createEvent();
                Log::info('Event created', ['event_id' => $event->id]);

                $this->attachCategories($event);
                Log::info('Categories attached');

                $this->createEventSessions($event);
                Log::info('Sessions created');

                $this->handleFileUploads($event);
                Log::info('Files handled');

                $this->sendNotificationEmail($event);
                Log::info('Notifications sent');
            });

            $this->showSuccessMessage();
            $this->reset();
            $this->addSession();

            // Redirect to events index
            return redirect(route('events.index'));

        } catch (ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'data' => [
                    'type' => $this->type,
                    'venue' => $this->venue,
                    'categories' => $this->categories,
                    'sessions' => $this->sessions
                ]
            ]);
            throw $e;
        } catch (Exception $e) {
            Log::error('Failed to create event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => [
                    'type' => $this->type,
                    'venue' => $this->venue,
                    'categories' => $this->categories,
                    'sessions' => $this->sessions
                ]
            ]);

            $this->addError('general', 'Failed to create event: ' . $e->getMessage());
        }
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
        if (!empty($this->categories)) {
            $event->categories()->attach($this->categories);
        }
    }

    protected function createEventSessions(Event $event): void
    {
        foreach ($this->sessions as $sessionData) {
            if (!is_array($sessionData)) {
                Log::warning('Session data is not an array', [
                    'session_data_type' => gettype($sessionData),
                    'session_data' => $sessionData,
                    'event_id' => $event->id
                ]);
                continue;
            }

            // Helper function to convert string to float or null
            $convertToFloat = static function ($value) {
                if (empty($value)) {
                    return null;
                }
                return (float)$value;
            };

            $event->eventSessions()->create([
                'name' => $sessionData['name'] ?? '',
                'description' => $sessionData['description'] ?? '',
                'location' => $sessionData['location'] ?? '',
                'start_date' => $sessionData['start_date'] ?? null,
                'start_time' => $sessionData['start_time'] ?? null,
                'end_time' => $sessionData['end_time'] ?? null,
                'cost' => $this->convertToFloat($sessionData['cost'] ?? null),
                'grant' => $this->convertToFloat($sessionData['grant'] ?? null),
                'capacity' => $sessionData['capacity'] ?? null,
                'allow_guests' => $sessionData['allow_guests'] ?? false,
            ]);
        }
    }

    protected function handleFileUploads(Event $event): void
    {
        if (!empty($this->files)) {
            $event->addFromMediaLibraryRequest($this->files)
                ->toMediaCollection('event');
        }
    }

    private function sendNotificationEmail(Event $event): void
    {
        try {
            $event->load(['title', 'venue']);

            $subscribedUsers = User::where('is_subscribed', true)
                ->where('is_approved', true)
                ->where('is_blocked', false)
                ->whereNotNull('name')
                ->whereNotNull('email')
                ->get();

            if ($subscribedUsers->count() > 0) {
                $subscribedUsers->chunk(50)->each(function ($userChunk) use ($event) {
                    Notification::send(
                        $userChunk,
                        new EventCreatedNotification($event)
                    );
                });
            }

        } catch (Exception $e) {
            Log::error('Failed to send event notification emails', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

{{-- **********Create Event layout********** --}}
<div>
    <div
        class="flex flex-col mx-auto max-w-7xl translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750 space-y-6">

        <flux:heading size="xl" class="mb-6">Create New Community Event</flux:heading>

        <flux:card>
            <flux:heading size="xl" class="mb-6">Event Details</flux:heading>
            <form wire:submit="save">
                <div class="grid grid-cols-1 gap-4">
                    <flux:fieldset>
                        <div class="grid 2xl:grid-cols-3 gap-4">
                            <div>
                                <flux:select badge="required" required variant="listbox" searchable
                                             placeholder="Choose type..."
                                             label="Event type"
                                             wire:model.lazy="type">
                                    @forelse($this->eventTypes as $type)
                                        <flux:select.option value="{{ $type->id }}">{{ $type->name }}</flux:select.option>
                                    @empty
                                        <flux:text>No types found</flux:text>
                                    @endforelse
                                </flux:select>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label badge="required">Event Venue</flux:label>
                                    <flux:input.group>
                                        <flux:button icon="plus" wire:click="venueModal"/>
                                        <flux:select badge="required" required variant="listbox" searchable
                                                     placeholder="Choose venue..."
                                                     wire:model.live="venue">
                                            @forelse($this->eventLocations as $location)
                                                <flux:select.option value="{{ $location->id }}">{{ $location->name }}</flux:select.option>
                                            @empty
                                                <flux:text>No locations found</flux:text>
                                            @endforelse
                                        </flux:select>
                                    </flux:input.group>
                                    <flux:error name="venue"/>
                                </flux:field>
                            </div>

                            <div>
                                <flux:select badge="required" required variant="listbox" multiple searchable
                                             placeholder="Choose categories..."
                                             label="Event categories"
                                             wire:model.lazy="categories">
                                    @forelse($this->eventCategories as $category)
                                        <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                                    @empty
                                        <flux:text>No categories found</flux:text>
                                    @endforelse
                                </flux:select>
                            </div>

                            <div>
                                <flux:date-picker badge="required" required wire:model.lazy="event_start_date"
                                                  label="Start date"/>
                            </div>

                            <div>
                                <flux:date-picker badge="required" required wire:model.lazy="event_end_date"
                                                  label="End date"/>
                            </div>

                            <div>
                                <flux:date-picker badge="required" required wire:model.lazy="close_rsvp_at"
                                                  label="Close RSVP at"/>
                            </div>

                            <div class="grid grid-cols-1 col-span-full">
                                <flux:editor badge="required" required wire:model.lazy="description"
                                toolbar="heading | bold italic underline"
                                             label="Event description"
                                             class="**:data-[slot=content]:min-h-[100px]!"/>
                            </div>

                            <div class="grid grid-cols-1 col-span-full">
                                <flux:fieldset>
                                    <flux:label badge="optional" class="mb-2!">Event Documents</flux:label>
                                    <livewire:media-library
                                        wire:model.lazy="files"
                                        multiple
                                        max-size="10240"
                                        max-items="20"
                                    />
                                    <flux:description class="mt-2!">
                                        Upload documents and images for your event.
                                        Supported formats: JPG, PNG, DOC, DOCX, PPT, PPTX, XLS, XLSX, PDF (Max: 10MB per
                                        file)
                                    </flux:description>
                                </flux:fieldset>
                            </div>
                        </div>
                    </flux:fieldset>
                </div>

                <div class="grid grid-cols-1 col-span-full my-6">
                    <flux:separator class="h-2"/>
                </div>

                {{-- Event Sessions --}}
                <div>
                    <div class="grid grid-cols-1">
                        <flux:heading size="xl">Event Sessions</flux:heading>
                        <flux:text variant="subtle" class="text-xs md:text-md">
                            Add your sessions (sub events) to this event. You must have at least one and a
                            maximum of 40 sessions.
                        </flux:text>

                        <div class="flex justify-between items-center space-y-2 my-4">
                            <flux:text size="sm">Sessions ({{ is_array($sessions) ? count($sessions) : 0 }}/40)
                            </flux:text>
                            <flux:button icon="calendar-days" wire:click="addSession" variant="primary"
                                         :disabled="is_array($sessions) && count($sessions) >= 40">
                                Add Session
                            </flux:button>
                        </div>
                        <flux:error name="sessions"/>
                    </div>
                </div>


                <div class="grid grid-cols-1 gap-4">
                    @if(is_array($sessions))
                        @forelse ($sessions as $index => $session)
                            <flux:card>
                                <div class="flex justify-between items-center mb-4">
                                    <flux:badge size="sm" color="amber" variant="solid">
                                        Session {{ $index + 1 }}
                                    </flux:badge>
                                    @if(count($sessions) > 1)
                                        <flux:button icon="trash" wire:click="removeSession({{ $index }})"
                                                     variant="danger" size="xs">
                                            Remove Session
                                        </flux:button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 space-y-4">
                                    <div>
                                        <flux:input badge="required" required label="Title"
                                                    wire:model.lazy="sessions.{{ $index }}.name"/>
                                    </div>

                                    <div>
                                        <flux:input badge="required" required label="Location"
                                                    wire:model.lazy="sessions.{{ $index }}.location"/>
                                    </div>

                                    <div>
                                        <flux:input badge="optional" label="Capacity"
                                                    wire:model.lazy="sessions.{{ $index }}.capacity"/>
                                    </div>

                                    <div class="grid col-span-full">
                                        <flux:textarea badge="required" rows="3" required label="Description"
                                                       wire:model.lazy="sessions.{{ $index }}.description"/>
                                    </div>

                                    <div>
                                        <flux:date-picker badge="required" required label="Start date"
                                                          wire:model.lazy="sessions.{{ $index }}.start_date"/>
                                    </div>

                                    <div>
                                        <flux:input badge="required" required type="time" label="Start time"
                                                    wire:model.lazy="sessions.{{ $index }}.start_time"/>
                                    </div>

                                    <div>
                                        <flux:input badge="required" required type="time" label="End time"
                                                    wire:model.lazy="sessions.{{ $index }}.end_time"/>
                                    </div>

                                    <div>
                                        <flux:field>
                                            <flux:label badge="optional">Grant</flux:label>
                                            <flux:input.group>
                                                <flux:input.group.prefix>£</flux:input.group.prefix>
                                                <flux:input badge="optional" placeholder="e.g. £10.00"
                                                            wire:model.lazy="sessions.{{ $index }}.grant"/>
                                            </flux:input.group>
                                            <flux:error name="grant"/>
                                        </flux:field>
                                    </div>

                                    <div>
                                        <flux:field>
                                            <flux:label badge="optional">Cost</flux:label>
                                            <flux:input.group>
                                                <flux:input.group.prefix>£</flux:input.group.prefix>
                                                <flux:input badge="optional" placeholder="e.g. £10.00"
                                                            wire:model.lazy="sessions.{{ $index }}.cost"/>
                                            </flux:input.group>
                                            <flux:error name="cost"/>
                                        </flux:field>
                                    </div>

                                    <div class="md:mt-10">
                                        <flux:checkbox label="Allow guests"
                                                       wire:model.lazy="sessions.{{ $index }}.allow_guests"/>

                                    </div>

                                </div>
                            </flux:card>
                        @empty
                            <flux:text>No sessions found</flux:text>
                        @endforelse
                    @endif
                </div>


                <div class="flex justify-end mt-6 gap-4">
                    <flux:button type="button" variant="filled" href="{{ route('events.index') }}">Back</flux:button>
                    <flux:button type="submit" variant="primary">Save</flux:button>
                </div>
            </form>
        </flux:card>
    </div>

    <!-- venue modal -->
    <form wire:submit.prevent="saveVenue">
        <flux:modal wire:model="showVenueModal"
                    size="lg" class="max-w-sm w-auto">
            <flux:heading class="mb-6">{{ $isCreating ? 'Create' : 'Edit' }} Venue</flux:heading>
            <div class="flex flex-col gap-4">
                <flux:input required badge="required" wire:model="saving.name" label="Name" placeholder="Name"
                            type="text"
                            class="w-full"/>
                <flux:input required badge="required" wire:model="saving.address" label="Address" placeholder="Address"
                            type="text"
                            class="w-full"/>
                <flux:input required badge="required" wire:model="saving.city" label="City" placeholder="City"
                            type="text"
                            class="w-full"/>
                <flux:select required badge="required" variant="listbox" searchable wire:model="saving.county"
                             label="County" placeholder="County"
                             type="text"
                             class="w-full">
                    @foreach(Venue::venueCounties() as $county)
                        <flux:select.option value="{{ $county }}">{{ $county }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input required badge="required" wire:model="saving.post_code" label="Post Code"
                            placeholder="Post Code" type="text"
                            class="w-full"/>
            </div>
            <div class="flex w-full items-end justify-end gap-4 mt-4">
                <flux:button type="button" variant="primary" wire:click="resetVenueModal()"

                >Close
                </flux:button>
                <flux:button type="submit" variant="danger" class="disabled:cursor-not-allowed disabled:opacity-75">
                    Save
                </flux:button>
            </div>

        </flux:modal>
    </form>
</div>
