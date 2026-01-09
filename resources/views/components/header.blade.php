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

    <nav id="mobile-sidebar" class="z-40 fixed top-0 right-0 h-screen bg-white shadow-sidebar w-11/12 hidden px-6">
        <button onclick="closeSidebar()" class="float-end text-3xl text-grey-500 font-medium">X</button>
        <ul class="text-gray-500 flex gap-3 flex-col mt-28">
            @auth
                <li><a class="w-full block" href="{{ route('volunteer.home') }}">Home</a></li>
            @else
                <li><a class="w-full block" href="https://berbagibitesjogja.com/">Home</a></li>
            @endauth
            @guest
                <li><a class="w-full block" href="{{ route('form.create') }}">Form</a></li>
                <li><a class="w-full block" href="{{ route('donation.index') }}">Donation</a></li>
                <li><a class="w-full block" href="https://berbagibitesjogja.com/beri-kontribusi">Beri Kontribusi</a></li>
            @endguest
            @auth
                <li>
                    <button type="button" class="flex items-center w-full text-base group" aria-controls="dropdown-action"
                        data-collapse-toggle="dropdown-action">
                        <span class="flex-1 text-left rtl:text-right whitespace-nowrap">Action</span>
                        <svg class="w-3 h-3 transition-transform duration-200 transform" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="dropdown-action" aria-expanded="false" class="hidden py-2 space-y-2">
                        <li>
                            <a href="{{ route('donation.index') }}"
                                class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                <span class="text-sm">Donation</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hero.index') }}"
                                class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                <span class="text-sm">Heroes</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('food.index') }}"
                                class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                <span class="text-sm">Foods</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <button type="button" class="flex items-center w-full text-base group" aria-controls="dropdown-beneficiary"
                        data-collapse-toggle="dropdown-beneficiary">
                        <span class="flex-1 text-left rtl:text-right whitespace-nowrap">Beneficiaries</span>
                        <svg class="w-3 h-3 transition-transform duration-200 transform" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="dropdown-beneficiary" aria-expanded="false" class="hidden py-2 space-y-2">
                        <li>
                            <a href="{{ route('beneficiary.index', ['variant' => 'student']) }}"
                                class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                <span class="text-sm">University</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('beneficiary.index', ['variant' => 'foundation']) }}"
                                class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                <span class="text-sm">Foundation</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('beneficiary.index', ['variant' => 'society']) }}"
                                class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                <span class="text-sm">Society</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('beneficiary.index') }}"
                                class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                <span class="text-sm">All</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <button type="button" class="flex items-center w-full text-base group" aria-controls="dropdown-partner"
                        data-collapse-toggle="dropdown-partner">
                        <span class="flex-1 text-left rtl:text-right whitespace-nowrap">Partner</span>
                        <svg class="w-3 h-3 transition-transform duration-200 transform" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="dropdown-partner" aria-expanded="false" class="hidden py-2 space-y-2">
                        <li>
                            <a href="{{ route('sponsor.index', ['variant' => 'company']) }}"
                                class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                <span class="text-sm">Company</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sponsor.index', ['variant' => 'individual']) }}"
                                class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                <span class="text-sm">Individual</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sponsor.index') }}"
                                class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                <span class="text-sm">All</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @if (auth()->user()->role == 'super')
                    <li>
                        <button type="button" class="flex items-center w-full text-base group" aria-controls="dropdown-logs"
                            data-collapse-toggle="dropdown-logs">
                            <span class="flex-1 text-left rtl:text-right whitespace-nowrap">Logs</span>
                            <svg class="w-3 h-3 transition-transform duration-200 transform" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <ul id="dropdown-logs" aria-expanded="false" class="hidden py-2 space-y-2">
                            <li>
                                <a href="{{ route('logs.system') }}"
                                    class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                    <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                    <span class="text-sm">System</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logs.activity') }}"
                                    class="flex items-center space-x-5 p-2 transition duration-75 rounded-lg group">
                                    <span class="w-2 h-2 rounded-full
                        inline-block ms-1"></span>
                                    <span class="text-sm">User</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <li><a class="w-full block" href="{{ route('logout') }}">Logout</a></li>
            @endauth
        </ul>
        <div class="flex items-center gap-6 mt-6">
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
        <div class="flex flex-col gap-6 mt-6">
            <a class="flex items-center gap-3 text-gray-400" href="mailto:berbagibitesjogja@gmail.com">

                <svg xmlns="http://www.w3.org/2000/svg" fill="#0395AF" width="15" height="12.683"
                    viewBox="0 0 20 12.683">
                    <path id="Path_26505" data-name="Path 26505"
                        d="M10.463,976.362a1.465,1.465,0,0,0-.541.107l8.491,7.226a.825.825,0,0,0,1.159,0l8.5-7.233a1.469,1.469,0,0,0-.534-.1H10.463Zm-1.448,1.25a1.511,1.511,0,0,0-.015.213v9.756a1.46,1.46,0,0,0,1.463,1.463H27.537A1.46,1.46,0,0,0,29,987.581v-9.756a1.51,1.51,0,0,0-.015-.213l-8.46,7.2a2.376,2.376,0,0,1-3.064,0Z"
                        transform="translate(-9 -976.362)"></path>
                </svg>
                berbagibitesjogja@gmail.com
            </a>
            <a class="flex items-center gap-3 text-gray-400" href="https://wa.me/628986950700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#0395AF" class="rishi-icon" width="14"
                    height="19.788" viewBox="0 0 18.823 19.788">
                    <path id="Phone"
                        d="M15.925,19.741a8.537,8.537,0,0,1-3.747-1.51,20.942,20.942,0,0,1-3.524-3.094,51.918,51.918,0,0,1-3.759-4.28A13.13,13.13,0,0,1,2.75,6.867a6.3,6.3,0,0,1-.233-2.914,5.144,5.144,0,0,1,1.66-2.906A7.085,7.085,0,0,1,5.306.221,1.454,1.454,0,0,1,6.9.246a5.738,5.738,0,0,1,2.443,2.93,1.06,1.06,0,0,1-.117,1.072c-.283.382-.578.754-.863,1.136-.251.338-.512.671-.736,1.027a.946.946,0,0,0,.01,1.108c.564.791,1.11,1.607,1.723,2.36a30.024,30.024,0,0,0,3.672,3.8c.3.255.615.481.932.712a.892.892,0,0,0,.96.087,10.79,10.79,0,0,0,.989-.554c.443-.283.878-.574,1.314-.853a1.155,1.155,0,0,1,1.207-.024,5.876,5.876,0,0,1,2.612,2.572,1.583,1.583,0,0,1-.142,1.795,5.431,5.431,0,0,1-4.353,2.362A6.181,6.181,0,0,1,15.925,19.741Z"
                        transform="translate(-2.441 0.006)"></path>
                </svg>
                628986950700
            </a>
        </div>
    </nav>
    <nav class="z-10 bg-white flex sticky top-0 md:hidden justify-between px-6 pb-6 pt-6">
        <a href="https://berbagibitesjogja.com" class="flex items-center text-tosca text-2xl font-bold">
            <img src="{{ asset('assets/biru.png') }}" class="w-12" alt="">Berbagi Bites Jogja
        </a>
        <button onclick="openSidebar()">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
            </svg>
        </button>
    </nav>
    <nav class="hidden md:flex p-4 justify-between items-center bg-gray-50 px-44">
        <div class="flex items-center gap-6">
            <a class="flex items-center gap-3 text-gray-400" href="mailto:berbagibitesjogja@gmail.com">

                <svg xmlns="http://www.w3.org/2000/svg" fill="#0395AF" width="15" height="12.683"
                    viewBox="0 0 20 12.683">
                    <path id="Path_26505" data-name="Path 26505"
                        d="M10.463,976.362a1.465,1.465,0,0,0-.541.107l8.491,7.226a.825.825,0,0,0,1.159,0l8.5-7.233a1.469,1.469,0,0,0-.534-.1H10.463Zm-1.448,1.25a1.511,1.511,0,0,0-.015.213v9.756a1.46,1.46,0,0,0,1.463,1.463H27.537A1.46,1.46,0,0,0,29,987.581v-9.756a1.51,1.51,0,0,0-.015-.213l-8.46,7.2a2.376,2.376,0,0,1-3.064,0Z"
                        transform="translate(-9 -976.362)"></path>
                </svg>
                berbagibitesjogja@gmail.com
            </a>
            <a class="flex items-center gap-3 text-gray-400" href="https://wa.me/628986950700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#0395AF" class="rishi-icon" width="14"
                    height="19.788" viewBox="0 0 18.823 19.788">
                    <path id="Phone"
                        d="M15.925,19.741a8.537,8.537,0,0,1-3.747-1.51,20.942,20.942,0,0,1-3.524-3.094,51.918,51.918,0,0,1-3.759-4.28A13.13,13.13,0,0,1,2.75,6.867a6.3,6.3,0,0,1-.233-2.914,5.144,5.144,0,0,1,1.66-2.906A7.085,7.085,0,0,1,5.306.221,1.454,1.454,0,0,1,6.9.246a5.738,5.738,0,0,1,2.443,2.93,1.06,1.06,0,0,1-.117,1.072c-.283.382-.578.754-.863,1.136-.251.338-.512.671-.736,1.027a.946.946,0,0,0,.01,1.108c.564.791,1.11,1.607,1.723,2.36a30.024,30.024,0,0,0,3.672,3.8c.3.255.615.481.932.712a.892.892,0,0,0,.96.087,10.79,10.79,0,0,0,.989-.554c.443-.283.878-.574,1.314-.853a1.155,1.155,0,0,1,1.207-.024,5.876,5.876,0,0,1,2.612,2.572,1.583,1.583,0,0,1-.142,1.795,5.431,5.431,0,0,1-4.353,2.362A6.181,6.181,0,0,1,15.925,19.741Z"
                        transform="translate(-2.441 0.006)"></path>
                </svg>
                628986950700
            </a>
        </div>
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
                    <a class="@if (str_contains(request()->route()->getName(), 'volunteer')) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca @endif
                    py-2"
                        href="{{ route('volunteer.home') }}" class="text-gray-400 hover:text-tosca py-2">Home</a>
                @else
                    <a class="@if (str_contains(request()->route()->getName(), 'volunteer')) border-b-2 border-tosca-500 text-tosca
                    @else
                    hover-underline text-gray-400 hover:text-tosca @endif
                    py-2"
                        href="https://berbagibitesjogja.com/" class="text-gray-400 hover:text-tosca py-2">Home</a>
                @endauth
            </li>
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
                    py-2">Action</a>
                    <div
                        class="absolute transition-all duration-300 ease-in-out transform translate-y-[-10px] opacity-0 group-hover:translate-y-0 group-hover:opacity-100 z-10 bg-white divide-y mt-2 divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
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
                    py-2">Beneficiaries</a>
                    <div
                        class="absolute transition-all duration-300 ease-in-out transform translate-y-[-10px] opacity-0 group-hover:translate-y-0 group-hover:opacity-100 z-10 bg-white divide-y mt-2 divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
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
                    py-2">Partner</a>
                    <div
                        class="absolute transition-all duration-300 ease-in-out transform translate-y-[-10px] opacity-0 group-hover:translate-y-0 group-hover:opacity-100 z-10 bg-white divide-y mt-2 divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
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
                    py-2">Logs</a>
                        <div
                            class="absolute transition-all duration-300 ease-in-out transform translate-y-[-10px] opacity-0 group-hover:translate-y-0 group-hover:opacity-100 z-10 bg-white divide-y mt-2 divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
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
    <script>
        function closeSidebar() {
            document.querySelector('#mobile-sidebar').classList.add('hidden')
        }

        function openSidebar() {
            document.querySelector('#mobile-sidebar').classList.remove('hidden')
        }
        const dropdownToggles = document.querySelectorAll('[data-collapse-toggle]');

        dropdownToggles.forEach(function(toggle) {
            const targetId = toggle.getAttribute('data-collapse-toggle');
            const dropdownMenu = document.getElementById(targetId);

            toggle.addEventListener('click', function() {
                dropdownMenu.classList.toggle('hidden');
                dropdownMenu.classList.toggle('block');
            });
        });
    </script>
