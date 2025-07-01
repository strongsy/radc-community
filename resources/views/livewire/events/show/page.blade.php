<?php

use App\Models\Event;
use App\Models\EventSession;
use App\Models\EventSessionUser;
use App\Models\EventSessionGuest;
use App\Models\FoodPreference;
use App\Models\DrinkPreference;
use App\Models\FoodAllergy;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Flux\Flux;

new class extends Component {
    public Event $event;
    public Collection $eventSessions;
    public Collection $foodPreferences;
    public Collection $drinkPreferences;
    public Collection $foodAllergies;

    // Registration state
    public array $selectedSessions = [];
    public array $userPreferences = [];
    public array $userAllergies = [];
    public array $userDrinks = [];

    // Guest management
    public array $guests = [];
    public bool $showGuestModal = false;
    public int $currentSessionId = 0;

    // Guest form data
    public string $guestName = '';
    public string $guestEmail = '';
    public array $guestFoodPreferences = [];
    public array $guestDrinkPreferences = [];
    public array $guestAllergies = [];

    public function mount(int $id): void
    {
        $this->event = Event::with([
            'title',
            'venue',
            'categories',
            'user',
            'eventSessions.eventSessionUsers.user',
            'eventSessions.eventSessionGuests'
        ])->findOrFail($id);

        $this->eventSessions = $this->event->eventSessions;
        $this->loadPreferencesAndAllergies();
        $this->loadUserRegistrations();
    }

    private function loadPreferencesAndAllergies(): void
    {
        $this->foodPreferences = FoodPreference::orderBy('name')->get();
        $this->drinkPreferences = DrinkPreference::orderBy('name')->get();
        $this->foodAllergies = FoodAllergy::orderBy('name')->get();
    }

    private function loadUserRegistrations(): void
    {
        if (!auth()->check()) {
            return;
        }

        $userId = auth()->id();

        // Load user's existing registrations
        $userSessions = EventSessionUser::where('user_id', $userId)
            ->whereIn('event_session_id', $this->eventSessions->pluck('id'))
            ->with(['foodPreferences', 'drinkPreferences', 'foodAllergies'])
            ->get();

        foreach ($userSessions as $userSession) {
            $this->selectedSessions[] = $userSession->event_session_id;
            $this->userPreferences[$userSession->event_session_id] = $userSession->foodPreferences->pluck('id')->toArray();
            $this->userDrinks[$userSession->event_session_id] = $userSession->drinkPreferences->pluck('id')->toArray();
            $this->userAllergies[$userSession->event_session_id] = $userSession->foodAllergies->pluck('id')->toArray();
        }

        // Load user's guests
        $userGuests = EventSessionGuest::where('user_id', $userId)
            ->whereIn('event_session_id', $this->eventSessions->pluck('id'))
            ->with(['foodPreferences', 'drinkPreferences', 'foodAllergies'])
            ->get();

        foreach ($userGuests as $guest) {
            $this->guests[$guest->event_session_id][] = [
                'id' => $guest->id,
                'name' => $guest->name,
                'email' => $guest->email,
                'food_preferences' => $guest->foodPreferences->pluck('id')->toArray(),
                'drink_preferences' => $guest->drinkPreferences->pluck('id')->toArray(),
                'allergies' => $guest->foodAllergies->pluck('id')->toArray(),
            ];
        }
    }

    public function toggleSessionRegistration(int $sessionId): void
    {
        if (!auth()->check()) {
            Flux::toast(
                text: 'Please log in to register for sessions',
                heading: 'Authentication Required',
                variant: 'danger',
            );
            return;
        }

        if (in_array($sessionId, $this->selectedSessions, true)) {
            $this->unregisterFromSession($sessionId);
        } else {
            $this->registerForSession($sessionId);
        }
    }

    private function registerForSession(int $sessionId): void
    {
        try {
            $session = $this->eventSessions->find($sessionId);

            // Check capacity
            if ($session->capacity && $this->getSessionRegistrationCount($sessionId) >= $session->capacity) {
                Flux::toast(
                    text: 'This session is at full capacity',
                    heading: 'Registration Failed',
                    variant: 'danger',
                );
                return;
            }

            // Check if RSVP is still open
            if ($this->event->rsvp_closes_at->isPast()) {
                Flux::toast(
                    text: 'RSVP for this event has closed',
                    heading: 'Registration Closed',
                    variant: 'danger',
                );
                return;
            }

            DB::transaction(function () use ($sessionId) {
                $eventSessionUser = EventSessionUser::create([
                    'user_id' => auth()->id(),
                    'event_session_id' => $sessionId,
                ]);

                // Attach preferences if selected
                if (!empty($this->userPreferences[$sessionId])) {
                    $eventSessionUser->foodPreferences()->attach($this->userPreferences[$sessionId]);
                }
                if (!empty($this->userDrinks[$sessionId])) {
                    $eventSessionUser->drinkPreferences()->attach($this->userDrinks[$sessionId]);
                }
                if (!empty($this->userAllergies[$sessionId])) {
                    $eventSessionUser->foodAllergies()->attach($this->userAllergies[$sessionId]);
                }
            });

            $this->selectedSessions[] = $sessionId;
            $this->refreshEventData();

            Flux::toast(
                text: 'Successfully registered for session',
                heading: 'Registration Successful',
                variant: 'success',
            );

        } catch (Exception $e) {
            Log::error('Failed to register for session', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
            ]);

            Flux::toast(
                text: 'Failed to register for session',
                heading: 'Registration Failed',
                variant: 'danger',
            );
        }
    }

    private function unregisterFromSession(int $sessionId): void
    {
        try {
            DB::transaction(static function () use ($sessionId) {
                // Remove user registration
                EventSessionUser::where('user_id', auth()->id())
                    ->where('event_session_id', $sessionId)
                    ->delete();

                // Remove all guests for this session
                EventSessionGuest::where('user_id', auth()->id())
                    ->where('event_session_id', $sessionId)
                    ->delete();
            });

            $this->selectedSessions = array_diff($this->selectedSessions, [$sessionId]);
            unset($this->guests[$sessionId]);
            $this->refreshEventData();

            Flux::toast(
                text: 'Successfully unregistered from session',
                heading: 'Unregistration Successful',
                variant: 'success',
            );

        } catch (Exception $e) {
            Log::error('Failed to unregister from session', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
            ]);

            Flux::toast(
                text: 'Failed to unregister from session',
                heading: 'Unregistration Failed',
                variant: 'danger',
            );
        }
    }

    public function updateUserPreferences(int $sessionId): void
    {
        if (!in_array($sessionId, $this->selectedSessions, true)) {
            return;
        }

        try {
            $eventSessionUser = EventSessionUser::where('user_id', auth()->id())
                ->where('event_session_id', $sessionId)
                ->first();

            if ($eventSessionUser) {
                // Sync preferences
                $eventSessionUser->foodPreferences()->sync($this->userPreferences[$sessionId] ?? []);
                $eventSessionUser->drinkPreferences()->sync($this->userDrinks[$sessionId] ?? []);
                $eventSessionUser->foodAllergies()->sync($this->userAllergies[$sessionId] ?? []);

                Flux::toast(
                    text: 'Preferences updated successfully',
                    heading: 'Preferences Updated',
                    variant: 'success',
                );
            }
        } catch (Exception $e) {
            Log::error('Failed to update user preferences', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
            ]);

            Flux::toast(
                text: 'Failed to update preferences',
                heading: 'Update Failed',
                variant: 'danger',
            );
        }
    }

    public function openGuestModal(int $sessionId): void
    {
        $session = $this->eventSessions->find($sessionId);

        if (!$session->allow_guests) {
            return;
        }

        if (!in_array($sessionId, $this->selectedSessions, true)) {
            Flux::toast(
                text: 'You must be registered for this session to add guests',
                heading: 'Registration Required',
                variant: 'danger',
            );
            return;
        }

        $this->currentSessionId = $sessionId;
        $this->resetGuestForm();
        $this->showGuestModal = true;
    }

    protected function getFullAddress(Venue $venue): string
    {
        return sprintf(
            '%s, %s %s %s %s',
            $venue->venue,
            $venue->address,
            strtoupper($venue->city),
            $venue->county,
            strtoupper($venue->post_code)
        );
    }

    public function addGuest(): void
    {
        $this->validate([
            'guestName' => 'required|string|max:255',
            'guestEmail' => 'required|email|max:255',
        ]);

        try {
            $session = $this->eventSessions->find($this->currentSessionId);

            // Check capacity including guests
            if ($session->capacity && $this->getSessionRegistrationCount($this->currentSessionId) >= $session->capacity) {
                Flux::toast(
                    text: 'This session is at full capacity',
                    heading: 'Capacity Reached',
                    variant: 'danger',
                );
                return;
            }

            DB::transaction(function () {
                $guest = EventSessionGuest::create([
                    'user_id' => auth()->id(),
                    'event_session_id' => $this->currentSessionId,
                    'name' => $this->guestName,
                    'email' => $this->guestEmail,
                ]);

                // Attach preferences
                if (!empty($this->guestFoodPreferences)) {
                    $guest->foodPreferences()->attach($this->guestFoodPreferences);
                }
                if (!empty($this->guestDrinkPreferences)) {
                    $guest->drinkPreferences()->attach($this->guestDrinkPreferences);
                }
                if (!empty($this->guestAllergies)) {
                    $guest->foodAllergies()->attach($this->guestAllergies);
                }
            });

            $this->refreshEventData();
            $this->loadUserRegistrations();
            $this->closeGuestModal();

            Flux::toast(
                text: 'Guest added successfully',
                heading: 'Guest Added',
                variant: 'success',
            );

        } catch (Exception $e) {
            Log::error('Failed to add guest', [
                'error' => $e->getMessage(),
                'session_id' => $this->currentSessionId,
                'user_id' => auth()->id(),
            ]);

            Flux::toast(
                text: 'Failed to add guest',
                heading: 'Add Guest Failed',
                variant: 'danger',
            );
        }
    }

    public function removeGuest(int $sessionId, int $guestId): void
    {
        try {
            EventSessionGuest::where('id', $guestId)
                ->where('user_id', auth()->id())
                ->delete();

            $this->refreshEventData();
            $this->loadUserRegistrations();

            Flux::toast(
                text: 'Guest removed successfully',
                heading: 'Guest Removed',
                variant: 'success',
            );

        } catch (Exception $e) {
            Log::error('Failed to remove guest', [
                'error' => $e->getMessage(),
                'guest_id' => $guestId,
                'user_id' => auth()->id(),
            ]);

            Flux::toast(
                text: 'Failed to remove guest',
                heading: 'Remove Guest Failed',
                variant: 'danger',
            );
        }
    }

    private function resetGuestForm(): void
    {
        $this->guestName = '';
        $this->guestEmail = '';
        $this->guestFoodPreferences = [];
        $this->guestDrinkPreferences = [];
        $this->guestAllergies = [];
    }

    public function closeGuestModal(): void
    {
        $this->showGuestModal = false;
        $this->currentSessionId = 0;
        $this->resetGuestForm();
    }

    private function refreshEventData(): void
    {
        $this->event->refresh();
        $this->event->load([
            'eventSessions.eventSessionUsers.user',
            'eventSessions.eventSessionGuests'
        ]);
        $this->eventSessions = $this->event->eventSessions;
    }

    private function getSessionRegistrationCount(int $sessionId): int
    {
        $session = $this->eventSessions->find($sessionId);
        if (!$session) return 0;

        return $session->eventSessionUsers->count() + $session->eventSessionGuests->count();
    }

    public function isRegisteredForSession(int $sessionId): bool
    {
        return in_array($sessionId, $this->selectedSessions, true);
    }

    public function canAddGuests(int $sessionId): bool
    {
        $session = $this->eventSessions->find($sessionId);
        return $session && $session->allow_guests && $this->isRegisteredForSession($sessionId);
    }
};
?>

