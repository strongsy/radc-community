<?php

use App\Models\Story;
use App\Traits\WithSortingAndSearching;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination, WithSortingAndSearching;

    protected function applySearchFilters($query): mixed
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->whereHas('author', function ($userQuery) {
                $userQuery->where('name', 'like', '%' . $this->search . '%');
            })
                ->orWhere('article_title', 'like', '%' . $this->search . '%');
        });

    }

    public function with(): array
    {
        $query = Story::query()->with('author')->where('is_approved', true);

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(10);

        return [
            'stories' => $paginated,
        ];
    }
}; ?>

<div class="flex flex-col gap-5 w-full">
    <div>
        <div class="relative w-full">
            <flux:heading size="xl" level="1">{{ __('Stories') }}</flux:heading>
            <flux:subheading
                size="lg">{{ __('Stories created users.') }}</flux:subheading>
        </div>
    </div>

    <!-- search field -->
    <div class="flex flex-1/2 items-center justify-between">
        <div class="flex items-center">
            <flux:input icon="magnifying-glass" placeholder="Search..." type="text" class="w-full"
                        wire:model.live.debounce.500ms="search"/>
        </div>

    </div>

    <x-search-and-sort
        :search="$search"
        :sortBy="$sortBy"
        :sortDirection="$sortDirection"
    />

    <flux:separator variant="subtle"/>

    <flux:table :pagination="$stories">
        <flux:table.columns>
            <flux:table.column>Author</flux:table.column>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>created_at</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($stories as $story)
                <flux:table.row>
                    <flux:table.cell>{{ $story->author->name ?? 'Unknown' }}</flux:table.cell>
                    <flux:table.cell>{{ $story->story_title ?? 'Unknown' }}</flux:table.cell>
                    <flux:table.cell>{{ $story->created_at->format('d M Y, g:i A') ?? 'No date' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>
                                @can('event-read')
                                    <flux:menu.item icon="user-plus" wire:click="update({{ $user->id ?? 'N/A' }})"
                                                    wire:confirm="Are you sure you want to activate this user?">
                                        Activate
                                    </flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.cell colspan="8">
                    <div class="flex w-full justify-center items-center gap-3">
                        <flux:badge size="xl" color="teal" variant="subtle" class="my-3">
                            <flux:heading size="lg">No Stories Found...</flux:heading>
                        </flux:badge>
                    </div>
                </flux:table.cell>
            @endforelse

        </flux:table.rows>
    </flux:table>
</div>
