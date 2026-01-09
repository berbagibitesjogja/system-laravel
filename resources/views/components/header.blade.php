<div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
    @auth
        <style>
            .hover-underline::after {
                content: '';
                position: absolute;
                bottom: -9px;
                left: 0;
                width: 0;
                height: 1.5px;
                background-color: #3b82f6;
                transition: width 0.3s ease;
            }

            .hover-underline:hover::after {
                width: 100%;
            }
        </style>

        {{-- Mobile Overlay & Sidebar --}}
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80" @click="sidebarOpen = false"></div>

            {{-- Sidebar Panel --}}
            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in-out duration-300 transform"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="relative ml-auto flex w-full max-w-xs flex-col overflow-y-auto bg-white shadow-xl h-full">

                    {{-- Close Button --}}
                    <div class="flex items-center justify-between px-6 pt-6 pb-4">
                        <span class="text-lg font-bold text-navy-900">Menu</span>
                        <button type="button" @click="sidebarOpen = false" class="-m-2.5 rounded-md p-2.5 text-gray-700 hover:text-navy-600">
                            <span class="sr-only">Close menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Menu Items --}}
                    <div class="flex flex-col gap-1 px-6 mt-4">
                        @auth
                            <a href="{{ route('volunteer.home') }}"
                                class="block px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 rounded-lg">Home</a>
                            <a href="{{ route('hall-of-fame') }}"
                                class="block px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 rounded-lg">Hall of Fame</a>
                            <a href="{{ route('gallery') }}"
                                class="block px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 rounded-lg">Gallery</a>
                        @else
                            <a href="https://berbagibitesjogja.com/"
                                class="block px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 rounded-lg">Home</a>
                        @endauth

                        @guest
                            <a href="{{ route('form.create') }}"
                                class="block px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 rounded-lg">Form</a>
                            <a href="{{ route('donation.index') }}"
                                class="block px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 rounded-lg">Donation</a>
                            <a href="https://berbagibitesjogja.com/beri-kontribusi"
                                class="block px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 rounded-lg">Beri
                                Kontribusi</a>
                        @endguest

                        @auth
                            {{-- Action Dropdown --}}
                            <div x-data="{ isActionOpen: false }" class="space-y-1">
                                <button type="button" @click="isActionOpen = !isActionOpen"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                                    Action
                                    <svg :class="{ 'rotate-180': isActionOpen }" class="h-5 w-5 flex-none text-gray-400 transition-transform"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="isActionOpen" x-transition class="mt-2 space-y-2 pl-4">
                                    <a href="{{ route('donation.index') }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Donation</a>
                                    <a href="{{ route('hero.index') }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Heroes</a>
                                    <a href="{{ route('food.index') }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Foods</a>
                                </div>
                            </div>

                            {{-- Beneficiaries Dropdown --}}
                            <div x-data="{ isBeneficiaryOpen: false }" class="space-y-1">
                                <button type="button" @click="isBeneficiaryOpen = !isBeneficiaryOpen"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                                    Beneficiaries
                                    <svg :class="{ 'rotate-180': isBeneficiaryOpen }" class="h-5 w-5 flex-none text-gray-400 transition-transform"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="isBeneficiaryOpen" x-transition class="mt-2 space-y-2 pl-4">
                                    <a href="{{ route('beneficiary.index', ['variant' => 'student']) }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">University</a>
                                    <a href="{{ route('beneficiary.index', ['variant' => 'foundation']) }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Foundation</a>
                                    <a href="{{ route('beneficiary.index', ['variant' => 'society']) }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Society</a>
                                    <a href="{{ route('beneficiary.index') }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">All</a>
                                </div>
                            </div>

                            {{-- Partner Dropdown --}}
                            <div x-data="{ isPartnerOpen: false }" class="space-y-1">
                                <button type="button" @click="isPartnerOpen = !isPartnerOpen"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                                    Partner
                                    <svg :class="{ 'rotate-180': isPartnerOpen }" class="h-5 w-5 flex-none text-gray-400 transition-transform"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="isPartnerOpen" x-transition class="mt-2 space-y-2 pl-4">
                                    <a href="{{ route('sponsor.index', ['variant' => 'company']) }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Company</a>
                                    <a href="{{ route('sponsor.index', ['variant' => 'individual']) }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Individual</a>
                                    <a href="{{ route('sponsor.index') }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">All</a>
                                </div>
                            </div>

                            @if (auth()->user()->role == 'super')
                                {{-- Logs Dropdown --}}
                                <div x-data="{ isLogsOpen: false }" class="space-y-1">
                                    <button type="button" @click="isLogsOpen = !isLogsOpen"
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                                        Logs
                                        <svg :class="{ 'rotate-180': isLogsOpen }" class="h-5 w-5 flex-none text-gray-400 transition-transform"
                                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div x-show="isLogsOpen" x-transition class="mt-2 space-y-2 pl-4">
                                        <a href="{{ route('logs.system') }}"
                                            class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">System</a>
                                        <a href="{{ route('logs.activity') }}"
                                            class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">User</a>
                                    </div>
                                </div>
                            @endif
                            <a href="{{ route('logout') }}"
                                class="block px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 rounded-lg">Logout</a>
                        @endauth
                    </div>

                    {{-- Footer Links in Mobile Menu --}}
                    <div class="mt-auto p-6 space-y-4">
                        <div class="flex items-center gap-4">
                            <a href="https://www.instagram.com/berbagibitesjogja">
                                <svg class="h-6 w-auto" fill="#0395AF" viewBox="0 0 511 511.9">
                                    <path d="m510.949219 150.5c-1.199219-27.199219-5.597657-45.898438-11.898438-62.101562-6.5-17.199219-16.5-32.597657-29.601562-45.398438-12.800781-13-28.300781-23.101562-45.300781-29.5-16.296876-6.300781-34.898438-10.699219-62.097657-11.898438-27.402343-1.300781-36.101562-1.601562-105.601562-1.601562s-78.199219.300781-105.5 1.5c-27.199219 1.199219-45.898438 5.601562-62.097657 11.898438-17.203124 6.5-32.601562 16.5-45.402343 29.601562-13 12.800781-23.097657 28.300781-29.5 45.300781-6.300781 16.300781-10.699219 34.898438-11.898438 62.097657-1.300781 27.402343-1.601562 36.101562-1.601562 105.601562s.300781 78.199219 1.5 105.5c1.199219 27.199219 5.601562 45.898438 11.902343 62.101562 6.5 17.199219 16.597657 32.597657 29.597657 45.398438 12.800781 13 28.300781 23.101562 45.300781 29.5 16.300781 6.300781 34.898438 10.699219 62.101562 11.898438 27.296876 1.203124 36 1.5 105.5 1.5s78.199219-.296876 105.5-1.5c27.199219-1.199219 45.898438-5.597657 62.097657-11.898438 34.402343-13.300781 61.601562-40.5 74.902343-74.898438 6.296876-16.300781 10.699219-34.902343 11.898438-62.101562 1.199219-27.300781 1.5-36 1.5-105.5s-.101562-78.199219-1.300781-105.5zm-46.097657 209c-1.101562 25-5.300781 38.5-8.800781 47.5-8.601562 22.300781-26.300781 40-48.601562 48.601562-9 3.5-22.597657 7.699219-47.5 8.796876-27 1.203124-35.097657 1.5-103.398438 1.5s-76.5-.296876-103.402343-1.5c-25-1.097657-38.5-5.296876-47.5-8.796876-11.097657-4.101562-21.199219-10.601562-29.398438-19.101562-8.5-8.300781-15-18.300781-19.101562-29.398438-3.5-9-7.699219-22.601562-8.796876-47.5-1.203124-27-1.5-35.101562-1.5-103.402343s.296876-76.5 1.5-103.398438c1.097657-25 5.296876-38.5 8.796876-47.5 4.101562-11.101562 10.601562-21.199219 19.203124-29.402343 8.296876-8.5 18.296876-15 29.398438-19.097657 9-3.5 22.601562-7.699219 47.5-8.800781 27-1.199219 35.101562-1.5 103.398438-1.5 68.402343 0 76.5.300781 103.402343 1.5 25 1.101562 38.5 5.300781 47.5 8.800781 11.097657 4.097657 21.199219 10.597657 29.398438 19.097657 8.5 8.300781 15 18.300781 19.101562 29.402343 3.5 9 7.699219 22.597657 8.800781 47.5 1.199219 27 1.5 35.097657 1.5 103.398438s-.300781 76.300781-1.5 103.300781zm0 0"></path>
                                    <path d="m256.449219 124.5c-72.597657 0-131.5 58.898438-131.5 131.5s58.902343 131.5 131.5 131.5c72.601562 0 131.5-58.898438 131.5-131.5s-58.898438-131.5-131.5-131.5zm0 216.800781c-47.097657 0-85.300781-38.199219-85.300781-85.300781s38.203124-85.300781 85.300781-85.300781c47.101562 0 85.300781 38.199219 85.300781 85.300781s-38.199219 85.300781-85.300781 85.300781zm0 0"></path>
                                    <path d="m423.851562 119.300781c0 16.953125-13.746093 30.699219-30.703124 30.699219-16.953126 0-30.699219-13.746094-30.699219-30.699219 0-16.957031 13.746093-30.699219 30.699219-30.699219 16.957031 0 30.703124 13.742188 30.703124 30.699219zm0 0"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Header with Toggle --}}
        <nav class="z-10 bg-white flex sticky top-0 md:hidden justify-between px-6 pb-6 pt-6 shadow-sm border-b border-gray-100 items-center">
            <a href="https://berbagibitesjogja.com" class="flex items-center text-tosca text-xl font-bold gap-2">
                <img src="{{ asset('assets/biru.png') }}" class="w-8" alt="logo">
                <span class="truncate">Berbagi Bites Jogja</span>
            </a>
            <button @click="sidebarOpen = true" class="flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-tosca-500">
                <span class="sr-only">Open main menu</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </nav>

        {{-- Desktop Navigation (Unchanged) --}}
        <nav class="hidden md:flex p-4 justify-between items-center bg-gray-50 px-44">
            <div class="flex items-center gap-6">
                <!-- Contact info placeholder -->
                <span class="text-xs text-gray-400">berbagibitesjogja@gmail.com</span>
            </div>
            <!-- Social icons placeholder -->
             <div class="flex items-center gap-6">
            <a href="https://www.instagram.com/berbagibitesjogja">
                <svg class="rishi-icon" width="15" fill="#0395AF" height="20" viewBox="0 0 511 511.9">
                    <path
                        d="m510.949219 150.5c-1.199219-27.199219-5.597657-45.898438-11.898438-62.101562-6.5-17.199219-16.5-32.597657-29.601562-45.398438-12.800781-13-28.300781-23.101562-45.300781-29.5-16.296876-6.300781-34.898438-10.699219-62.097657-11.898438-27.402343-1.300781-36.101562-1.601562-105.601562-1.601562s-78.199219.300781-105.5 1.5c-27.199219 1.199219-45.898438 5.601562-62.097657 11.898438-17.203124 6.5-32.601562 16.5-45.402343 29.601562-13 12.800781-23.097657 28.300781-29.5 45.300781-6.300781 16.300781-10.699219 34.898438-11.898438 62.097657-1.300781 27.402343-1.601562 36.101562-1.601562 105.601562s.300781 78.199219 1.5 105.5c1.199219 27.199219 5.601562 45.898438 11.902343 62.101562 6.5 17.199219 16.597657 32.597657 29.597657 45.398438 12.800781 13 28.300781 23.101562 45.300781 29.5 16.300781 6.300781 34.898438 10.699219 62.101562 11.898438 27.296876 1.203124 36 1.5 105.5 1.5s78.199219-.296876 105.5-1.5c27.199219-1.199219 45.898438-5.597657 62.097657-11.898438 34.402343-13.300781 61.601562-40.5 74.902343-74.898438 6.296876-16.300781 10.699219-34.902343 11.898438-62.101562 1.199219-27.300781 1.5-36 1.5-105.5s-.101562-78.199219-1.300781-105.5zm-46.097657 209c-1.101562 25-5.300781 38.5-8.800781 47.5-8.601562 22.300781-26.300781 40-48.601562 48.601562-9 3.5-22.597657 7.699219-47.5 8.796876-27 1.203124-35.097657 1.5-103.398438 1.5s-76.5-.296876-103.402343-1.5c-25-1.097657-38.5-5.296876-47.5-8.796876-11.097657-4.101562-21.199219-10.601562-29.398438-19.101562-8.5-8.300781-15-18.300781-19.101562-29.398438-3.5-9-7.699219-22.601562-8.796876-47.5-1.203124-27-1.5-35.101562-1.5-103.402343s.296876-76.5 1.5-103.398438c1.097657-25 5.296876-38.5 8.796876-47.5 4.101562-11.101562 10.601562-21.199219 19.203124-29.402343 8.296876-8.5 18.296876-15 29.398438-19.097657 9-3.5 22.601562-7.699219 47.5-8.800781 27-1.199219 35.101562-1.5 103.398438-1.5 68.402343 0 76.5.300781 103.402343 1.5 25 1.101562 38.5 5.300781 47.5 8.800781 11.097657 4.097657 21.199219 10.597657 29.398438 19.097657 8.5 8.300781 15 18.300781 19.101562 29.402343 3.5 9 7.699219 22.597657 8.800781 47.5 1.199219 27 1.5 35.097657 1.5 103.398438s-.300781 76.300781-1.5 103.300781zm0 0">
                    </path>
                    <path
                        d="m256.449219 124.5c-72.597657 0-131.5 58.898438-131.5 131.5s58.902343 131.5 131.5 131.5c72.601562 0 131.5-58.898438 131.5-131.5s-58.898438-131.5-131.5-131.5zm0 216.800781c-47.097657 0-85.300781-38.199219-85.300781-85.300781s38.203124-85.300781 85.300781-85.300781c47.101562 0 85.300781 38.199219 85.300781 85.300781s-38.199219 85.300781-85.300781 85.300781zm0 0">
                    </path>
                    <path
                        d="m423.851562 119.300781c0 16.953125-13.746093 30.699219-30.703124 30.699219-16.953126 0-30.699219-13.746094-30.699219-30.699219 0-16.957031 13.746093-30.699219 30.699219-30.699219 16.957031 0 30.703124 13.742188 30.703124 30.699219zm0 0">
                    </path>
                </svg>

            </a>
            <a href="https://www.linkedin.com/company/berbagibitesjogja">
                <svg class="rishi-icon" fill="#0395AF" width="15" height="20" viewBox="0 0 24 24">
                    <path
                        d="m23.994 24v-.001h.006v-8.802c0-4.306-.927-7.623-5.961-7.623-2.42 0-4.044 1.328-4.707 2.587h-.07v-2.185h-4.773v16.023h4.97v-7.934c0-2.089.396-4.109 2.983-4.109 2.549 0 2.587 2.384 2.587 4.243v7.801z">
                    </path>
                    <path d="m.396 7.977h4.976v16.023h-4.976z"></path>
                    <path
                        d="m2.882 0c-1.591 0-2.882 1.291-2.882 2.882s1.291 2.909 2.882 2.909 2.882-1.318 2.882-2.909c-.001-1.591-1.292-2.882-2.882-2.882z">
                    </path>
                </svg>

            </a>

        </div>
        </nav>
        <nav
        class="z-40 hidden md:flex bg-white sticky top-0 mx-auto p-4 justify-between items-center border-b-2 border-gray-200  px-44">
        <div class="flex flex-row items-center gap-2">
            <img src="{{ asset('assets/biru.png') }}" class="w-10" alt="">
            <a href="https://berbagibitesjogja.com" class="text-2xl font-semibold text-tosca-500">Berbagi Bites Jogja</a>
        </div>
        <ul class="flex space-x-8 relative">

            <li class="relative">
                @auth
                    <a class="@if (request()->routeIs('volunteer.home')) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca @endif
                    py-2"
                        href="{{ route('volunteer.home') }}">Home</a>
                @else
                    <a class="@if (str_contains(request()->route()->getName(), 'volunteer')) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca @endif
                    py-2"
                        href="https://berbagibitesjogja.com/">Home</a>
                @endauth
            </li>
            @auth
                <li class="relative">
                    <a class="@if (request()->routeIs('hall-of-fame')) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca @endif
                    py-2"
                        href="{{ route('hall-of-fame') }}">Hall of Fame</a>
                </li>
                <li class="relative">
                    <a class="@if (request()->routeIs('gallery')) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca @endif
                    py-2"
                        href="{{ route('gallery') }}">Gallery</a>
                </li>
            @endauth
            @guest

                <li class="relative">
                    <a href="{{ route('form.create') }}"
                        class="@if (str_contains(request()->route()->getName(), 'form')) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca @endif
                    py-2">Form</a>
                </li>
                <li class="relative">
                    <a href="{{ route('donation.index') }}"
                        class="@if (str_contains(request()->route()->getName(), 'donation')) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca @endif
                    py-2">Donation</a>
                </li>
                <li class="relative">
                    <a href="https://berbagibitesjogja.com/beri-kontribusi"
                        class="@if (str_contains(request()->route()->getName(), 'form')) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca @endif
                    py-2">Beri
                        Kontribusi</a>
                </li>
            @endguest
            @auth

                <li class="relative group">
                    <a
                        class="@if (in_array(explode('.', request()->route()->getName())[0], ['donation', 'food', 'hero'])) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca group-hover:text-tosca @endif
                    py-2 cursor-pointer">Action</a>
                    <div
                        class="absolute transition-all duration-300 ease-in-out transform translate-y-[-10px] opacity-0 invisible pointer-events-none group-hover:translate-y-0 group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto z-10 bg-white divide-y mt-2 divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                            <li>
                                <a href="{{ route('donation.index') }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Donation</a>
                            </li>
                            <li>
                                <a href="{{ route('food.index') }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Foods</a>
                            </li>
                            <li>
                                <a href="{{ route('hero.index') }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Heroes</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="relative group">
                    <a href="{{ route('beneficiary.index') }}"
                        class="@if (in_array(explode('.', request()->route()->getName())[0], ['university', 'foundation', 'society', 'beneficiary'])) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca group-hover:text-tosca @endif
                    py-2 cursor-pointer">Beneficiaries</a>
                    <div
                        class="absolute transition-all duration-300 ease-in-out transform translate-y-[-10px] opacity-0 invisible pointer-events-none group-hover:translate-y-0 group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto z-10 bg-white divide-y mt-2 divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                            <li>
                                <a href="{{ route('beneficiary.index', ['variant' => 'student']) }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">University</a>
                            </li>
                            <li>
                                <a href="{{ route('beneficiary.index', ['variant' => 'foundation']) }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Foundation</a>
                            </li>
                            <li>
                                <a href="{{ route('beneficiary.index', ['variant' => 'society']) }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Society</a>
                            </li>
                            <li>
                                <a href="{{ route('beneficiary.index') }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">All</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="relative group">
                    <a href="{{ route('sponsor.index') }}"
                        class="@if (in_array(explode('.', request()->route()->getName())[0], ['sponsor'])) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca group-hover:text-tosca @endif
                    py-2 cursor-pointer">Partner</a>
                    <div
                        class="absolute transition-all duration-300 ease-in-out transform translate-y-[-10px] opacity-0 invisible pointer-events-none group-hover:translate-y-0 group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto z-10 bg-white divide-y mt-2 divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                            <li>
                                <a href="{{ route('sponsor.index', ['variant' => 'company']) }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Company</a>
                            </li>
                            <li>
                                <a href="{{ route('sponsor.index', ['variant' => 'individual']) }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Individual</a>
                            </li>
                            <li>
                                <a href="{{ route('sponsor.index') }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">All</a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                @if (auth()->user()->role == 'super')
                    <li class="relative group">
                        <a
                            class="@if (in_array(explode('.', request()->route()->getName())[0], ['logs'])) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca group-hover:text-tosca @endif
                    py-2 cursor-pointer">Logs</a>
                        <div
                            class="absolute transition-all duration-300 ease-in-out transform translate-y-[-10px] opacity-0 invisible pointer-events-none group-hover:translate-y-0 group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto z-10 bg-white divide-y mt-2 divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                <li>
                                    <a href="{{ route('logs.system') }}"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">System</a>
                                </li>
                                <li>
                                    <a href="{{ route('logs.activity') }}"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">User</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
                <li class="relative">
                    <a href="{{ route('logout') }}"
                        class="
                    hover-underline text-gray-400 hover:text-tosca
                 py-2">Logout</a>
                </li>
            @endauth
        </ul>
    </nav>
    @endauth

    @guest
        <header class='flex justify-between py-3 md:py-6 items-center px-6 md:px-32 sticky top-0 bg-white z-100 shadow-sm'>
            <a href="https://berbagibitesjogja.com/" class='flex items-center gap-x-2'>
                <img src="{{ asset('assets/biru.png') }}" alt="bbj-logo" width='40' height='40' />
                <span class='text-navy font-bold text-xl md:text-2xl'>Berbagi Bites Jogja</span>
            </a>
            <nav>
                <ul class="hidden md:flex space-x-12">
                    <li>
                        <a class='font-semibold text-sm hover:text-tosca transition-colors' href="https://berbagibitesjogja.com/#program">Program</a>
                    </li>
                    <li>
                        <a class='font-semibold text-sm hover:text-tosca transition-colors' href="https://berbagibitesjogja.com/#dampak">Dampak</a>
                    </li>
                    <li>
                        <a class='font-semibold text-sm hover:text-tosca transition-colors' href="https://berbagibitesjogja.com/news">Artikel</a>
                    </li>
                    <li>
                        <a class='font-semibold text-sm hover:text-tosca transition-colors' href="https://berbagibitesjogja.com/#gabung">Bergabung</a>
                    </li>
                    <li>
                        <a class='font-semibold text-sm hover:text-tosca transition-colors' href="https://berbagibitesjogja.com/#kontak">Kontak</a>
                    </li>
                </ul>
            </nav>
            <a class='bg-navy hover:bg-navy-700 text-sm rounded-full h-max py-2 text-white font-semibold px-4 md:px-6 hidden md:block transition-all'
                href="https://wa.me/628986950700">Donasi Sekarang</a>
        </header>
    @endguest
</div>
