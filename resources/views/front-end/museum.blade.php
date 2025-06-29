<x-layouts.front-end title="The Museum of Military Medicine">
    <flux:container
        class="flex w-full flex-col items-start starting:opacity-0 opacity-100 transition-opacity duration-750 md:grow">
        <flux:heading size="xl" level="1" class="my-10 flex flex-row">The Museum of Military Medicine
        </flux:heading>
    </flux:container>

    <flux:container
        class="flex w-full flex-col gap-10 items-start starting:opacity-0 opacity-100 transition-opacity duration-750 md:grow">
        <flux:container class="flex px-0! w-full flex-col items-start">
            <flux:text class="leading-relaxed">
                <img src="{{ Storage::disk('s3')->url('museum/museum02.png') }}"
                     alt="The uniform of Capt Julius Green"
                     class="mb-5 me-8 h-auto w-full sm:w-72 lg:w-96 max-w-none translate-y-0 starting:translate-y-6 rounded-lg object-cover starting:opacity-0 opacity-100 transition-all duration-750 border border-gray-200 float-start dark:border-gray-700"
                     aria-describedby="The uniform of Capt Julius Green"/>
                <flux:heading size="lg" level="3" class="mb-5">The history of the museum</flux:heading>
                The Museum of Military Medicine is the Army-designated and supported focus for the heritage and history
                of the four Sovereign corps of the Army Medical Services (AMS). It holds in trust artefact collections
                and archives relating to the Royal Army Medical Corps (RAMC), Royal Army Veterinary Corps (RAVC), Royal
                Army Dental Corps (RADC), and the Queen Alexandra’s Royal Army Nursing Corps (QARANC), along with their
                respective antecedents. The collections are maintained by the Museum of Military Medicine Trust, a
                Charitable Incorporated Organisation, registered charity no. 1171026, originally constituted in May 1999
                when the four individual Corps museums that existed until that date were brought together under one
                governance structure.

                <flux:text class="mt-3 leading-relaxed">
                    The earliest antecedent collection is that of the RAMC Historical Museum, established in 1952, while
                    the
                    RADC Museum came into being a few years later, in 1957. It had long been an ambition of the RADC
                    Depot
                    and Training Establishment on Connaught Road, Aldershot, to have its own museum, to display items of
                    professional and regimental interest to visitors and courses―both officers and other ranks―who
                    attended
                    the Depot. A meeting of interested officers was held to discuss the creation of a museum, and the
                    organisation of a collection followed. Although not on “a grandiose scale,” in 1960 it was reported
                    that
                    the museum was “sufficient to arouse interest to visitors. The display cases house some very
                    interesting
                    specimens contributed by past and present members of the A.D. Corps and the R.A.D.C. and some
                    members of
                    the dental profession.”
                </flux:text>

                <flux:text class="mt-3 leading-relaxed my-3">
                    From the outset, each AMS Corps museum was intended to inculcate a sense of regimental traditions
                    and to
                    engender an ‘esprit de Corps’ to new recruits, which gave each museum their distinctive savour. As
                    hinted in the quotation above, acquisition and collections development relied upon the gifts of
                    individual serving and retired personnel that reflected their service, a situation that is still
                    true
                    today.
                </flux:text>
            </flux:text>
            <flux:separator variant="subtle" class="mt-5"/>
        </flux:container>

        <flux:container class="flex px-0! w-full flex-col items-start">
            <flux:text class="leading-relaxed">
                <img src="{{ Storage::disk('s3')->url('museum/museum01.png') }}"
                     alt="The musketeer diorama"
                     class="mb-5 me-8 h-auto w-full sm:w-72 lg:w-96 max-w-none translate-y-0 starting:translate-y-6 rounded-lg object-cover starting:opacity-0 opacity-100 transition-all duration-750 border border-gray-200 float-start dark:border-gray-700"
                     aria-describedby="The musketeer diorama"/>
                <flux:heading size="lg" level="3" class="mb-5">The Musketeer</flux:heading>
                The museum also features a series of dioramas to help illustrate certain themes in dental history. One
                features a Civil War-era musketeer. Before 1860, when breach-loading rifles and metal cartridges were
                introduced, it was essential that soldiers had good dentition to bite open powder cartridges while still
                holding their firearm. At this time, dental care mainly amounted to tooth extraction, sometimes using
                blacksmith’s tools, without sanitation or anaesthetics. In the 1850s sugar consumption soared, and with
                them rates of tooth decay. In the Boer War there were 7,000 admissions to hospital with dental problems.

                <flux:text class="mt-3 leading-relaxed my-3">
                    The museum continues to collect material relating to the history of the RADC and welcomes donations
                    from
                    Corps members, past and present. Please contact the museum for information on how to donate items.
                </flux:text>

                <flux:text class="mt-3 leading-relaxed my-3">
                    The museum, in Keogh Barracks, Ash Vale, Aldershot, is open Monday to Friday, 9.30 a.m. to 3.30 p.m.
                    and
                    is free to enter.
                </flux:text>
            </flux:text>
            <flux:separator variant="subtle" class="mt-5"/>
        </flux:container>

        <flux:container class="flex px-0! w-full flex-col items-start">
            <flux:text class="leading-relaxed">
                <img src="{{ Storage::disk('s3')->url('museum/museum03.png') }}"
                     alt="The bamboo dental chair"
                     class="mb-5 me-8 h-auto w-full sm:w-72 lg:w-96 max-w-none translate-y-0 starting:translate-y-6 rounded-lg object-cover starting:opacity-0 opacity-100 transition-all duration-750 border border-gray-200 float-start dark:border-gray-700"
                     aria-describedby="The bamboo dental chair"/>
                <flux:heading size="lg" level="3" class="mb-5">The bamboo dental chair</flux:heading>
                The museum displays feature numerous items drawn from the RADC collections that include dental
                instruments and kit, as well as more personal items. For example, while a prisoner at Milag, near
                Bremen, Captain Julius Green (1912-1990) wrote to his tailor in Glasgow to order a new uniform, which,
                remarkably, arrived two months later. His German captors were impressed! Similarly, Captain David Arkush
                treated the dental issues affecting fellow POWs during this imprisonment in the Far East, and had to
                contend with poor supplies and equipment. Arkush often had to improvise new tools crafted from bamboo,
                and even had his own dental chair constructed by one of his patients out of bamboo and rope. A number of
                these relics are on display at the museum.
            </flux:text>
        </flux:container>
    </flux:container>

</x-layouts.front-end>
