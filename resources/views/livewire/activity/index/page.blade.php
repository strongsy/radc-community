<?php

use App\Exports\ActivitiesExport;
use App\Traits\WithSortingAndSearching;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

new class extends Component {
    use WithPagination, WithSortingAndSearching;

    protected function applySearchFilters($query): mixed
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('description', 'like', '%' . $this->search . '%')
                ->orWhere('event', 'like', '%' . $this->search . '%');
        });
    }

    public function export($ext)
    {

        return (new ActivitiesExport())->download('activities.' . $ext);

        //return Excel::download(new ActivitiesExport(), 'activities.' . $ext);

    }

    /*public function selectedActivity(): void
    {
        return $this->selectedActivity();
    }*/

    public function with(): array
    {
        $query = Activity::query()->with('causer');

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(10);

        return [
            'activities' => $paginated,
        ];
    }
}; ?>

<div class="flex flex-col gap-5 w-full">
    <div>
        <div class="relative w-full">
            <flux:heading size="xl" level="1">{{ __('Activities') }}</flux:heading>
            <flux:subheading
                size="lg">{{ __('List of monitored user activities. To view a comprehensive list, please download.') }}</flux:subheading>
        </div>
    </div>

    <!-- search field -->
    <div class="flex flex-1/2 items-center justify-between">
        <div class="flex items-center">
            <flux:input icon="magnifying-glass" placeholder="Search..." type="text" class="w-full"
                        wire:model.live.debounce.500ms="search"/>
        </div>

        <div class="flex items-center">
            <form wire:submit.prevent="export('xlsx')">
                <flux:button icon="arrow-down-tray" variant="primary" type="submit">Download</flux:button>
            </form>

        </div>
    </div>

    <x-search-and-sort
        :search="$search"
        :sortBy="$sortBy"
        :sortDirection="$sortDirection"
    />

    <flux:separator variant="subtle"/>

    <flux:table :paginate="$activities">
        <flux:table.columns>

            <flux:table.column>Select</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'log_name'" :direction="$sortDirection"
                               wire:click="sort('log_name')">Name
            </flux:table.column>

            <flux:table.column>Description</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'event'" :direction="$sortDirection"
                               wire:click="sort('event')">Event
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'causer_id'" :direction="$sortDirection"
                               wire:click="sort('causer_id')">Causer
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                               wire:click="sort('created_at')">Created at
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'causer_type'" :direction="$sortDirection"
                               wire:click="sort('causer_type')">Type
            </flux:table.column>

        </flux:table.columns>

        <flux:table.rows>
            @forelse($activities as $activity)
                <flux:table.row :key="$activity->id">
                    <flux:table.cell>
                        <flux:checkbox wire:model="selectedActivity" :value="$activity->id"/>
                    </flux:table.cell>
                    <flux:table.cell>{{ $activity->log_name ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $activity->description ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $activity->event ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $activity->causer->name ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>{{ $activity->created_at->format('d M Y H:i:s') }}</flux:table.cell>
                    <flux:table.cell>{{ $activity->causer_type ?? 'None' }}</flux:table.cell>

                </flux:table.row>
            @empty
                <flux:table.cell colspan="8">
                    <div class="flex w-full justify-center items-center gap-3">
                        <flux:badge size="xl" color="teal" variant="subtle" class="my-3">
                            <flux:heading size="lg">No Activity Found...</flux:heading>
                        </flux:badge>
                    </div>
                </flux:table.cell>
            @endforelse
        </flux:table.rows>
    </flux:table>

</div>
