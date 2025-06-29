<?php

use App\Models\Post;
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
        $query = Post::query()->where('user_id', 2)->where('status_id', 2);

        // Apply search and order
        $filtered = $this->applySearchFilters($query);

        // Apply sorting filter if applicable
        $query = $this->applySorting($query);

        // Paginate the data
        $paginated = $query->paginate(10);

        return [
            'posts' => $paginated,
        ];
    }
}; ?>

<div class="flex flex-col gap-5 w-full">
    <div>
        <div class="relative w-full">
            <flux:heading size="xl" level="1">{{ __('Posts') }}</flux:heading>
            <flux:subheading
                size="lg">{{ __('Posts created users.') }}</flux:subheading>
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

    <flux:table :pagination="$posts">
        <flux:table.columns>
            <flux:table.column>Author</flux:table.column>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>created_at</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($posts as $post)
                <flux:table.row>
                    <flux:table.cell>{{ $post->user->name ?? 'Unknown' }}</flux:table.cell>
                    <flux:table.cell>{{ $post->post_title ?? 'Unknown' }}</flux:table.cell>
                    <flux:table.cell>{{ $post->created_at->format('d M Y, g:i A') ?? 'No date' }}</flux:table.cell>
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
                            <flux:heading size="lg">No Posts Found...</flux:heading>
                        </flux:badge>
                    </div>
                </flux:table.cell>
            @endforelse

        </flux:table.rows>
    </flux:table>
</div>
