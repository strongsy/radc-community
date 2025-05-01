<div>
    <flux:container class="flex flex-col w-full px-0! md:flex-row mx-auto max-w-7xl items-center justify-between mt-5">
        <flux:container class="flex flex-col pl-0! justify-start w-full text-center mb-5 md:mb-0 md:flex-row">
            <flux:text>
                Copyright &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </flux:text>
        </flux:container>

        <flux:container class="flex justify-center text-center flex-col gap-x-3 w-full mb-5 md:mb-0 md:flex-row">
            <flux:link href="#" class="text-sm">Privacy Policy</flux:link>
        </flux:container>

        <flux:container class="flex flex-col lg:pr-0! w-full justify-end text-center mb-5 md:mb-0 md:flex-row">
            <flux:text>
                Made with <span class="accent-zinc-500">&hearts;</span> by <strong
                    class=" text-zinc-900 dark:text-zinc-50">Strongsy</strong>
            </flux:text>
        </flux:container>
    </flux:container>
</div>
