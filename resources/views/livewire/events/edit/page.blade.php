
<?php

use App\Models\Event;
use App\Models\Title;
use App\Models\User;
use App\Models\Venue;
use App\Models\Category;
use App\Models\EventSession;
use App\Notifications\EventUpdatedNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\MediaLibraryPro\Livewire\Concerns\WithMedia;
use Flux\Flux;

new class extends Component {
    use WithMedia;

    public Event $event;

    // Event fields
    public string $type = '';
    public string $venue = '';
    public array $categories = [];
    public string $description = '';
    public string $event_start_date = '';
    public string $event_end_date = '';
    public string $close_rsvp_at = '';

    // Collections
    public Collection $eventTypes;
    public Collection $eventLocations;
    public Collection $eventCategories;
    public array $files = [];

    // Sessions array
    public array $sessions = [];
    public int $sessionCount = 0;

    // Track sessions to delete
    public array $sessionsToDelete = [];

    public function mount(int $id): void
    {
        $this->event = Event::with(['title', 'venue', 'categories', 'eventSessions', 'media'])->findOrFail($id);

        // Check if the user can edit this event
        if (!auth()->user()->can('event-update') && $this->event->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to edit this event.');
        }

        $this->loadCollections();
        $this->populateFormData();
    }

    private function loadCollections(): void
    {
        $this->eventTypes = Title::orderBy('name')->get() ?: collect();
        $this->eventLocations = Venue::orderBy('name')->get() ?: collect();
        $this->eventCategories = Category::orderBy('name')->get() ?: collect();
    }

    private function populateFormData(): void
    {
        $this->type = (string) $this->event->title_id;
        $this->venue = (string) $this->event->venue_id;
        $this->categories = $this->event->categories->pluck('id')->toArray();
        $this->description = $this->event->description ?? '';
        $this->event_start_date = $this->event->start_date?->format('Y-m-d') ?? '';
        $this->event_end_date = $this->event->end_date?->format('Y-m-d') ?? '';
        $this->close_rsvp_at = $this->event->rsvp_closes_at?->format('Y-m-d') ?? '';

        // Load existing sessions with properly formatted times
        $this->sessions = $this->event->eventSessions->map(function ($session) {
            return [
                'id' => $session->id,
                'name' => $session->name ?? '',
                'description' => $session->description ?? '',
                'location' => $session->location ?? '',
                'start_date' => $session->start_date ? Carbon::parse($session->start_date)->format('Y-m-d') : '',
                'start_time' => $session->start_time ? Carbon::parse($session->start_time)->format('H:i') : '',
                'end_time' => $session->end_time ? Carbon::parse($session->end_time)->format('H:i') : '',
                'allow_guests' => $session->allow_guests ?? false,
            ];
        })->toArray();

        // Ensure at least one session exists
        if (empty($this->sessions)) {
            $this->addSession();
        }

        $this->updateSessionCount();

        // Initialize media library with existing files
        $this->files = $this->event->getMedia('event')->toArray();
    }

    public function addSession(): void
    {
        $this->clearValidation();
        $this->sessions[] = $this->createEmptySession();
        $this->updateSessionCount();
        $this->dispatch('session-added', ['index' => count($this->sessions) - 1]);
    }

    public function removeSession(int $index): void
    {
        if (count($this->sessions) <= 1) {
            return;
        }

        $this->clearValidation();

        // If a session has an ID, mark it for deletion
        if (isset($this->sessions[$index]['id'])) {
            $this->sessionsToDelete[] = $this->sessions[$index]['id'];
        }

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
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'close_rsvp_at' => 'required|date|before:event_start_date',

            // Sessions validation
            'sessions' => 'required|array|min:1',
            'sessions.*.name' => 'required|string|max:255',
            'sessions.*.location' => 'required|string|max:255',
            'sessions.*.description' => 'required|string',
            'sessions.*.start_date' => 'required|date|before_or_equal:event_end_date',
            'sessions.*.start_time' => 'required',
            'sessions.*.end_time' => 'required|after:sessions.*.start_time',
        ];
    }

    public function update(): Redirector
    {
        try {
            Log::info('Update method called', [
                'event_id' => $this->event->id,
                'type' => $this->type,
                'venue' => $this->venue,
                'categories' => $this->categories,
                'sessions_count' => count($this->sessions),
            ]);

            // Validate sessions before proceeding
            if (empty($this->sessions)) {
                $this->addError('sessions', 'At least one session is required.');
                return redirect()->back();
            }

            // Validate all fields
            $this->validate();

            Log::info('Validation passed, starting transaction');

            DB::transaction(function () {
                $this->updateEvent();
                Log::info('Event updated');

                $this->updateCategories();
                Log::info('Categories updated');

                $this->updateEventSessions();
                Log::info('Sessions updated');

                $this->handleFileUploads();
                Log::info('Files handled');

                $this->sendUpdateNotification();
                Log::info('Update notifications sent');
            });

            $this->showSuccessMessage();

            return redirect(route('events.index'));

        } catch (ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'event_id' => $this->event->id,
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_id' => $this->event->id,
            ]);

            $this->addError('general', 'Failed to update event: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    protected function updateEvent(): void
    {
        $this->event->update([
            'title_id' => $this->type,
            'venue_id' => $this->venue,
            'description' => $this->description,
            'start_date' => $this->event_start_date,
            'end_date' => $this->event_end_date,
            'rsvp_closes_at' => $this->close_rsvp_at,
        ]);
    }

    private function updateCategories(): void
    {
        if (!empty($this->categories)) {
            $this->event->categories()->sync($this->categories);
        }
    }

    protected function updateEventSessions(): void
    {
        // Delete removed sessions
        if (!empty($this->sessionsToDelete)) {
            EventSession::whereIn('id', $this->sessionsToDelete)->delete();
        }

        foreach ($this->sessions as $sessionData) {
            if (!is_array($sessionData)) {
                Log::warning('Session data is not an array', [
                    'session_data_type' => gettype($sessionData),
                    'session_data' => $sessionData,
                    'event_id' => $this->event->id
                ]);
                continue;
            }

            if (isset($sessionData['id']) && $sessionData['id']) {
                // Update existing session
                EventSession::where('id', $sessionData['id'])->update([
                    'name' => $sessionData['name'] ?? '',
                    'description' => $sessionData['description'] ?? '',
                    'location' => $sessionData['location'] ?? '',
                    'start_date' => $sessionData['start_date'] ?? null,
                    'start_time' => $sessionData['start_time'] ?? null,
                    'end_time' => $sessionData['end_time'] ?? null,
                    'allow_guests' => $sessionData['allow_guests'] ?? false,
                ]);
            } else {
                // Create a new session
                $this->event->eventSessions()->create([
                    'name' => $sessionData['name'] ?? '',
                    'description' => $sessionData['description'] ?? '',
                    'location' => $sessionData['location'] ?? '',
                    'start_date' => $sessionData['start_date'] ?? null,
                    'start_time' => $sessionData['start_time'] ?? null,
                    'end_time' => $sessionData['end_time'] ?? null,
                    'allow_guests' => $sessionData['allow_guests'] ?? false,
                ]);
            }
        }
    }

    protected function handleFileUploads(): void
    {
        if (!empty($this->files)) {
            $this->event->addFromMediaLibraryRequest($this->files)
                ->toMediaCollection('event');
        }
    }

    private function sendUpdateNotification(): void
    {
        try {
            $this->event->load(['title', 'venue']);

            $subscribedUsers = User::where('is_subscribed', true)
                ->where('is_approved', true)
                ->where('is_blocked', false)
                ->whereNotNull('name')
                ->whereNotNull('email')
                ->where('id', '!=', auth()->id()) // Don't notify the person who updated
                ->get();

            if ($subscribedUsers->count() > 0) {
                $subscribedUsers->chunk(50)->each(function ($userChunk) {
                    Notification::send(
                        $userChunk,
                        new EventUpdatedNotification($this->event)
                    );
                });
            }

        } catch (\Exception $e) {
            Log::error('Failed to send event update notification emails', [
                'event_id' => $this->event->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function showSuccessMessage(): void
    {
        Flux::toast(
            text: 'Event updated successfully',
            heading: 'Event Updated',
            variant: 'success',
        );
    }
};
?>

<div>
    <div class="flex flex-col translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750 space-y-6">

        <flux:heading size="xl" class="mb-6">Edit Event: {{ $event->title->name }}</flux:heading>

        <!-- Display general errors -->
        @error('general')
        <flux:card variant="danger">
            <flux:text>{{ $message }}</flux:text>
        </flux:card>
        @enderror

        <flux:card>
            <flux:heading size="xl" class="mb-6">Event Details</flux:heading>
            <form wire:submit="update">
                <flux:fieldset>
                    <div class="grid 2xl:grid-cols-3 gap-x-4">
                        <div>
                            <flux:select badge="required" required variant="listbox" searchable
                                         placeholder="Choose type..."
                                         label="Event type"
                                         wire:model="type">
                                @forelse($eventTypes as $eventType)
                                    <flux:select.option value="{{ $eventType->id }}">{{ $eventType->name }}</flux:select.option>
                                @empty
                                    <flux:text>No types found</flux:text>
                                @endforelse
                            </flux:select>
                        </div>

                        <div>
                            <flux:select badge="required" required variant="listbox" searchable
                                         placeholder="Choose venue..." label="Event venue"
                                         wire:model="venue">
                                @forelse($eventLocations as $location)
                                    <flux:select.option
                                        value="{{ $location->id }}">{{ $location->name }}</flux:select.option>
                                @empty
                                    <flux:text>No locations found</flux:text>
                                @endforelse
                            </flux:select>
                        </div>

                        <div>
                            <flux:select badge="required" required variant="listbox" multiple searchable
                                         placeholder="Choose categories..."
                                         label="Event categories"
                                         wire:model="categories">
                                @forelse($eventCategories as $category)
                                    <flux:select.option
                                        value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                                @empty
                                    <flux:text>No categories found</flux:text>
                                @endforelse
                            </flux:select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 mt-6">
                        <flux:editor badge="required" required wire:model="description" label="Event description"
                                     class="**:data-[slot=content]:min-h-[100px]!"/>
                    </div>

                    <div class="grid 2xl:grid-cols-3 gap-4 space-y-6 mt-6">
                        <div>
                            <flux:date-picker badge="required" required wire:model="event_start_date"
                                              label="Start date"/>
                        </div>

                        <div>
                            <flux:date-picker badge="required" required wire:model="event_end_date"
                                              label="End date"/>
                        </div>

                        <div>
                            <flux:date-picker badge="required" required wire:model="close_rsvp_at"
                                              label="Close RSVP at"/>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 col-span-full">
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
                        </flux:fieldset>
                    </div>
                </flux:fieldset>

                <div class="mb-6">
                    <div class="flex flex-col max-h-screen overflow-hidden mt-6">
                        <flux:heading size="xl">Event Sessions</flux:heading>
                        <flux:text variant="subtle" class="text-xs md:text-md">
                            Add your sessions (sub events) to this event. You must have at least one and a
                            maximum of 40 sessions.
                        </flux:text>
                        <flux:separator variant="subtle" class="my-6"/>

                        <div class="flex justify-between items-center">
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

                <main>
                    <div class="grid grid-cols-1 gap-4">
                        @if(is_array($sessions))
                            @forelse ($sessions as $index => $session)
                                <flux:card class="gap-4 p-4!">
                                    <div class="flex justify-between items-center my-6">
                                        <flux:badge size="sm" color="amber" variant="solid">
                                            Session {{ $index + 1 }}
                                            @if(isset($session['id']))
                                                <span class="ml-1">(Existing)</span>
                                            @else
                                                <span class="ml-1">(New)</span>
                                            @endif
                                        </flux:badge>
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
                                                        wire:model="sessions.{{ $index }}.name"/>
                                        </div>

                                        <div>
                                            <flux:input badge="required" required label="Location"
                                                        wire:model="sessions.{{ $index }}.location"/>
                                        </div>

                                        <div>
                                            <flux:input badge="optional" label="Capacity"
                                                        wire:model="sessions.{{ $index }}.capacity"/>
                                        </div>

                                        <div class="grid col-span-full">
                                            <flux:textarea badge="required" rows="3" required label="Description"
                                                           wire:model="sessions.{{ $index }}.description"/>
                                        </div>

                                        <div>
                                            <flux:date-picker badge="required" required label="Start date"
                                                              wire:model="sessions.{{ $index }}.start_date"/>
                                        </div>

                                        <div>
                                            <flux:input badge="required" required type="time" label="Start time"
                                                        wire:model="sessions.{{ $index }}.start_time"/>
                                        </div>

                                        <div>
                                            <flux:input badge="required" required type="time" label="End time"
                                                        wire:model="sessions.{{ $index }}.end_time"/>
                                        </div>

                                        <div>
                                            <flux:checkbox label="Allow guests"
                                                           wire:model="sessions.{{ $index }}.allow_guests"/>
                                        </div>
                                    </div>
                                </flux:card>
                            @empty
                                <flux:text>No sessions found</flux:text>
                            @endforelse
                        @endif
                    </div>
                </main>

                <div class="flex justify-end mt-6 gap-4">
                    <flux:button type="button" variant="filled" href="{{ route('events.index') }}">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Update Event</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>
