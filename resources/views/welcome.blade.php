<x-layouts.front-end title="Welcome to the Homepage">

    <!--heading-->
    <flux:container
        class="flex w-full flex-col items-start starting:opacity-0 opacity-100 transition-opacity duration-750 md:grow">
        <flux:heading size="xl" level="1" class="my-10 flex flex-row">Welcome
        </flux:heading>
    </flux:container>

    <!--amalgamation-->
    <flux:container
        class="flex gap-10 flex-col mx-auto max-w-7xl starting:opacity-0 opacity-100 transition-opacity duration-750 md:justify-start">

        <!--horizontal cards wrapper-->
        <flux:container class="flex px-0! flex-col sm:flex-row gap-10">

            <!--amalgamation-->
            <flux-container class="w-full lg:w-1/2 border border-gray-200 dark:border-gray-700 rounded-lg col-span-1/2">
                <flux-container class="flex-initial">
                    <img
                        class="w-full h-auto max-w-none rounded-t-lg translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750"
                        src="{{ Storage::disk('s3')->url('home/home01.png') }}" alt=""/>
                </flux-container>

                <flux-container class="flex-auto">
                    <flux-container class="flex flex-col p-5">
                        <flux:text class="text-sm  leading-relaxed md:text-md">
                            <flux:heading size="lg" level="2" class="mb-5">Requirements for access to this site
                            </flux:heading>
                            <flux:text class="mb-5 font-semibold">
                                To gain authorisation to access this site, you must meet the following requirements:
                            </flux:text>

                            <ul class="list-disc list-inside mt-3">
                                <li>You must be a serving member of RAMS (Dental Branch)</li>
                                <li>A veteran of the above or a veteran of the RADC</li>
                                <li>A reservist of RAMS (Dental Branch) or a reserve veteran of either RAMS or RADC</li>
                                <li>A civilian who has served or is serving with either RAMS (Dental Branch) or RADC
                                </li>
                                <li>A spouse or partner of any of the above</li>
                            </ul>
                            <div class="text-start">
                                <a href="{{ route('register') }}">
                                    <flux:button variant="danger" class="mt-3 w-min" icon-trailing="arrow-right"
                                                 aria-label="Register button to join the community">
                                        Register
                                    </flux:button>
                                </a>
                            </div>
                        </flux:text>
                    </flux-container>
                </flux-container>
            </flux-container>

            <!--veterans-->
            <flux-container class="w-full lg:w-1/2 border border-gray-200 dark:border-gray-700 rounded-lg">
                <flux-container class="flex-initial">
                    <img
                        class="h-auto w-full max-w-none rounded-t-lg translate-y-0 starting:translate-y-6 object-cover starting:opacity-0 opacity-100 transition-all duration-750"
                        src="{{ Storage::disk('s3')->url('home/home02.png') }}" alt=""/>
                </flux-container>

                <flux-container class="flex-auto">
                    <flux-container class="flex flex-col p-5">
                        <flux:text class="text-sm  leading-relaxed md:text-md">
                            <flux:heading size="lg" level="2" class="mb-5">The amalgamation of the corps</flux:heading>
                            The news that our Corps was to amalgamate was announced at relatively short
                            notice in the
                            Autumn of 2024 and must, therefore, have come as a bit of a surprise. We fully recognise
                            that some
                            of you will have felt that you had been kept in the dark. I am truly sorry for
                            this but, in
                            actual fact, we were prevented from breaking the news, and involving you in the
                            discussions,
                            by a series of events: the death of our Late Queen; the coronation; the
                            announcement and
                            then the conduct of the general election; and, finally...
                            <div class="text-start">
                                <a href="{{ route('about') }}">
                                    <flux:button variant="danger" class="mt-3 w-min" icon-trailing="arrow-right"
                                                 aria-label="Read more about the corps amalgamation">
                                        Read More
                                    </flux:button>
                                </a>
                            </div>

                        </flux:text>
                    </flux-container>
                </flux-container>
            </flux-container>
        </flux:container>

        <!--related pages-->
        <flux:container class="flex w-full flex-col px-0! items-start">

            <!--container heading-->
            <flux:heading size="lg" level="2" class="w-full mb-5">Related pages</flux:heading>

            <!--wrapper-->
            <flux:container
                class="flex flex-col px-0! sm:flex-row gap-5 md:gap-10 starting:opacity-0 opacity-100 transition-opacity duration-750">

                <!--history-->
                <flux-container
                    class="flex shrink flex-col justify-between flex-1 md:basis-1/4 border border-gray-200 dark:border-gray-700 rounded-lg">

                    <!--image-->
                    <flux:container class="px-0!">
                        <img src="{{ Storage::disk('s3')->url('home/history.png') }}"
                             class="h-auto w-full max-w-none translate-y-0 starting:translate-y-6 rounded-t-lg object-cover starting:opacity-0 opacity-100 transition-all duration-750"
                             alt="Image 1">
                    </flux:container>

                    <!--text-->
                    <flux:container class="p-3! flex-1">
                        <flux:heading size="lg" level="2" class="mb-3">
                            History
                        </flux:heading>
                        <flux:text class="mb-5">
                            As far back as the seventeenth century instruments for the extraction and scaling...
                        </flux:text>
                    </flux:container>

                    <!--button-->
                    <flux:container class="p-3! flex-1 grow-0">
                        <a href="{{ route('history') }}">
                            <flux:button variant="filled" icon-trailing="arrow-right"
                                         aria-label="Read more about the corps history">
                                Read more
                            </flux:button>
                        </a>
                    </flux:container>
                </flux-container>

                <!--chapel-->
                <flux-container
                    class="flex shrink flex-col justify-between flex-1 md:basis-1/4 border border-gray-200 dark:border-gray-700 rounded-lg">

                    <!--image-->
                    <flux:container class="px-0!">
                        <img src="{{ Storage::disk('s3')->url('home/chapel.png') }}"
                             class="h-auto w-full max-w-none translate-y-0 starting:translate-y-6 rounded-t-lg object-cover starting:opacity-0 opacity-100 transition-all duration-750"
                             alt="Image 1">
                    </flux:container>

                    <!--text-->
                    <flux:container class="p-3! flex-1">
                        <flux:heading size="lg" level="2" class="mb-3">
                            Chapel
                        </flux:heading>
                        <flux:text class="mb-5">
                            Following the Second World War, a Book of Remembrance was commissioned by the
                            Corps...
                        </flux:text>
                    </flux:container>

                    <!--button-->
                    <flux:container class="p-3! flex-1 grow-0">
                        <a href="{{ route('chapel') }}">
                            <flux:button variant="filled" icon-trailing="arrow-right"
                                         aria-label="Read more about the corps chapel">
                                Read more
                            </flux:button>
                        </a>
                    </flux:container>
                </flux-container>

                <!--memorial-->
                <flux-container
                    class="flex shrink flex-col justify-between flex-1 md:basis-1/4 border border-gray-200 dark:border-gray-700 rounded-lg">

                    <!--image-->
                    <flux:container class="px-0!">
                        <img src="{{ Storage::disk('s3')->url('home/memorial.png') }}"
                             class="h-auto w-full max-w-none translate-y-0 starting:translate-y-6 rounded-t-lg object-cover starting:opacity-0 opacity-100 transition-all duration-750"
                             alt="Image 1">
                    </flux:container>

                    <!--text-->
                    <flux:container class="p-3! flex-1">
                        <flux:heading size="lg" level="2" class="mb-3">
                            Memorial
                        </flux:heading>
                        <flux:text class="mb-5">
                            Following the Second World War, a Memorial Stone was commissioned...
                        </flux:text>
                    </flux:container>

                    <!--button-->
                    <flux:container class="p-3! flex-1 grow-0">
                        <a href="{{ route('memorial') }}">
                            <flux:button variant="filled" icon-trailing="arrow-right"
                                         aria-label="Read more about the corps memorial">
                                Read more
                            </flux:button>
                        </a>
                    </flux:container>
                </flux-container>

                <!--museum-->
                <flux-container
                    class="flex shrink flex-col justify-between flex-1 md:basis-1/4 border border-gray-200 dark:border-gray-700 rounded-lg">

                    <!--image-->
                    <flux:container class="px-0!">
                        <img src="{{ Storage::disk('s3')->url('home/museum.png') }}"
                             class="h-auto w-full max-w-none translate-y-0 starting:translate-y-6 rounded-t-lg object-cover starting:opacity-0 opacity-100 transition-all duration-750"
                             alt="Image 1">
                    </flux:container>

                    <!--text-->
                    <flux:container class="p-3! flex-1">
                        <flux:heading size="lg" level="2" class="mb-3">
                            Museum
                        </flux:heading>
                        <flux:text class="mb-5">
                            The Museum of Military Medicine is the Army-designated and supported focus...
                        </flux:text>
                    </flux:container>

                    <!--button-->
                    <flux:container class="p-3! flex-1 grow-0">
                        <a href="{{ route('museum') }}">
                            <flux:button variant="filled" icon-trailing="arrow-right"
                                         aria-label="Read more about the museum of military medicine">
                                Read more
                            </flux:button>
                        </a>
                    </flux:container>
                </flux-container>
            </flux:container>
        </flux:container>
    </flux:container>
</x-layouts.front-end>




