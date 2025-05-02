@props([
    'search' => null,
    'sortBy' => null,
    'sortDirection' => null,
])

<div>
    <div class="flex space-x-2">
        {{-- Render search badge if applicable --}}
        @if($search)
            <flux:badge
            color="teal"
            wire:click="clearFilter('search')"
            variant="ghost">
                {{ $search }} <flux:badge.close />
            </flux:badge>
        @endif

        {{-- Render sort badge if applicable --}}
        @if($sortBy)
            <flux:badge
                color="yellow"
                wire:click="clearFilter('sort')"
                variant="ghost">
                {{ ucfirst($sortBy) ?? 'asc' }} ({{ ucfirst($sortDirection) ?? 'asc' }}) <flux:badge.close />
            </flux:badge>
        @endif
    </div>
</div>
