@extends('layouts.main')
@section('container')
    <div class="flex justify-between flex-wrap-reverse flex-row gap-y-4">
        <div class="flex md:w-max w-full justify-start gap-2">
        </div>
        <a href="{{ route('volunteer.show', $user->id) }}" class="flex md:w-max w-full justify-end gap-3 items-center group">
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
            <x-action-card href="{{ route('reimburse.create') }}" color="orange" icon="{{ asset('assets/donate.svg') }}">
                Ajukan Reimburse
            </x-action-card>
            
            @if ($user->role=='super' || $user->division->name=='Friend')
                <x-action-card href="{{ route('reimburse.index') }}" color="tosca" icon="{{ asset('assets/food.svg') }}">
                    Data Reimburse
                </x-action-card>
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

    <div class="mt-12 bg-white rounded-xl border border-navy-100 shadow-md p-6">
        <h1 class="font-bold text-navy-900 text-xl mb-6">Statistik Heroes {{ count($lastData) }} Bulan Terakhir</h1>
        <div class="h-64">
            <canvas id="heroStatistics"></canvas>
        </div>
    </div>
    
    <div class="mt-8 bg-white rounded-xl border border-navy-100 shadow-md p-6">
        <h1 class="font-bold text-navy-900 text-xl mb-6">Statistik Makanan {{ count($lastData) }} Bulan Terakhir</h1>
        <div class="h-64">
            <canvas id="foodStatistics"></canvas>
        </div>
    </div>
    
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
