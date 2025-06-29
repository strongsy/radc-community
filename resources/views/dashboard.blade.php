<x-layouts.app :title="__('Dashboard')">

    <flux:card class="md:flex mx-auto md:items-center max-w-7xl md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <flux:heading size="xl">
                Welcome to your dashboard {{ Auth::check() ? Auth::user()->getFirstNameAttribute() : null }}
            </flux:heading>
            <flux:text>
                Take a look around and start contributing to the community.
            </flux:text>
        </div>
    </flux:card>

    <div class="flex h-full mx-auto  max-w-7xl flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
