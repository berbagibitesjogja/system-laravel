@extends('layouts.main')
@php
    $days = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
@endphp
@section('container')
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Left Column: Volunteer List --}}
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6">
                <div class="flex items-center gap-3 mb-6">
                    <span class="bg-tosca-100 text-tosca-600 p-2 rounded-lg text-xl">🕒</span>
                    <div>
                        <h1 class="text-xl font-bold text-navy-900">
                            {{ $days[intval($avails[0]->day)] }}
                        </h1>
                        <p class="text-sm font-medium text-navy-500">
                            Pukul {{ $avails[0]->hour }}.{{ str_pad($avails[0]->minute, 2, '0') }}
                        </p>
                    </div>
                </div>

                @if($users->isEmpty())
                    <div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <span class="text-4xl block mb-2">🏜️</span>
                        <p class="text-navy-400">Belum ada volunteer yang bersedia di jam ini.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($users as $item)
                            <div class="flex items-start gap-4 p-4 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md hover:border-tosca-200 transition-all group">
                                <img class="w-16 h-16 rounded-xl object-cover" src="{{ $item->photo }}" alt="{{ $item->name }}">
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-lg font-bold text-navy-900 truncate group-hover:text-tosca-600 transition-colors">
                                        {{ $item->name }}
                                    </h5>
                                    <p class="text-xs text-navy-400 mb-3 truncate">{{ $item->email }}</p>
                                    <a href="https://wa.me/{{ $item->phone }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-bold rounded-lg hover:bg-green-100 transition-colors">
                                        <span>💬</span> Chat WhatsApp
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column: Other Schedules --}}
        <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6 sticky top-6">
                <h2 class="text-lg font-bold text-navy-900 mb-4 flex items-center justify-between">
                    <span>Jadwal Lain</span>
                    <a href="{{ route('availability.dashboard') }}" class="text-xs font-semibold text-tosca-500 hover:text-tosca-600">
                        Atur Jadwal →
                    </a>
                </h2>
                
                <div class="space-y-3">
                    @for ($day = 1; $day <= 7; $day++)
                        <div x-data="{ open: false }" class="border border-navy-50 rounded-xl overflow-hidden">
                            <button @click="open = !open" class="flex items-center justify-between w-full p-3 bg-gray-50 hover:bg-gray-100 transition-colors text-sm">
                                <span class="font-semibold text-navy-700">{{ $days[$day] }}</span>
                                <span class="bg-white px-2 py-0.5 rounded text-xs text-navy-400 font-medium border border-gray-200">
                                    {{ $avail->get($day)->count() }} slot
                                </span>
                            </button>
                            <div x-show="open" class="p-3 bg-white border-t border-navy-50 flex flex-wrap gap-2 text-xs">
                                @foreach ($avail->get($day) as $item)
                                    <a href="{{ route('availability.time', $item->first()->code) }}"
                                        class="px-2 py-1 rounded bg-tosca-50 text-tosca-700 hover:bg-tosca-100 transition-colors border border-tosca-100">
                                        {{ $item->first()->hour }}.{{ str_pad($item->first()->minute, 2, '0') }}
                                        <span class="text-tosca-400 ml-1">({{ $item->count() }})</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.querySelectorAll("input[type=checkbox]").forEach(function(d) {
            d.addEventListener('change', function(e) {
                console.log({
                    target: e.target.value,
                    checked: e.target.checked
                })
            })
        })
    </script>
@endsection