@endauth
@guest
    <header class='flex justify-between py-3 md:py-6 items-center px-6 md:px-32 sticky top-0 bg-white z-100'>
        <a href="https://berbagibitesjogja.com/" class='flex items-center gap-x-2'>
            <img src="{{ asset('assets/biru.png') }}" alt="bbj-logo" width='40' height='40' />
            <span class='text-navy font-bold text-xl md:text-2xl'>Berbagi Bites Jogja</span>
        </a>
        <nav>
            <ul class="hidden md:flex space-x-12">
                <li>
                    <a class='font-semibold text-sm' href="https://berbagibitesjogja.com/#program">Program</a>
                </li>
                <li>
                    <a class='font-semibold text-sm' href="https://berbagibitesjogja.com/#dampak">Dampak</a>
                </li>
                <li>
                    <a class='font-semibold text-sm' href="https://berbagibitesjogja.com/news">Artikel</a>
                </li>
                <li>
                    <a class='font-semibold text-sm' href="https://berbagibitesjogja.com/#gabung">Bergabung</a>
                </li>
                <li>
                    <a class='font-semibold text-sm' href="https://berbagibitesjogja.com/#kontak">Kontak</a>
                </li>
            </ul>
        </nav>
        <a class='bg-navy text-sm rounded-full h-max py-2 text-white font-semibold px-4 md:px-6 hidden md:block'
            href="https://wa.me/628986950700">Donasi Sekarang</a>
    </header>

@endguest
