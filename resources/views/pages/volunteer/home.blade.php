@extends('layouts.main')
@section('container')
    <div class="flex justify-between flex-wrap-reverse flex-row gap-y-4">
        <div class="flex md:w-max w-full justify-start gap-2">
        </div>
        <a href="{{ route('volunteer.profile') }}" class="flex md:w-max w-full justify-end gap-3 items-center group">
            <div class="text-end">
                <p class="font-semibold text-navy-900 group-hover:text-navy-600 transition-colors">{{ $user->name }}</p>
                <p class="text-sm text-navy-400">{{ $user->division->name }} ({{ $user->attendances->count() }} Aksi)</p>
            </div>
            <div class="w-12 h-12 rounded-xl overflow-hidden ring-2 ring-navy-100 group-hover:ring-tosca-300 transition-all duration-300">
                <img src="{{ $user->photo }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
            </div>
        </a>
    </div>

    <div class="mt-8">
        <h2 class="text-lg font-bold text-navy-900 mb-4 flex items-center gap-2">
            <span class="p-1.5 bg-orange-100 text-orange-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </span>
            Aksi Cepat
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- <x-action-card href="{{ route('reimburse.create') }}" color="orange" icon="{{ asset('assets/donate.svg') }}">
                Ajukan Reimburse
            </x-action-card> --}}
            
            @if ($user->role=='super' || $user->division->name=='Friend')
                {{-- <x-action-card href="{{ route('reimburse.index') }}" color="tosca" icon="{{ asset('assets/food.svg') }}">
                    Data Reimburse
                </x-action-card> --}}
                <x-action-card href="{{ route('precence.index') }}" color="navy" icon="{{ asset('assets/people.svg') }}">
                    Presensi Volunteer
                </x-action-card>
            @endif
            
            @if ($user->role!='member')
                <x-action-card href="{{ route('volunteer.index') }}" color="lime" icon="{{ asset('assets/hero.svg') }}">
                    Manajemen Volunteer
                </x-action-card>
            @endif
            
            @if ($precence==1)
                <x-action-card href="{{ route('precence.qr', 'scan') }}" color="tosca" icon="{{ asset('assets/people.svg') }}">
                    Scan QR Presensi
                </x-action-card>
            @endif
        </div>
    </div>

    <div class="mt-10">
        <h2 class="text-lg font-bold text-navy-900 mb-4">Dampak Kami</h2>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 grid-cols-1 gap-4">
            <x-stat-card 
                icon="{{ asset('assets/donate.svg') }}" 
                label="Food Rescue" 
                value="{{ $donations->count() }} Aksi" 
            />
            
            <x-stat-card 
                icon="{{ asset('assets/food.svg') }}" 
                label="Makanan Diselamatkan" 
                value="{{ round($foods->sum('weight') / 1000) }} Kg" 
            />
            
            <x-stat-card 
                icon="{{ asset('assets/people.svg') }}" 
                label="Total Heroes" 
                value="{{ $heroes->sum('quantity') }} Orang" 
            />
        </div>
    </div>

    <div class="mt-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Charts Section --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6">
                <h1 class="font-bold text-navy-900 text-xl mb-6 flex items-center gap-2">
                    <span class="w-2 h-6 bg-tosca-500 rounded-full"></span>
                    Statistik Heroes
                </h1>
                <div class="h-64">
                    <canvas id="heroStatistics"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6">
                <h1 class="font-bold text-navy-900 text-xl mb-6 flex items-center gap-2">
                    <span class="w-2 h-6 bg-navy-500 rounded-full"></span>
                    Statistik Makanan
                </h1>
                <div class="h-64">
                    <canvas id="foodStatistics"></canvas>
                </div>
            </div>
        </div>

        {{-- Activity Timeline --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6 h-full flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-bold text-navy-900 text-xl flex items-center gap-2">
                        <span class="p-1.5 bg-navy-100 text-navy-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        Aktivitas Terbaru
                    </h2>
                    @if(auth()->user()->role == 'super')
                        <a href="{{ route('logs.activity') }}" class="text-xs font-bold text-tosca-600 hover:text-tosca-700 uppercase tracking-wider">Lihat Semua</a>
                    @endif
                </div>

                <div class="flex-1 space-y-6 relative">
                    {{-- Vertical Line --}}
                    <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-navy-50"></div>

                    @forelse($activities as $activity)
                        <div class="relative pl-10">
                            <div class="absolute left-2.5 top-1.5 w-3.5 h-3.5 rounded-full border-2 border-white 
                                {{ $activity->event === 'created' ? 'bg-lime-500 shadow-[0_0_0_4px_rgba(132,204,22,0.1)]' : 
                                   ($activity->event === 'deleted' ? 'bg-red-500 shadow-[0_0_0_4px_rgba(239,68,68,0.1)]' : 'bg-navy-500 shadow-[0_0_0_4px_rgba(33,86,138,0.1)]') }}">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-navy-900 leading-tight">
                                    {{ $activity->causer ? $activity->causer->name : 'System' }}
                                    <span class="font-normal text-navy-400">
                                        {{ $activity->description }}
                                        @if($activity->subject_type)
                                            {{ class_basename($activity->subject_type) }}
                                        @endif
                                    </span>
                                </p>
                                <p class="text-[10px] font-bold text-navy-300 uppercase mt-1">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <p class="text-sm text-navy-400">Belum ada aktivitas tercatat.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role != 'member')
        <x-fab onClick="window.location.href='{{ route('donation.create') }}'" label="Tambah Donasi" />
    @endif
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const heroStatistics = document.getElementById('heroStatistics');
        const foodStatistics = document.getElementById('foodStatistics');
        let monthName = `{!! json_encode(array_column($lastData, 'bulan')) !!}`
        let heroSum = `{!! json_encode(array_column($lastData, 'heroes')) !!}`
        let foodSum = `{!! json_encode(array_column($lastData, 'foods')) !!}`
        monthName = JSON.parse(monthName.replace('{','').replace('}',''))
        heroSum = JSON.parse(heroSum.replace('{','').replace('}',''))
        foodSum = JSON.parse(foodSum.replace('{','').replace('}',''))
        new Chart(foodStatistics, {
            type: 'line',
            data: {
                labels: monthName,
                datasets: [{
                    label: 'Surplus Food (kg)',
                    data: foodSum,
                    fill: true,
                    borderColor: '#21568A',
                    tension: 0.4,
                    borderWidth: 3,
                    backgroundColor: 'rgba(33, 86, 138, 0.1)',
                    pointBackgroundColor: '#21568A',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        })
        new Chart(heroStatistics, {
            type: 'line',
            data: {
                labels: monthName,
                datasets: [{
                    label: 'Penerima',
                    data: heroSum,
                    fill: true,
                    borderColor: '#0395AF',
                    tension: 0.4,
                    borderWidth: 3,
                    backgroundColor: 'rgba(3, 149, 175, 0.1)',
                    pointBackgroundColor: '#0395AF',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        })
        
    </script>
@endsection
