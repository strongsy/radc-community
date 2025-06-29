<x-layouts.front-end title="The RADC Chapel">
    <flux:container
        class="flex w-full flex-col items-start starting:opacity-0 opacity-100 transition-opacity duration-750 md:grow">
        <flux:heading size="xl" level="1" class="my-10 flex flex-row">The RADC Chapel
        </flux:heading>
    </flux:container>

    <flux:container
        class="flex w-full flex-col gap-10 items-start starting:opacity-0 opacity-100 transition-opacity duration-750 md:grow">
        <flux:container class="flex px-0! w-full flex-col items-start">
            <flux:text class="leading-relaxed">
                <img src="{{ Storage::disk('s3')->url('chapel/chapel01.png') }}"
                     alt="The RADC Book of Remembrance"
                     class="mb-5 me-8 h-auto w-full sm:w-72 lg:w-96 max-w-none translate-y-0 starting:translate-y-6 rounded-lg object-cover starting:opacity-0 opacity-100 transition-all duration-750 border border-gray-200 float-start dark:border-gray-700"
                     aria-describedby="The RADC Book of Remembrance"/>
                <flux:heading size="lg" level="3" class="mb-5">The RADC Book of Remembrance</flux:heading>
                Following the Second World War, a Book of Remembrance was commissioned by the Corps to commemorate the
                officers and men of the Royal Army Dental Corps who gave their lives during that war and since. On
                Monday 16th August 1948, a Memorial Service was conducted at the Depot and Training Establishment RADC,
                Connaught Road by the Chaplain General, the Rev Canon FL Hughes, BBE, MC, MA TD, Chaplain to the King.

                <flux:text class="mt-3 leading-relaxed">
                    On 29 November 1964 a final Service was held at St Albans as the church closed and was later
                    demolished.
                </flux:text>

                <flux:text class="mt-3 leading-relaxed">
                    At the Service a Memorial Stone and the Book of Remembrance were unveiled by the Adjutant-General,
                    General Sir James S Steele KBE, CB, DSO, MC, LLD. Wreaths were laid by General Sir James S Steele,
                    Major
                    General JCA Dowse (representing Director General Army Medical Services), Major General AB Austin
                    (Director Army Dental Service), and Colonel JP Duguid (Colonel Commandant RADC). The music was
                    provided
                    by the Band and Buglers of the RAMC, and the ceremony was attended by over 500 personnel.
                </flux:text>


                <flux:text class="mt-3 leading-relaxed">
                    The Book of Remembrance was initially placed in the old Connaught Hospital Chapel at the Depot and
                    Training Establishment RADC. It was inscribed and illuminated by Daisy Alcock, considered by many,
                    one
                    of the greatest calligraphers of the 20th century, who was also responsible for the HMS HOOD
                    Memorial
                    Book in St John’s Church, Boldre in the New Forest and the Battle of Britain Roll of Honour in
                    Westminster Abbey.
                </flux:text>

                <flux:text class="mt-3 leading-relaxed">
                    In 1951 the old wooden hospital chapel was deemed no longer fit for use and the Book was removed to
                    St
                    Alban’s Garrison Church at the west end of Connaught Barracks where Evelyn Woods Road turns South to
                    Mons Lines. This church was positioned in front of the East end of Tournai Officers’ Mess and faced
                    down
                    the length of Evelyn Woods Road. It was also constructed of wood, was built in 1856 as the original
                    North Camp Garrison Church, renamed the Marlborough Lines Garrison Church in 1892 when North Camp
                    was
                    officially redesignated as Marlborough Lines, and finally, after dedication in honour of St Alban on
                    27th September 1950 it became St Alban’s Garrison Church.
                </flux:text>

                <flux:text class="mt-3 leading-relaxed">
                    On 29 November 1964 a final Service was held at St Albans as the church closed and was later
                    demolished.
                    The Book of Remembrance was then moved to St George’s Garrison Church in Queens Avenue. On the
                    reorganisation of the Garrison Churches in 1973, the Book finally came to rest at the Royal Garrison
                    Church of All Saints, where it is housed in an RADC crested illuminated oak case in the RADC Chapel.
                </flux:text>

                <flux:text class="my-3 leading-relaxed">
                    Each year in the Garrison Church of All Saints, and at the National Memorial Arboretum or Lichfield
                    Garrison Church, there is a Turning of the Page Service when the RADC remember their fallen comrades
                    whose names feature in the Book of Remembrance.
                </flux:text>
            </flux:text>
            <flux:separator variant="subtle" class="mt-5"/>
        </flux:container>

        <flux:container class="flex px-0! w-full flex-col items-start">
            <flux:text class="leading-relaxed">
                <img src="{{ Storage::disk('s3')->url('chapel/chapel02.png') }}"
                     alt="Outside the Garrison Church of All Saints"
                     class="mb-5 me-8 h-auto w-full sm:w-72 lg:w-96 max-w-none translate-y-0 starting:translate-y-6 rounded-lg object-cover starting:opacity-0 opacity-100 transition-all duration-750 border border-gray-200 float-start dark:border-gray-700"
                     aria-describedby="Outside the Garrison Church of All Saints"/>
                <flux:heading size="lg" level="3" class="mb-5">The relocation</flux:heading>
                In 1951 the old wooden hospital chapel was deemed no longer fit for use and the Book was
                removed to St Alban’s Garrison Church at the west end of Connaught Barracks where Evelyn
                Woods Road turns South to Mons Lines. This church was positioned in front of the East end of
                Tournai Officers’ Mess and faced down the length of Evelyn Woods Road. It was also
                constructed of wood, was built in 1856 as the original North Camp Garrison Church, renamed
                the Marlborough Lines Garrison Church in 1892 when North Camp was officially re-designated
                as
                Marlborough Lines, and finally, after dedication in honour of St Alban on 27th September
                1950 it became St Alban’s Garrison Church.

                <flux:text class="mt-3 leading-relaxed">
                    On 29 November 1964 a final Service was held at St Albans as the church closed and was later
                    demolished. The Book of Remembrance was then moved to St George’s Garrison Church in Queens
                    Avenue. On the reorganisation of the Garrison Churches in 1973, the Book finally came to
                    rest at the Royal Garrison Church of All Saints, where it is housed in an RADC crested illuminated
                    oak case in the RADC Chapel.
                </flux:text>

                <flux:text class="mt-3 leading-relaxed">
                    Each year in the Garrison Church of All Saints, and at the National Memorial Arboretum or
                    Lichfield Garrison Church, there is a Turning of the Page Service when the RADC remember
                    their fallen comrades whose names feature in the Book of Remembrance.
                </flux:text>
            </flux:text>
        </flux:container>
    </flux:container>

</x-layouts.front-end>
