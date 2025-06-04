<?php /** @noinspection ALL */

namespace App\Livewire\Forms;

use App\Models\CategoryEvent;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Flux\Flux;
use Livewire\Form;


class EventForm extends Form
{

    protected ?Carbon $eventDateTime = null;

    // constants for validation and event creation
    private const MIN_EVENT_DAYS_AHEAD = 14;

    private const MAX_GUESTS_ALLOWED = 4;

    private const MIN_CONTENT_LENGTH = 10;

    private const EVENT_CLOSE_DAYS = 7;

    public ?Event $event;


    #[Rule('required|integer')]
    public ?int $user_id = null;
    #[Rule('required')]
    public ?string $title_id = '';
    #[Rule('required')]
    public ?string $event_content = null;
    #[Rule('required')]
    public ?string $event_date = null;
    #[Rule('required|date_format:H:i')]
    public ?string $event_time = null;
    #[Rule('required')]
    public ?string $venue_id = '';
    #[Rule('required')]
    public ?array $category_id = [];
    public ?int $status_id = null;
    public bool $allow_guests = false;
    #[Rule('nullable|integer|min:1|max:4')]
    public ?int $max_guests = null;
    #[Rule('nullable|integer|min:1')]
    public ?int $max_attendees = null;
    #[Rule('nullable|numeric|decimal:2|min:0|max:9999.99')]
    public ?string $user_cost = null;
    #[Rule('nullable|numeric|decimal:2|min:0|max:9999.99')]
    public ?string $guest_cost = null;
    public ?string $cover_img = null;
    public ?string $closes_at = null;
    public ?string $expires_at = null;

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'title_id' => ['required', 'exists:event_titles,id'],
            'event_content' => [
                'required|string|min:3',
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        $fail('The event content is required.');
                        return;
                    }
                    // Strip HTML tags to count actual content length
                    $plainText = strip_tags($value);
                    $trimmedLength = strlen(trim($plainText));

