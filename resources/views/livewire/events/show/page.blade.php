<?php

use App\Models\Event;
use App\Models\EventSession;
use App\Models\EventSessionUser;
use App\Models\EventSessionGuest;
use App\Models\FoodPreference;
use App\Models\DrinkPreference;
use App\Models\FoodAllergy;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Exceptions;
use Flux\Flux;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
    public bool $showEditGuestModal = false;
    public int $editingGuestId = 0;

    public int $currentSessionId = 0;

    // Guest form data
    public string $guestName = '';
    public string $guestEmail = '';
    public array $guestFoodPreferences = [];
    public array $guestDrinkPreferences = [];
    public array $guestAllergies = [];

    public int $registrationCount = 0;

    public User $user;

    public function mount(int $id): void
    {
        $this->user = auth()->user();

        $this->event = Event::with([
            'title',
            'venue',
            'categories',
            'user',
            'eventSessions.eventSessionUsers.user',
            'eventSessions.eventSessionUsers.foodPreferences',
            'eventSessions.eventSessionUsers.drinkPreferences',
            'eventSessions.eventSessionUsers.foodAllergies',
            'eventSessions.eventSessionGuests.user', // This line was missing the .user relationship
            'eventSessions.eventSessionGuests.foodPreferences',
            'eventSessions.eventSessionGuests.drinkPreferences',
            'eventSessions.eventSessionGuests.foodAllergies'
        ])->findOrFail($id);


        $this->eventSessions = $this->event->eventSessions;
        $this->loadPreferencesAndAllergies();
        $this->loadUserRegistrations();

        $this->debugSessionData();
    }

    public function debugSessionData(): void
    {
        Log::info('=== SESSION DATA DEBUG ===', [
            'event_id' => $this->event->id,
            'total_sessions' => $this->eventSessions->count(),
            'session_details' => $this->eventSessions->map(function ($session, $index) {
                return [
                    'index' => $index,
                    'id' => $session->id,
                    'name' => $session->name,
                    'users_count' => $session->eventSessionUsers->count(),
                    'first_user_id' => $session->eventSessionUsers->first()?->user_id ?? 'none'
                ];
            })->toArray(),
            'selected_sessions' => $this->selectedSessions,
            'current_user_id' => auth()->id()
        ]);
    }


    private function isUserRegisteredForSession(User $user, int $eventSessionId): bool
    {
        return $user->eventSessions()->where('event_session_id', $eventSessionId)->exists();
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
            /** @var EventSession|null $session */
            $session = $this->eventSessions->firstWhere('id', $sessionId);

            if (!$session) {
                return;
            }


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
            if ($this->event->rsvp_closes_at?->isPast()) {
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
            $userId = auth()->id();

            if (!$userId) {
                Flux::toast(
                    text: 'You must be logged in to unregister',
                    heading: 'Authentication Required',
                    variant: 'danger',
                );
                return;
            }

            DB::transaction(static function () use ($sessionId, $userId) {
                // Remove user registration
                EventSessionUser::where('user_id', $userId)
                    ->where('event_session_id', $sessionId)
                    ->delete();

                // Get all guests for this session before deletion
                $guests = EventSessionGuest::where('user_id', $userId)
                    ->where('event_session_id', $sessionId)
                    ->get();

                // Remove pivot table relationships for each guest
                foreach ($guests as $guest) {
                    $guest->foodPreferences()->detach();
                    $guest->drinkPreferences()->detach();
                    $guest->foodAllergies()->detach();
                }

                // Now remove the guests
                EventSessionGuest::where('user_id', $userId)
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
        $this->validate([
            "userPreferences.$sessionId.*" => 'exists:food_preferences,id',
            "userDrinks.$sessionId.*" => 'exists:drink_preferences,id',
            "userAllergies.$sessionId.*" => 'exists:food_allergies,id',
        ]);

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
        /** @var EventSession|null $session */
        $session = $this->eventSessions->firstWhere('id', $sessionId);

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

        // Check if the user already has 2 guests for this session
        $currentGuestCount = isset($this->guests[$sessionId]) ? count($this->guests[$sessionId]) : 0;
        if ($currentGuestCount >= 2) {
            Flux::toast(
                text: 'You can only add a maximum of 2 guests per session',
                heading: 'Guest Limit Reached',
                variant: 'danger',
            );
            return;
        }

        $this->currentSessionId = $sessionId;
        $this->resetGuestForm();
        $this->showGuestModal = true;
    }

    public function openEditGuestModal(int $sessionId, int $guestId): void
    {
        $guest = EventSessionGuest::where('id', $guestId)
            ->where('user_id', auth()->id())
            ->with(['foodPreferences', 'drinkPreferences', 'foodAllergies'])
            ->first();

        if (!$guest) {
            Flux::toast(
                text: 'Guest not found',
                heading: 'Error',
                variant: 'danger',
            );
            return;
        }

        $this->currentSessionId = $sessionId;
        $this->editingGuestId = $guestId;

        // Pre-populate form with existing guest data
        $this->guestName = $guest->name;
        $this->guestEmail = $guest->email;
        $this->guestFoodPreferences = $guest->foodPreferences->pluck('id')->toArray();
        $this->guestDrinkPreferences = $guest->drinkPreferences->pluck('id')->toArray();
        $this->guestAllergies = $guest->foodAllergies->pluck('id')->toArray();


        $this->showEditGuestModal = true;
    }

    public function updateGuest(): void
    {
        $this->validate([
            'guestName' => 'required|string|max:255',
            'guestEmail' => 'required|email|max:255',
        ]);

        try {
            // Add debugging to see what values we have
            Log::info('updateGuest called', [
                'editingGuestId' => $this->editingGuestId,
                'user_id' => auth()->id(),
                'guestName' => $this->guestName,
                'guestEmail' => $this->guestEmail
            ]);

            // Check if editingGuestId is set
            if (!$this->editingGuestId) {
                Flux::toast(
                    text: 'No guest selected for editing',
                    heading: 'Error',
                    variant: 'danger',
                );
                return;
            }

            $guest = EventSessionGuest::where('id', $this->editingGuestId)
                ->where('user_id', auth()->id())
                ->first();

            // Add more detailed logging
            Log::info('Guest query result', [
                'guest_found' => $guest ? 'yes' : 'no',
                'guest_id' => $guest?->id,
                'guest_user_id' => $guest?->user_id,
                'editingGuestId' => $this->editingGuestId,
                'auth_user_id' => auth()->id()
            ]);


            if (!$guest) {
                // Try to find the guest without the user_id constraint to see if it exists
                $guestExists = EventSessionGuest::where('id', $this->editingGuestId)->first();

                if ($guestExists) {
                    Log::warning('Guest exists but belongs to different user', [
                        'guest_id' => $this->editingGuestId,
                        'guest_user_id' => $guestExists->user_id,
                        'auth_user_id' => auth()->id()
                    ]);

                    Flux::toast(
                        text: 'You are not authorized to edit this guest',
                        heading: 'Unauthorized',
                        variant: 'danger',
                    );
                } else {
                    Log::warning('Guest does not exist', [
                        'guest_id' => $this->editingGuestId
                    ]);

                    Flux::toast(
                        text: 'Guest not found',
                        heading: 'Error',
                        variant: 'danger',
                    );
                }
                return;
            }


            DB::transaction(function () use ($guest) {
                // Update guest basic info
                $guest->update([
                    'name' => $this->guestName,
                    'email' => $this->guestEmail,
                ]);

                // Sync preferences
                $guest->foodPreferences()->sync($this->guestFoodPreferences ?? []);
                $guest->drinkPreferences()->sync($this->guestDrinkPreferences ?? []);
                $guest->foodAllergies()->sync($this->guestAllergies ?? []);
            });

            $this->refreshEventData();
            $this->loadUserRegistrations();
            $this->closeEditGuestModal();

            Flux::toast(
                text: 'Guest updated successfully',
                heading: 'Guest Updated',
                variant: 'success',
            );

        } catch (Exception $e) {
            Log::error('Failed to update guest', [
                'error' => $e->getMessage(),
                'guest_id' => $this->editingGuestId,
                'user_id' => auth()->id(),
            ]);

            Flux::toast(
                text: 'Failed to update guest',
                heading: 'Update Failed',
                variant: 'danger',
            );
        }
    }

    public function closeEditGuestModal(): void
    {
        $this->showEditGuestModal = false;
        $this->editingGuestId = 0;
        $this->currentSessionId = 0;
        $this->resetGuestForm();
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
            $session = $this->eventSessions->firstWhere('id', $this->currentSessionId);

            if (!$session) {
                return;
            }

            // Check guest limit before proceeding
            $currentGuestCount = isset($this->guests[$this->currentSessionId]) ? count($this->guests[$this->currentSessionId]) : 0;
            if ($currentGuestCount >= 2) {
                Flux::toast(
                    text: 'You can only add a maximum of 2 guests per session',
                    heading: 'Guest Limit Reached',
                    variant: 'danger',
                );
                return;
            }

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
            'eventSessions.eventSessionUsers.foodPreferences',
            'eventSessions.eventSessionUsers.drinkPreferences',
            'eventSessions.eventSessionUsers.foodAllergies',
            'eventSessions.eventSessionGuests.user',
            'eventSessions.eventSessionGuests.foodPreferences',
            'eventSessions.eventSessionGuests.drinkPreferences',
            'eventSessions.eventSessionGuests.foodAllergies'
        ]);
        $this->eventSessions = $this->event->eventSessions;
    }


    public function exportEventData(): StreamedResponse
    {
        // Check if the user is the event owner
        if (auth()->id() !== $this->event->user_id) {
            abort(403, 'Unauthorized');
        }

        return $this->generateExcelExport();
    }

    private function generateExcelExport(): StreamedResponse
    {
        // Load fresh data with all relationships for export
        $event = Event::with([
            'title',
            'venue',
            'categories',
            'user',
            'eventSessions.eventSessionUsers.user',
            'eventSessions.eventSessionUsers.foodPreferences',
            'eventSessions.eventSessionUsers.drinkPreferences',
            'eventSessions.eventSessionUsers.foodAllergies',
            'eventSessions.eventSessionGuests.user',
            'eventSessions.eventSessionGuests.foodPreferences',
            'eventSessions.eventSessionGuests.drinkPreferences',
            'eventSessions.eventSessionGuests.foodAllergies'
        ])->findOrFail($this->event->id);

        $spreadsheet = new Spreadsheet();

        // Remove default worksheet
        $spreadsheet->removeSheetByIndex(0);

        // Create a Summary sheet
        $this->createSummarySheet($spreadsheet, $event);

        // Create a sheet for each session
        foreach ($event->eventSessions as $session) {
            $this->createSessionSheet($spreadsheet, $session, $event);
        }

        // Create a Costing sheet
        $this->createCostingSheet($spreadsheet, $event);

        // Set the first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        $filename = sprintf(
            '%s_registrations_%s.xlsx',
            \Illuminate\Support\Str::slug($event->title->name),
            now()->format('Y-m-d')
        );

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function createCostingSheet(Spreadsheet $spreadsheet, $event): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Costing');

        // Sheet header
        $sheet->setCellValue('A1', 'Event Costing Summary');
        $sheet->setCellValue('A2', 'Event Name:');
        $sheet->setCellValue('B2', $event->title->name);
        $sheet->setCellValue('A3', 'Generated:');
        $sheet->setCellValue('B3', now()->format('M d, Y g:i A'));

        $row = 5;

        // Add costing headers
        $sheet->setCellValue('A' . $row, 'Attendee Name');
        $sheet->setCellValue('B' . $row, 'Email');
        $sheet->setCellValue('C' . $row, 'Type');
        $sheet->setCellValue('D' . $row, 'Session');
        $sheet->setCellValue('E' . $row, 'Date');
        $sheet->setCellValue('F' . $row, 'Grant Applied');
        $sheet->setCellValue('G' . $row, 'Cost');
        $sheet->setCellValue('H' . $row, 'Final Amount');
        $row++;

        $totalCost = 0;
        $totalFinalAmount = 0;

        foreach ($event->eventSessions as $session) {
            // Process registered users (eligible for grants)
            foreach ($session->eventSessionUsers as $sessionUser) {
                $user = $sessionUser->user;

                // Assume these fields exist on the event or session model
                // You may need to adjust these based on your actual database structure
                $baseCost = $event->cost ?? $session->cost ?? 0;
                $grantAmount = $event->grant ?? $session->grant ?? 0;
                $finalAmount = max(0, $baseCost - $grantAmount);

                $sheet->setCellValue('A' . $row, $user->name);
                $sheet->setCellValue('B' . $row, $user->email);
                $sheet->setCellValue('C' . $row, 'Registered User');
                $sheet->setCellValue('D' . $row, $session->name);
                $sheet->setCellValue('E' . $row, $session->start_date->format('M d, Y'));
                $sheet->setCellValue('F' . $row, $grantAmount > 0 ? 'Yes' : 'No');
                $sheet->setCellValue('G' . $row, '£' . number_format($baseCost, 2));
                $sheet->setCellValue('H' . $row, '£' . number_format($finalAmount, 2));

                $totalCost += $baseCost;
                $totalFinalAmount += $finalAmount;
                $row++;
            }

            // Process guests (not eligible for grants)
            foreach ($session->eventSessionGuests as $guest) {
                $baseCost = $event->cost ?? $session->cost ?? 0;
                $finalAmount = $baseCost; // No grant applied to guests

                $sheet->setCellValue('A' . $row, $guest->name);
                $sheet->setCellValue('B' . $row, $guest->email);
                $sheet->setCellValue('C' . $row, 'Guest');
                $sheet->setCellValue('D' . $row, $session->name);
                $sheet->setCellValue('E' . $row, $session->start_date->format('M d, Y'));
                $sheet->setCellValue('F' . $row, 'No');
                $sheet->setCellValue('G' . $row, '£' . number_format($baseCost, 2));
                $sheet->setCellValue('H' . $row, '£' . number_format($finalAmount, 2));

                $totalCost += $baseCost;
                $totalFinalAmount += $finalAmount;
                $row++;
            }
        }

        // Add totals
        $row += 2;
        $sheet->setCellValue('F' . $row, 'Total Cost:');
        $sheet->setCellValue('G' . $row, '£' . number_format($totalCost, 2));
        $sheet->setCellValue('H' . $row, '£' . number_format($totalFinalAmount, 2));

        $row++;
        $sheet->setCellValue('F' . $row, 'Total Grant Applied:');
        $sheet->setCellValue('G' . $row, '£' . number_format($totalCost - $totalFinalAmount, 2));

        // Style the headers
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $sheet->getStyle('F' . ($row - 1) . ':H' . $row)->getFont()->setBold(true);

        // Auto-size columns
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }


    private function createSummarySheet(Spreadsheet $spreadsheet, $event): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Summary');

        // Event info
        $sheet->setCellValue('A1', 'Event Summary');
        $sheet->setCellValue('A2', 'Event Name:');
        $sheet->setCellValue('B2', $event->title->name);
        $sheet->setCellValue('A3', 'Event Dates:');
        $sheet->setCellValue('B3', $event->start_date->format('M d, Y') . ' - ' . $event->end_date->format('M d, Y'));
        $sheet->setCellValue('A4', 'Venue:');
        $sheet->setCellValue('B4', $event->venue->name);
        $sheet->setCellValue('A5', 'Generated:');
        $sheet->setCellValue('B5', now()->format('M d, Y g:i A'));

        // Session summary
        $row = 7;
        $sheet->setCellValue('A' . $row, 'Session Summary');
        $row++;

        $sheet->setCellValue('A' . $row, 'Session Name');
        $sheet->setCellValue('B' . $row, 'Date');
        $sheet->setCellValue('C' . $row, 'Time');
        $sheet->setCellValue('D' . $row, 'Location');
        $sheet->setCellValue('E' . $row, 'Registered Users');
        $sheet->setCellValue('F' . $row, 'Guests');
        $sheet->setCellValue('G' . $row, 'Total Attendees');
        $row++;

        foreach ($event->eventSessions as $session) {
            $userCount = $session->eventSessionUsers->count();
            $guestCount = $session->eventSessionGuests->count();

            $sheet->setCellValue('A' . $row, $session->name);
            $sheet->setCellValue('B' . $row, $session->start_date->format('M d, Y'));
            $sheet->setCellValue('C' . $row, Carbon::parse($session->start_time)->format('g:i A') . ' - ' . Carbon::parse($session->end_time)->format('g:i A'));
            $sheet->setCellValue('D' . $row, $session->location);
            $sheet->setCellValue('E' . $row, $userCount);
            $sheet->setCellValue('F' . $row, $guestCount);
            $sheet->setCellValue('G' . $row, $userCount + $guestCount);
            $row++;
        }

        // Style the headers
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A7')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A8:G8')->getFont()->setBold(true);

        // Auto-size columns
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }


    private function createSessionSheet(Spreadsheet $spreadsheet, $session, $event): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheetTitle = substr($session->name . ' - ' . $session->start_date->format('M d'), 0, 31);
        $sheet->setTitle($sheetTitle);

        // Session info
        $sheet->setCellValue('A1', 'Session: ' . $session->name);
        $sheet->setCellValue('A2', 'Date: ' . $session->start_date->format('M d, Y'));
        $sheet->setCellValue('A3', 'Time: ' . Carbon::parse($session->start_time)->format('g:i A') . ' - ' . Carbon::parse($session->end_time)->format('g:i A'));
        $sheet->setCellValue('A4', 'Location: ' . $session->location);

        $row = 6;

        // Registered Users section
        $sheet->setCellValue('A' . $row, 'Registered Users');
        $row++;

        if ($session->eventSessionUsers->count() > 0) {
            $sheet->setCellValue('A' . $row, 'Name');
            $sheet->setCellValue('B' . $row, 'Email');
            $sheet->setCellValue('C' . $row, 'Food Preferences');
            $sheet->setCellValue('D' . $row, 'Drink Preferences');
            $sheet->setCellValue('E' . $row, 'Allergies');
            $row++;

            foreach ($session->eventSessionUsers as $sessionUser) {
                $user = $sessionUser->user;
                $sheet->setCellValue('A' . $row, $user->name);
                $sheet->setCellValue('B' . $row, $user->email);
                $sheet->setCellValue('C' . $row, $sessionUser->foodPreferences->pluck('name')->join(', '));
                $sheet->setCellValue('D' . $row, $sessionUser->drinkPreferences->pluck('name')->join(', '));
                $sheet->setCellValue('E' . $row, $sessionUser->foodAllergies->pluck('name')->join(', '));
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'No registered users');
            $row++;
        }

        $row++; // Empty row

        // Guests section
        if ($session->allow_guests) {
            $sheet->setCellValue('A' . $row, 'Guests');
            $row++;

            if ($session->eventSessionGuests->count() > 0) {
                $sheet->setCellValue('A' . $row, 'Guest Name');
                $sheet->setCellValue('B' . $row, 'Guest Email');
                $sheet->setCellValue('C' . $row, 'Brought by');
                $sheet->setCellValue('D' . $row, 'Food Preferences');
                $sheet->setCellValue('E' . $row, 'Drink Preferences');
                $sheet->setCellValue('F' . $row, 'Allergies');
                $row++;

                foreach ($session->eventSessionGuests as $guest) {
                    $sheet->setCellValue('A' . $row, $guest->name);
                    $sheet->setCellValue('B' . $row, $guest->email);
                    $sheet->setCellValue('C' . $row, $guest->user->name);
                    $sheet->setCellValue('D' . $row, $guest->foodPreferences->pluck('name')->join(', '));
                    $sheet->setCellValue('E' . $row, $guest->drinkPreferences->pluck('name')->join(', '));
                    $sheet->setCellValue('F' . $row, $guest->foodAllergies->pluck('name')->join(', '));
                    $row++;
                }
            } else {
                $sheet->setCellValue('A' . $row, 'No guests registered');
                $row++;
            }
        }

        // Style the headers
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12);

        // Find and style the user headers
        $userHeaderRow = 7;
        if ($session->eventSessionUsers->count() > 0) {
            $sheet->getStyle('A' . $userHeaderRow . ':E' . $userHeaderRow)->getFont()->setBold(true);
        }

        // Find and style the guest headers
        $guestHeaderStart = $userHeaderRow + $session->eventSessionUsers->count() + 3;
        if ($session->allow_guests) {
            $sheet->getStyle('A' . $guestHeaderStart)->getFont()->setBold(true)->setSize(12);
            if ($session->eventSessionGuests->count() > 0) {
                $guestDataHeaderRow = $guestHeaderStart + 1;
                $sheet->getStyle('A' . $guestDataHeaderRow . ':F' . $guestDataHeaderRow)->getFont()->setBold(true);
            }
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }


    public function getSessionRegistrationCount(int $sessionId): int
    {
        $session = $this->eventSessions->firstWhere('id', $sessionId);

        if (!$session) return 0;

        return $session->eventSessionUsers->count() + $session->eventSessionGuests->count();
    }

    public function isRegisteredForSession(int $sessionId): bool
    {
        return in_array($sessionId, $this->selectedSessions, true);
    }

    public function canAddGuests(int $sessionId): bool
    {
        $session = $this->eventSessions->firstWhere('id', $sessionId);

        return $session && $session->allow_guests && $this->isRegisteredForSession($sessionId);
    }
};
?>

