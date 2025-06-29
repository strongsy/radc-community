<?php

use App\Models\Event;
use App\Models\Title;
use App\Models\Venue;
use App\Models\Category;
use App\Models\EventSession;
use App\Models\FoodAllergy;
use App\Models\DrinkPreference;
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

    public function mount(): void
    {
        $this->eventTypes = Title::orderBy('name')->get();
        $this->eventLocations = Venue::orderBy('name')->get();
        $this->eventCategories = Category::orderBy('name')->get();
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
        ];
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () {
            $event = $this->createEvent();
            $this->attachCategories($event);
        });
    }

    public function createEvent(): Event
    {
        return Event::create([
           'user_id' => auth()->id(),
           'title_id' => $this->type,
           'venue_id' => $this->venue,
           'description' => $this->description,
           'start_date' => $this->event_start_date,
           'end_date' => $this->event_end_date,
           'rsvp_closes_at' => $this->close_rsvp_at ,
        ]);
    }

    private function attachCategories(Event $event): void
    {
        $event->categories()->attach($this->categories);
    }
};
?>


{{-- Component layout --}}
<div class="flex flex-col space-y-6">
    <flux:card>
        <flux:heading size="xl">Create New Community Event</flux:heading>
    </flux:card>

    <form wire:submit.prevent="save">
        <flux:card>
            <div class="mb-6">
                <flux:heading class="text-sm md:text-lg">Event Details</flux:heading>
                <flux:text variant="subtle" class="text-xs md:text-md">
                    Inform your potential attendees what this event is about; you can add more detail in the next
                    section.
                </flux:text>
            </div>

            <flux:separator variant="subtle" class="mb-6"/>

            <flux:fieldset>
                <div class="grid 2xl:grid-cols-3 gap-x-4 gap-y-6">
                    <div>
                        <flux:select required variant="listbox" searchable placeholder="Choose type..."
                                     label="Event type"
                                     wire:model.lazy="type">
                            @foreach ($eventTypes as $type)
                                <flux:select.option value="{{ $type->id }}">{{ $type->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div>
                        <flux:select variant="listbox" searchable placeholder="Choose venue..." label="Event venue"
                                     wire:model.lazy="venue">
                            @foreach ($eventLocations as $location)
                                <flux:select.option
                                    value="{{ $location->id }}">{{ $location->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div>
                        <flux:select variant="listbox" multiple searchable placeholder="Choose categories..."
                                     label="Event categories"
                                     wire:model.lazy="categories">
                            @foreach ($eventCategories as $category)
                                <flux:select.option
                                    value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <div class="grid grid-cols-1 mt-6">
                    <flux:editor wire:model.lazy="description" label="Event description"
                                 class="**:data-[slot=content]:min-h-[100px]!"/>
                </div>

                <div class="grid 2xl:grid-cols-3 gap-4 space-y-6 mt-6">
                    <div>
                        <flux:date-picker wire:model.lazy="event_start_date" label="Start date"
                                          class="**:data-[slot=content]"/>
                    </div>

                    <div>
                        <flux:date-picker wire:model.lazy="event_end_date" label="End date"
                                          class="**:data-[slot=content]"/>
                    </div>

                    <div>
                        <flux:date-picker wire:model.lazy="close_rsvp_at" label="Close RSVP at"
                                          class="**:data-[slot=content]"/>
                    </div>

                </div>
            </flux:fieldset>
        </flux:card>

        <div class="flex justify-end mt-6 gap-4">
            <flux:button type="button" variant="filled" wire:click="{{ route('events.index') }}">Cancel</flux:button>
            <flux:button type="submit" variant="primary">Save</flux:button>
        </div>
    </form>
</div>