                    if ($trimmedLength < self::MIN_CONTENT_LENGTH) {
                        $fail("The event content must be at least " . self::MIN_CONTENT_LENGTH . " characters long.");
                    }
                    if (strlen($value) > 10000) {
                        $fail('The event content must not exceed 10000 characters.');
                    }
                }

            ],
            'event_date' => ['required', 'date', 'date_format:Y-m-d', 'after:' . now()->addDays(self::MIN_EVENT_DAYS_AHEAD)->format('Y-m-d')],
            'event_time' => 'required|date_format:H:i',
            'venue_id' => 'required|exists:venues,id',
            'category_id' => 'required|array|min:1',
            'category_id.*' => 'exists:event_categories,id',
            'status_id' => 'nullable|integer',
            'allow_guests' => 'boolean',
            'max_guests' => ['required', 'exclude_unless:allow_guests,true', 'integer', 'min:1', 'max:' . self::MAX_GUESTS_ALLOWED],
            'max_attendees' => [
                'nullable',
                'integer',
                'min:1',
                'required_with:user_cost,guest_cost'
            ],

            'user_cost' => [
                'nullable',
                'numeric',
                'decimal:2',
                'min:0',
                'max:999999.99',
                'regex:/^\d+(\.\d{0,2})?$/',
            ],

            'guest_cost' => [
                'nullable',
                'numeric',
                'decimal:2',
                'min:0',
                'max:999999.99',
                'regex:/^\d+(\.\d{0,2})?$/',
            ],

            'cover_img' => 'nullable',
            'closes_at' => 'nullable|date',
            'expires_at' => 'nullable|date'

        ];
    }


    public function messages(): array
    {
        return [
            //'max_attendees.required_with' => 'Maximum attendees is required when setting a cost.',
            'user_cost.decimal' => 'The user cost must have 2 or fewer decimal places.',
            'user_cost.decimal' => 'The user cost must have 2 or fewer decimal places.',
            'event_content.required' => 'The event content is required.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'venue_id.required' => 'Please select a venue.',
            'venue_id.exists' => 'The selected venue is invalid.',
            'content.required' => 'The event content is required.',
            'content.min' => 'The event content must be at least 3 characters.',

        ];
    }

    public function setEvent(Event $event): void
    {
        $this->event = $event;

        $this->title_id = $event->title_id;
        $this->event_content = $event->event_content;
        $this->event_date = $event->event_date;
        $this->event_time = $event->event_time;
        $this->venue_id = $event->venue_id;
        $this->category_id= $event->categories->pluck('id')->toArray();
        $this->status_id = $event->status_id;
        $this->allow_guests = $event->allow_guests;
        $this->max_guests = $event->max_guests;
        $this->max_attendees = $event->max_attendees;
        $this->user_cost = $event->user_cost;
        $this->guest_cost = $event->guest_cost;
        $this->cover_img = $event->cover_img;
        $this->closes_at = $event->closes_at;
        $this->expires_at = $event->expires_at;
    }

    // Add method to update eventDateTime
    protected function updateEventDateTime(): void
    {
        if ($this->event_date && $this->event_time) {
            try {
                $dateTimeString = "{$this->event_date} {$this->event_time}";
                $this->eventDateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTimeString);

                if (!$this->eventDateTime->isValid()) {
                    throw new \RuntimeException('Invalid date/time format');
                }
            } catch (\Exception $e) {
                $this->eventDateTime = null;
                throw new \RuntimeException('Invalid event date/time format: ' . $e->getMessage());
            }
        }
    }

    // Add updated hooks for date and time
    public function updated($property): void
    {
        if ($property === 'event_date' || $property === 'event_time') {
            $this->updateEventDateTime();
        }
    }

    //create method
    public function store(): void
    {
       if (Auth::user()->can('create-event')) {
           try {
               $this->status_id = 1;
               $this->user_id = Auth::id();
               $this->updateEventDateTime();
               $this->expires_at = $this->eventDateTime->addDay()->format('Y-m-d H:i:s');
               $this->closes_at = $this->eventDateTime->copy()->subDays(self::EVENT_CLOSE_DAYS)->format('Y-m-d H:i:s');
               $categoryIds = $this->category_id ?? [];
               dd($categoryIds);
               $this->validate();

               $event = Event::create($this->validate());

               if (!empty($categoryIds)) {
                   $event->categories()->attach($categoryId);
               }

           } catch (Exception $e) {
               Flux::toast(
                   text: 'An error occurred while creating the event.',
                   heading: 'Error',
                   variant: 'danger',
               );

           }
       } else {
           abort(403, 'You are not authorised to create events!');
       }
        //$this->validate();
        // First check if user is authenticated
        /*if (!Auth::check()) {
            Flux::toast(
                text: 'Your session has expired. Please login again.',
                heading: 'Authentication Error',
                variant: 'danger',
            );
            return;
        }

        $user = Auth::user();
        if (!$user) {
            Flux::toast(
                text: 'Unable to retrieve user information.',
                heading: 'Authentication Error',
                variant: 'danger',
            );
            return;
        }

        $this->status_id = 1;
        $this->expires_at = $this->eventDateTime->copy()->addDay()->format('Y-m-d H:i:s');
        $this->closes_at = $this->eventDateTime->copy()->subDays(self::EVENT_CLOSE_DAYS)->format('Y-m-d H:i:s');

        $data = array_merge($validated, [
            'user_id' => auth()->id(),
            'status_id' => $this->status_id,
            'expires_at' => $this->expires_at,
            'closes_at' => $this->closes_at
        ]);

        dd([
            'data_before_category_removal' => $data,
            'categoryIds' => $data['event_category_id'] ?? null
        ]);*/

        // Store the category IDs and remove from data array
        //$categoryIds = $data['event_category_id'] ?? [];
       // unset($data['event_category_id']);

        // Create the event
        //$event = Event::create($data);

        // Attach categories if any were selected
        /*if (!empty($categoryIds)) {
            $event->categories()->attach($categoryIds);
        }*/


        /*if (Auth::user()->can('create-event')) {
            try {
                $validated = $this->validate();
                dd([
                    'validated' => $validated,
                    'form' => $this->form->toArray()  // Assuming you're using a form object
                ]);

                $this->updateEventDateTime();

                if (!$this->eventDateTime) {
                    throw new \RuntimeException('Event date/time is required');
                }

                $this->status_id = 1;
                $this->expires_at = $this->eventDateTime->copy()->addDay()->format('Y-m-d H:i:s');
                $this->closes_at = $this->eventDateTime->copy()->subDays(self::EVENT_CLOSE_DAYS)->format('Y-m-d H:i:s');

                $data = array_merge($validated, [
                    'user_id' => auth()->id(),
                    'status_id' => $this->status_id,
                    'expires_at' => $this->expires_at,
                    'closes_at' => $this->closes_at
                ]);

                dd([
                    'data_before_category_removal' => $data,
                    'categoryIds' => $data['event_category_id'] ?? null
                ]);

                // Store the category IDs and remove from data array
                $categoryIds = $data['event_category_id'] ?? [];
                unset($data['event_category_id']);

                // Create the event
                $event = Event::create($data);

                // Attach categories if any were selected
                if (!empty($categoryIds)) {
                    $event->categories()->attach($categoryIds);
                }

                Flux::toast(
                    text: 'Event created successfully.',
                    heading: 'Success',
                    variant: 'success',
                );

            } catch (Exception $e) {
                dd([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                Flux::toast(
                    text: 'An error occurred while creating the event.',
                    heading: 'Error',
                    variant: 'danger',
                );
            }
        } else {
            abort(403, 'You are not authorised to create events!');
        }*/
    }

    public function update(): void
    {
        $validated = $this->validate();

        // Fix the property name to match event_category_id
        $categories = $validated['category_id'];
        unset($validated['category_id']);

        // Update the event
        $this->event->update($validated);

        // Sync the categories
        $this->event->categories()->sync($categories);

        Flux::toast(
            text: 'Event updated successfully.',
            heading: 'Success',
            variant: 'success',
        );

        $this->reset();
    }


}
