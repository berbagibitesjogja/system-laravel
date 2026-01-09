@extends('layouts.main')
@php
    $days = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
@endphp
@section('container')
    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
    
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Desktop Schedule Grid --}}
        <div class="hidden lg:block w-full lg:w-2/3">
            <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6 h-max">
                <h1 class="text-xl font-bold text-navy-900 mb-6 flex items-center gap-2">
                    <span class="bg-tosca-100 text-tosca-600 p-2 rounded-lg">📅</span>
                    Jadwal Kesediaan Anda
                </h1>
                
                <div class="overflow-x-auto">
                    <div class="grid grid-cols-8 gap-2 min-w-[600px]">
                        {{-- Header Row --}}
                        <div class="font-bold text-navy-400 text-center py-2">Jam</div>
                        @for ($i = 1; $i <= 7; $i++)
                            <div class="font-bold text-navy-700 text-center py-2 bg-navy-50 rounded-lg">
                                {{ $days[$i] }}
                            </div>
                        @endfor

                        {{-- Time Slots --}}
                        @for ($i = 1; $i <= 15; $i++)
                            {{-- Full Hour --}}
                            <div class="text-sm font-medium text-navy-500 flex items-center justify-center">
                                {{ $i + 6 }}.00
                            </div>
                            @for ($j = 1; $j <= 7; $j++)
                                @php
                                    $cod = array_shift($code);
                                    $checked = in_array($cod, $availabilities);
                                @endphp
                                <div class="flex items-center justify-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" value="{{ $cod }}" {{ $checked ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-6 h-6 bg-gray-100 border-2 border-gray-200 rounded-md peer peer-checked:bg-tosca-500 peer-checked:border-tosca-500 peer-checked:after:content-['✓'] peer-checked:after:text-white peer-checked:after:text-xs peer-checked:after:flex peer-checked:after:justify-center peer-checked:after:items-center peer-checked:after:h-full peer-focus:ring-2 peer-focus:ring-tosca-300 transition-all duration-200 hover:bg-gray-200"></div>
                                    </label>
                                </div>
                            @endfor

                            {{-- Half Hour --}}
                            <div class="text-sm font-medium text-navy-500 flex items-center justify-center">
                                {{ $i + 6 }}.30
                            </div>
                            @for ($j = 1; $j <= 7; $j++)
                                @php
                                    $cod = array_shift($code);
                                    $checked = in_array($cod, $availabilities);
                                @endphp
                                <div class="flex items-center justify-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" value="{{ $cod }}" {{ $checked ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-6 h-6 bg-gray-100 border-2 border-gray-200 rounded-md peer peer-checked:bg-tosca-500 peer-checked:border-tosca-500 peer-checked:after:content-['✓'] peer-checked:after:text-white peer-checked:after:text-xs peer-checked:after:flex peer-checked:after:justify-center peer-checked:after:items-center peer-checked:after:h-full peer-focus:ring-2 peer-focus:ring-tosca-300 transition-all duration-200 hover:bg-gray-200"></div>
                                    </label>
                                </div>
                            @endfor
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile/Summary View --}}
        <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6 sticky top-6">
                <h1 class="text-xl font-bold text-navy-900 mb-6 flex items-center gap-2">
                    <span class="bg-orange-100 text-orange-500 p-2 rounded-lg">👥</span>
                    Kesediaan Volunteer
                </h1>
                
                <div class="space-y-4">
                    @for ($day = 1; $day <= 7; $day++)
                        <div x-data="{ open: false }" class="border border-navy-100 rounded-xl overflow-hidden">
                            <button @click="open = !open" class="flex items-center justify-between w-full p-4 bg-gray-50 hover:bg-gray-100 transition-colors">
                                <span class="font-semibold text-navy-700">{{ $days[$day] }}</span>
                                <svg class="w-5 h-5 text-navy-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" class="p-4 bg-white border-t border-navy-100 flex flex-wrap gap-2">
                                @if($avail->get($day)->isEmpty())
                                    <p class="text-sm text-navy-400 italic">Tidak ada data</p>
                                @else
                                    @foreach ($avail->get($day) as $item)
                                        <a href="{{ route('availability.time', $item->first()->code) }}"
                                            class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-tosca-50 text-tosca-700 hover:bg-tosca-100 transition-colors border border-tosca-200">
                                            {{ $item->first()->hour }}.{{ str_pad($item->first()->minute, 2, '0') }}
                                            <span class="ml-1.5 bg-tosca-200 text-tosca-800 text-xs px-1.5 rounded-full">{{ $item->count() }}</span>
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="mt-6 lg:hidden">
                    <p class="text-sm text-center text-navy-400 italic">
                        Buka di desktop untuk mengatur jadwal kesediaan Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.querySelectorAll("input[type=checkbox]").forEach(function(d) {
            d.addEventListener('change', function(e) {
                fetch(`availability/${e.target.value}/${e.target.checked}`)
                    .then(() => {
                        // Optional: Show a small toast notification on success
                    });
            })
        })
    </script>
@endsection
