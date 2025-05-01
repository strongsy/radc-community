{{-- css variable for hero image --}}
{{--@php
    $backgroundImage = Storage::disk('s3')->url('hero/hero02.png');
    $backgroundStyles = "background-image: url('$backgroundImage');";
    $backgroundClasses = "bg-no-repeat bg-cover bg-center bg-gray-700 bg-blend-multiply";
@endphp--}}

<div class="flex w-full flex-col bg-no-repeat bg-zinc-50 dark:bg-zinc-900 bg-cover bg-center bg-blend-multiply" {{--style="{{ $backgroundStyles }}"--}}>
    <!--navbar-->
    <flux:container class="flex flex-row space-x-20 w-full py-3 items-center justify-between">
        <a href="{{ route('home') }}" class="ml-2 mr-5 items-center space-x-2 lg:ml-0 hidden lg:flex" wire:navigate>
            <x-app-logo class="text-white"/>
        </a>
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2"/>

        <flux:navbar class="hidden lg:flex justify-end">
            <x-navbar-items/>
        </flux:navbar>

        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle"
                     aria-label="Toggle dark mode"/>
    </flux:container>

    <flux:separator size="lg" variant="subtle"/>

    <!--hero-->
    <flux:container class="flex flex-row gap-x-10 max-w-7xl text-center justify-between py-5 my-10">
        <flux:container class="flex flex-col px-0! sm:mb-6 lg:text-left lg:mb-0 lg:pl-0">
            <h1 class="mb-4 font-bold leading-tight text-4xl">
                Royal Army Dental Corps Veterans Community
            </h1>
            <flux:heading size="lg" level="2" class="mb-4 text-zinc-700! dark:text-zinc-400!">
                We aim to foster a safe and healthy environment for old comrades of the former Royal Army Dental
                Corps
                (RADC) to meet and socialise.
            </flux:heading>

            <a href="{{ route('login') }}">
                <flux:button variant="danger" icon-trailing="arrow-right" aria-label="Register button to join the community">
                    Login
                </flux:button>
            </a>
        </flux:container>

        <!--youtube container-->
        <flux:container class="hidden lg:flex flex-col sm:mb-6 lg:text-left lg:mb-0 lg:pr-0">
            <iframe class="md:aspect-video md:h-42 lg:h-64 2xl:h-80 rounded-lg"
                    aria-label="Video of David Arkush, Japanese Prisoner of War."
                    src="https://www.youtube.com/embed/LNWeiDAxU10" title="YouTube video player"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
        </flux:container>
    </flux:container>
</div>