<div
    class="flex flex-col translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750 space-y-6">

    <flux:heading size="xl" class="mb-6">{{ $event->title->name }}</flux:heading>
    <flux:card>
        <div class="flex items-center gap-4 mb-4">
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-4">
                    @if($event->rsvp_closes_at->isFuture())
                        <flux:badge icon="arrow-right-end-on-rectangle" size="sm" color="green" variant="solid">
                            RSVP Open
                        </flux:badge>
                    @else
                        <flux:badge icon="arrow-left-start-on-rectangle" size="sm" color="red" variant="solid">
                            RSVP Closed
                        </flux:badge>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="flex items-center">
                <flux:icon.calendar class="mr-3 w-5 h-5"/>
                <div>
                    <flux:heading size="sm">Event Dates</flux:heading>
                    <flux:text size="sm">
                        {{ $event->start_date->format('D d M Y') }} - {{ $event->end_date->format('D d M Y') }}
                    </flux:text>
                </div>
            </div>

            <div class="flex items-center">
                <flux:icon.map-pin class="mr-3 w-5 h-5"/>
                <div>
                    <flux:heading size="sm" class="font-medium">Venue</flux:heading>
                    <div>
                        <flux:link class="text-sm"
                            href="https://maps.google.com/maps?q={{ urlencode($event->venue->getFullAddress()) }}"
                            target="_blank"
                            variant="ghost"
                        >
                            {{ $event->venue->name }}
                            <flux:icon.arrow-top-right-on-square class="ml-1 w-3 h-3 inline"/>
                        </flux:link>
                    </div>

                </div>
            </div>

            <div class="flex items-center">
                <flux:icon.user class="mr-3 w-5 h-5"/>
                <div>
                    <flux:heading size="sm" class="font-medium">Organizer</flux:heading>
                    <flux:text size="sm">{{ $event->user->name }}</flux:text>
                </div>
            </div>

            <div class="flex items-center">
                <flux:icon.clock class="mr-3 w-5 h-5"/>
                <div>
                    <flux:heading size="sm" class="font-medium">RSVP Closes</flux:heading>
                    <flux:text size="sm">{{ $event->rsvp_closes_at->format('D d M Y g:i A') }}</flux:text>
                </div>
            </div>
        </div>
    </flux:card>


    <!-- Event Header -->
    {{--<flux:card>
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-4">
                    <flux:badge icon="calendar" color="blue" variant="solid">
                        {{ $event->title->name }}
                    </flux:badge>
                    @if($event->rsvp_closes_at->isFuture())
                        <flux:badge icon="arrow-right-end-on-rectangle" size="sm" color="green" variant="solid">
                            RSVP Open
                        </flux:badge>
                    @else
                        <flux:badge icon="arrow-left-start-on-rectangle" size="sm" color="red" variant="solid">
                            RSVP Closed
                        </flux:badge>
                    @endif
                </div>
                <div>
                    <flux:heading size="xl" class="mb-4">{{ $event->title->name }}</flux:heading>
                </div>


                <!-- Event Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="flex items-center">
                        <flux:icon.calendar class="mr-3 w-5 h-5"/>
                        <div>
                            <flux:heading size="sm">Event Dates</flux:heading>
                            <flux:text size="sm">
                                {{ $event->start_date->format('M d, Y') }} - {{ $event->end_date->format('M d, Y') }}
                            </flux:text>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <flux:icon.map-pin class="mr-3 w-5 h-5"/>
                        <div>
                            <flux:heading size="sm" class="font-medium">Venue</flux:heading>
                            <flux:text size="sm">{{ $event->venue->name }}</flux:text>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <flux:icon.user class="mr-3 w-5 h-5"/>
                        <div>
                            <flux:heading size="sm" class="font-medium">Organizer</flux:heading>
                            <flux:text size="sm">{{ $event->user->name }}</flux:text>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <flux:icon.clock class="mr-3 w-5 h-5"/>
                        <div>
                            <flux:heading size="sm" class="font-medium">RSVP Closes</flux:heading>
                            <flux:text size="sm">{{ $event->rsvp_closes_at->format('M d, Y g:i A') }}</flux:text>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                @if($event->categories->count() > 0)
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($event->categories as $category)
                            <flux:badge size="sm" color="{{ $category->colour }}">{{ $category->name }}</flux:badge>
                        @endforeach
                    </div>
                @endif

                <!-- Description -->
                <flux:heading size="sm" class="font-medium">About this event</flux:heading>
                <flux:text class="prose max-w-none text-sm">
                    {!! $event->description !!}
                </flux:text>
            </div>

            <!-- Event Image -->
            @php
                $media = $event->getFirstMedia('event');
            @endphp
            @if($media)
                <div class="lg:ml-8 mt-6 lg:mt-0">
                    <img src="{{ $media->getUrl('event') }}" alt="{{ $event->title->name }}"
                         class="w-full lg:w-80 h-60 object-cover rounded-lg">
                </div>
            @endif
        </div>
    </flux:card>

    <!-- Event Sessions -->
    <flux:card>
        <flux:heading size="lg" class="mb-6">Event Sessions</flux:heading>

        <flux:card class="space-y-6">
            @forelse($eventSessions as $session)
                <div>
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-4">
                        <div class="flex-1 space-y-4">
                            <div>
                                <flux:heading size="sm">Session name</flux:heading>
                                <flux:text size="sm">{{ $event->title->name }}</flux:text>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <flux:icon.calendar class="mr-2 w-4 h-4"/>
                                    <div>
                                        <flux:heading size="sm" class="font-medium">Event date</flux:heading>
                                        <flux:text size="sm">{{ $session->start_date->format('M d, Y') }}</flux:text>
                                    </div>

                                </div>

                                <div class="flex items-center">
                                    <flux:icon.clock class="mr-2 w-4 h-4 "/>
                                    <div>
                                        <flux:heading size="sm" class="font-medium">Event timings</flux:heading>
                                        <flux:text size="sm">
                                            {{ Carbon::parse($session->start_time)->format('g:i A') }} -
                                            {{ Carbon::parse($session->end_time)->format('g:i A') }}
                                        </flux:text>
                                    </div>

                                </div>

                                <div class="flex items-center">
                                    <flux:icon.map-pin class="mr-2 w-4 h-4"/>
                                    <div>
                                        <flux:heading size="sm" class="font-medium">Venue</flux:heading>
                                        <flux:text size="sm">{{ $session->location }}</flux:text>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <flux:heading size="sm" class="font-medium">Description</flux:heading>
                                <flux:text size="sm">{{ $session->description }}</flux:text>
                            </div>

                            <!-- Registration Status -->
                            <div class="flex items-center gap-4 mb-4">
                                @php
                                    $registrationCount = $this->getSessionRegistrationCount($session->id);
                                @endphp

                                <flux:badge size="sm" icon="users" color="blue">
                                    {{ $registrationCount }}{{ $session->capacity ? "/$session->capacity" : '' }}
                                    registered
                                </flux:badge>

                                @if($session->allow_guests)
                                    <flux:badge size="sm" icon="user-plus" color="green">Guests allowed</flux:badge>
                                @endif

                                @if($session->capacity && $registrationCount >= $session->capacity)
                                    <flux:badge size="sm" icon="exclamation-triangle" color="red">Full</flux:badge>
                                @endif
                            </div>
                        </div>

                        <!-- Registration Actions -->
                        @auth
                            <div class="flex flex-col gap-3 lg:ml-6">
                                @if($event->rsvp_closes_at->isFuture())
                                    <flux:button
                                        wire:click="toggleSessionRegistration({{ $session->id }})"
                                        variant="{{ $this->isRegisteredForSession($session->id) ? 'danger' : 'primary' }}"
                                        icon="{{ $this->isRegisteredForSession($session->id) ? 'x-mark' : 'check' }}"
                                        size="sm"
                                        :disabled="!$this->isRegisteredForSession($session->id) && $session->capacity && $registrationCount >= $session->capacity"
                                    >
                                        {{ $this->isRegisteredForSession($session->id) ? 'Unregister' : 'Register' }}
                                    </flux:button>

                                    @if($this->canAddGuests($session->id))
                                        <flux:button
                                            wire:click="openGuestModal({{ $session->id }})"
                                            variant="filled"
                                            icon="user-plus"
                                            size="sm"
                                        >
                                            Add Guest
                                        </flux:button>
                                    @endif
                                @else
                                    <flux:text size="sm" class="text-red-600">RSVP Closed</flux:text>
                                @endif
                            </div>
                        @else
                            <div class="lg:ml-6">
                                <flux:button href="{{ route('login') }}" variant="primary" size="sm">
                                    Login to Register
                                </flux:button>
                            </div>
                        @endauth
                    </div>

                    <!-- User Preferences (if registered) -->
                    @if($this->isRegisteredForSession($session->id))
                        <flux:separator variant="subtle" class="my-4"/>
                        <div>
                            <flux:heading size="sm" class="mb-3">Your Preferences</flux:heading>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                <!-- Food Preferences -->
                                <div>
                                    <flux:text size="sm" class="font-medium mb-2">Food Preferences</flux:text>
                                    <div class="space-y-1">
                                        @foreach($foodPreferences as $preference)
                                            <flux:checkbox
                                                label="{{ $preference->name }}"
                                                wire:model.lazy="userPreferences.{{ $session->id }}"
                                                wire:change="updateUserPreferences({{ $session->id }})"
                                                value="{{ $preference->id }}"
                                            />
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Drink Preferences -->
                                <div>
                                    <flux:text size="sm" class="font-medium mb-2">Drink Preferences</flux:text>
                                    <div class="space-y-1">
                                        @foreach($drinkPreferences as $drink)
                                            <flux:checkbox
                                                label="{{ $drink->name }}"
                                                wire:model.lazy="userDrinks.{{ $session->id }}"
                                                wire:change="updateUserPreferences({{ $session->id }})"
                                                value="{{ $drink->id }}"
                                            />
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Allergies -->
                                <div>
                                    <flux:text size="sm" class="font-medium mb-2">Allergies</flux:text>
                                    <div class="space-y-1">
                                        @foreach($foodAllergies as $allergy)
                                            <flux:checkbox
                                                label="{{ $allergy->name }}"
                                                wire:model.lazy="userAllergies.{{ $session->id }}"
                                                wire:change="updateUserPreferences({{ $session->id }})"
                                                value="{{ $allergy->id }}"
                                            />
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Guests List -->
                    @if(isset($guests[$session->id]) && count($guests[$session->id]) > 0)
                        <div>
                            <flux:separator variant="subtle" class="my-4"/>
                            <flux:heading size="sm" class="mb-3">Your Guests</flux:heading>
                            <div class="space-y-3">
                                @foreach($guests[$session->id] as $guest)
                                    <div class="flex items-center justify-between p-3 bg-zinc-50 rounded-lg">
                                        <div>
                                            <flux:text size="sm" class="font-medium">{{ $guest['name'] }}</flux:text>
                                            <flux:text size="xs" class="text-zinc-600">{{ $guest['email'] }}</flux:text>
                                        </div>
                                        <flux:button
                                            wire:click="removeGuest({{ $session->id }}, {{ $guest['id'] }})"
                                            variant="danger"
                                            size="xs"
                                            icon="trash"
                                        >
                                            Remove
                                        </flux:button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Registered Users -->
                    @if($session->eventSessionUsers->count() > 0)
                        <div>
                            <flux:separator variant="subtle" class="my-4"/>
                            <flux:heading size="sm" class="mb-3">Registered Attendees</flux:heading>
                            <div class="flex flex-wrap gap-2">
                                @foreach($session->eventSessionUsers as $sessionUser)
                                    <flux:badge size="sm" icon="user">{{ $sessionUser->user->name }}</flux:badge>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-8">
                    <flux:icon name="calendar" class="w-12 h-12 mx-auto text-zinc-400 mb-4"/>
                    <flux:text>No sessions available for this event.</flux:text>
                </div>
            @endforelse
        </flux:card>
    </flux:card>

    <!-- Guest Modal -->
    @if($showGuestModal)
        <flux:modal name="guest-modal" wire:model="showGuestModal">
            <form wire:submit="addGuest">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Add Guest</flux:heading>
                        <flux:text>Add a guest to this session</flux:text>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input
                            label="Guest Name"
                            wire:model="guestName"
                            required
                        />
                        <flux:input
                            label="Guest Email"
                            type="email"
                            wire:model="guestEmail"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <!-- Food Preferences -->
                        <div>
                            <flux:text size="sm" class="font-medium mb-2">Food Preferences</flux:text>
                            <div class="space-y-1 max-h-32 overflow-y-auto">
                                @foreach($foodPreferences as $preference)
                                    <flux:checkbox
                                        label="{{ $preference->name }}"
                                        wire:model="guestFoodPreferences"
                                        value="{{ $preference->id }}"
                                    />
                                @endforeach
                            </div>
                        </div>

                        <!-- Drink Preferences -->
                        <div>
                            <flux:text size="sm" class="font-medium mb-2">Drink Preferences</flux:text>
                            <div class="space-y-1 max-h-32 overflow-y-auto">
                                @foreach($drinkPreferences as $drink)
                                    <flux:checkbox
                                        label="{{ $drink->name }}"
                                        wire:model="guestDrinkPreferences"
                                        value="{{ $drink->id }}"
                                    />
                                @endforeach
                            </div>
                        </div>

                        <!-- Allergies -->
                        <div>
                            <flux:text size="sm" class="font-medium mb-2">Allergies</flux:text>
                            <div class="space-y-1 max-h-32 overflow-y-auto">
                                @foreach($foodAllergies as $allergy)
                                    <flux:checkbox
                                        label="{{ $allergy->name }}"
                                        wire:model="guestAllergies"
                                        value="{{ $allergy->id }}"
                                    />
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <flux:button variant="ghost" wire:click="closeGuestModal">Cancel</flux:button>
                        <flux:button type="submit" variant="primary">Add Guest</flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>
    @endif

    <!-- Back Button -->
    <div class="flex justify-start">
        <flux:button href="{{ route('events.index') }}" variant="filled" icon="arrow-left">
            Back to Events
        </flux:button>
    </div>--}}
</div>