{{-- *******Blade component starts here******* --}}
<div
    class="flex flex-col mx-auto max-w-7xl translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750 space-y-6">
    {{-- *******Event name******* --}}
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <flux:heading size="xl">{{ $event->title->name }}</flux:heading>
        </div>

        <div>
            {{-- Add this after the event description, before the separator --}}
            @if(auth()->check() && auth()->id() === $event->user_id)
                <div>
                    <flux:button
                        wire:click="exportEventData"
                        variant="filled"
                        icon="arrow-down-tray"
                        size="sm"
                    >
                        Export
                    </flux:button>
                </div>
            @endif
        </div>
    </div>

    {{-- *******Event card layout******* --}}
    <flux:card>
        <div class="grid grid-cols-1 md:grid-cols-3 items-center justify-between gap-4 mb-6">
            <div>
                @if($event->rsvp_closes_at->isFuture())
                    <flux:badge icon="arrow-right-end-on-rectangle" size="sm" color="green" variant="solid">
                        RSVP Open
                    </flux:badge>
                @else
                    <flux:badge icon="arrow-left-start-on-rectangle" size="sm" color="red" variant="solid">
                        RSVP Closed
                    </flux:badge>
                @endif
                <div class="space-y-4 mt-6">
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

                    <div>
                        @if($event->categories->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-6">
                                @foreach($event->categories as $category)
                                    <flux:badge size="sm"
                                                color="{{ $category->colour }}">{{ $category->name }}</flux:badge>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-col justify-center">
                <div class="justify-self-center items-center space-y-4">
                    <div class="flex">
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
                        <flux:icon.clock class="mr-3 w-5 h-5"/>
                        <div>
                            <flux:heading size="sm" class="font-medium">RSVP Closes</flux:heading>
                            <flux:text size="sm">{{ $event->rsvp_closes_at->format('M d, Y g:i A') }}</flux:text>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
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
        </div>

        <div class="my-4">
            <flux:separator variant="subtle"/>
        </div>

        <div>
            <flux:accordion transition>
                <flux:accordion.item>
                    <flux:accordion.heading size="lg">Event Details</flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:text class="prose max-w-none text-sm">
                            {!! $event->description !!}
                        </flux:text>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
        </div>

        <div class="my-4">
            <flux:separator/>
        </div>

        <div class="my-5">
            <flux:heading size="lg">Event Sessions</flux:heading>
        </div>

        {{-- **********Event Sessions********** --}}
        <div class="grid grid-cols-1 space-y-4 justify-between">
            @forelse($eventSessions as $session)
                <flux:card class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3">
                        <div class="grid grid-cols-1 space-y-4">
                            <div>
                                <flux:badge size="sm" color="amber" variant="solid">
                                    Session {{ $session->id }}
                                </flux:badge>
                            </div>

                            <div class="flex-1 space-y-4">
                                <div>
                                    <flux:heading size="sm">Name</flux:heading>
                                    <flux:text size="sm">{{ $event->title->name }}</flux:text>
                                </div>

                                <div>
                                    <div class="flex items-center gap-2">
                                        <flux:icon.calendar class="w-4 h-4 text-zinc-700 dark:text-zinc-400"/>
                                        <flux:heading size="sm" class="font-medium">Date</flux:heading>
                                        <flux:text size="sm">{{ $session->start_date->format('D d M Y') }}</flux:text>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center gap-2">
                                        <flux:icon.clock class="w-4 h-4 text-zinc-700 dark:text-zinc-400"/>
                                        <flux:heading size="sm" class="font-medium">Timings</flux:heading>
                                        <flux:text size="sm">
                                            {{ Carbon::parse($session->start_time)->format('g:i A') }} -
                                            {{ Carbon::parse($session->end_time)->format('g:i A') }}
                                        </flux:text>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center gap-2">
                                        <flux:icon.map-pin class="w-4 h-4 text-zinc-700 dark:text-zinc-400"/>
                                        <flux:heading size="sm" class="font-medium">Venue</flux:heading>
                                        <flux:text size="sm">{{ $session->location }}</flux:text>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 my-4">
                        <flux:separator variant="subtle"/>
                    </div>

                    <div class="grid grid-cols-1">
                        <flux:heading size="sm" class="font-medium">Description</flux:heading>
                        <flux:text size="sm">{{ $session->description }}</flux:text>
                    </div>

                    {{-- **********Member register area********** --}}
                    @auth
                        <div class="grid grid-cols-1 justify-end gap-4">
                            @if($event->rsvp_closes_at->isFuture())
                                <div class="flex flex-row items-center justify-end gap-4">
                                    <div>
                                        @if($this->canAddGuests($session->id))
                                            @php
                                                $currentGuestCount = isset($guests[$session->id]) ? count($guests[$session->id]) : 0;
                                            @endphp
                                            <flux:button
                                                wire:click="openGuestModal({{ $session->id }})"
                                                variant="filled"
                                                icon="user-plus"
                                                size="sm"
                                                :disabled="!$this->canAddGuests($session->id) || $currentGuestCount >= 2"
                                            >
                                                Add
                                                Guest {{ $currentGuestCount >= 2 ? '(Limit: 2)' : "($currentGuestCount/2)" }}
                                            </flux:button>
                                        @endif
                                    </div>

                                    <div>
                                        <flux:button
                                            wire:click="toggleSessionRegistration({{ $session->id }})"
                                            variant="{{ $this->isRegisteredForSession($session->id) ? 'danger' : 'primary' }}"
                                            icon="{{ $this->isRegisteredForSession($session->id) ? 'x-mark' : 'check' }}"
                                            size="sm"
                                            :disabled="!$this->isRegisteredForSession($session->id) && $session->capacity && $registrationCount >= $session->capacity"
                                        >
                                            {{ $this->isRegisteredForSession($session->id) ? 'Unregister' : 'Register' }}
                                        </flux:button>
                                    </div>
                                </div>
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

                    {{-- Only show if user is registered --}}
                    @if($this->isUserRegisteredForSession(auth()->user(), $session->id))
                        <div class="flex items-center justify-end gap-4 mb-4">
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

                        {{-- ********Costings******** --}}
                        <div class="grid grid-cols-1 my-6">
                            <div>
                                @if($session['cost'])
                                    <flux:text>
                                        Cost to members £{{ $session['cost'] - $session['grant'] }}
                                    </flux:text>

                                    <flux:text>
                                        Cost to guests £{{ $session['cost'] }}
                                    </flux:text>
                                @endif
                            </div>
                        </div>


                        <div class="grid grid-cols-1">
                            <flux:accordion transition>
                                <flux:accordion.item>
                                    <flux:accordion.heading>Your preferences</flux:accordion.heading>
                                    <flux:accordion.content>
                                        <flux:text size="sm" class="text-sm">
                                            If this session includes catering, please select from the option below if
                                            any
                                            apply
                                            to you.
                                            If you have any other preferences, please let us know.
                                        </flux:text>
                                        <div class="grid grid-cols-1 lg:grid-cols-3 justify-between gap-4 mt-4">
                                            <div>
                                                <flux:card>
                                                    <flux:checkbox.group>
                                                        <flux:text size="sm" class="font-medium mb-2">Food Preferences
                                                        </flux:text>
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
                                                    </flux:checkbox.group>
                                                </flux:card>
                                            </div>

                                            <div>
                                                <flux:card>
                                                    <flux:checkbox.group>
                                                        <flux:text size="sm" class="font-medium mb-2">Drink Preferences
                                                        </flux:text>
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
                                                    </flux:checkbox.group>
                                                </flux:card>
                                            </div>

                                            <div>
                                                <flux:card>
                                                    <flux:checkbox.group>
                                                        <flux:text size="sm" class="font-medium mb-2">Food Allergies
                                                        </flux:text>
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
                                                    </flux:checkbox.group>
                                                </flux:card>
                                            </div>

                                        </div>

                                    </flux:accordion.content>
                                </flux:accordion.item>

                                <flux:accordion.item>
                                    <flux:accordion.heading>Registered Users</flux:accordion.heading>
                                    <flux:accordion.content>
                                        @if($session->eventSessionUsers->count() > 0)
                                            <div>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($session->eventSessionUsers as $sessionUser)
                                                        <flux:badge size="sm"
                                                                    icon="user">{{ $sessionUser->user->name }}</flux:badge>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </flux:accordion.content>
                                </flux:accordion.item>

                                @if($session->allow_guests)
                                    <flux:accordion.item>
                                        <flux:accordion.heading>Guests</flux:accordion.heading>
                                        <flux:accordion.content>
                                            <div>
                                                <!-- Guests List -->
                                                @if(isset($guests[$session->id]) && count($guests[$session->id]) > 0)
                                                    <div>
                                                        <flux:separator variant="subtle" class="my-4"/>
                                                        <flux:heading class="mb-3">Your Guests</flux:heading>
                                                        <div class="space-y-4">
                                                            @if(isset($guests[$session->id]))
                                                                @foreach($guests[$session->id] as $guest)
                                                                    <flux:card
                                                                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                                        <div>
                                                                            <flux:text
                                                                                size="md">{{ $guest['name'] }}</flux:text>
                                                                            <flux:link
                                                                                href="mailto:{{ $guest['email'] }}">{{ $guest['email'] }}</flux:link>
                                                                        </div>
                                                                        <div class="flex space-x-2">
                                                                            <flux:button variant="filled" size="sm"
                                                                                         color="teal"
                                                                                         wire:click="openEditGuestModal({{ $session->id }}, {{ $guest['id'] }})"
                                                                            >
                                                                                Edit
                                                                            </flux:button>
                                                                            <flux:button variant="danger" size="sm"
                                                                                         wire:click="removeGuest({{ $session->id }}, {{ $guest['id'] }})"
                                                                            >
                                                                                Remove
                                                                            </flux:button>
                                                                        </div>
                                                                    </flux:card>
                                                                @endforeach
                                                            @endif

                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                @endif
                            </flux:accordion>
                        </div>
                    @endif
                </flux:card>
            @empty
                <div class="text-center py-8">
                    <flux:icon name="calendar" class="w-12 h-12 mx-auto text-zinc-400 mb-4"/>
                    <flux:text>No sessions available for this event.</flux:text>
                </div>
            @endforelse
        </div>

    </flux:card>

    <!-- **********Guest Modal********** -->
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
                            <flux:text>Food Preferences</flux:text>
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
                            <flux:text>Drink Preferences</flux:text>
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
                            <flux:text>Allergies</flux:text>
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

    <!-- **********Edit Guest Modal********** -->
    <flux:modal name="edit-guest-modal" wire:model="showEditGuestModal">
        <form wire:click="updateGuest">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Edit Guest</flux:heading>
                    <flux:text>Edit guest requirements</flux:text>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:label>Guest Name</flux:label>
                        <flux:input wire:model="guestName" placeholder="Enter guest name"/>
                        <flux:error name="guestName"/>
                    </div>

                    <div>
                        <flux:label>Guest Email</flux:label>
                        <flux:input wire:model="guestEmail" type="email" placeholder="Enter guest email"/>
                        <flux:error name="guestEmail"/>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div>
                        <flux:label>Food Preferences</flux:label>
                        <div class="space-y-1 max-h-32 overflow-y-auto">
                            @foreach($foodPreferences as $preference)
                                <flux:checkbox
                                    wire:model="guestFoodPreferences"
                                    value="{{ $preference->id }}"
                                >
                                    {{ $preference->name }}
                                </flux:checkbox>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <flux:label>Drink Preferences</flux:label>
                        <div class="space-y-1 max-h-32 overflow-y-auto">
                            @foreach($drinkPreferences as $preference)
                                <flux:checkbox
                                    wire:model="guestDrinkPreferences"
                                    value="{{ $preference->id }}"
                                >
                                    {{ $preference->name }}
                                </flux:checkbox>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <flux:label>Food Allergies</flux:label>
                        <div class="space-y-1 max-h-32 overflow-y-auto">
                            @foreach($foodAllergies as $allergy)
                                <flux:checkbox
                                    wire:model="guestAllergies"
                                    value="{{ $allergy->id }}"
                                >
                                    {{ $allergy->name }}
                                </flux:checkbox>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <flux:button variant="ghost" wire:click="closeEditGuestModal">
                        Cancel
                    </flux:button>
                    <flux:button type="submit">
                        Update Guest
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>


    <!-- **********Back Button********** -->
    <div class="flex justify-start">
        <flux:button href="{{ route('events.index') }}" variant="filled" icon="arrow-left">
            Back to Events
        </flux:button>
    </div>
</div>
