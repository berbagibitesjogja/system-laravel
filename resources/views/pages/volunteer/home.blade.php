@extends('layouts.main')
@section('container')
    <div class="flex justify-between flex-wrap-reverse flex-row gap-y-3">
        <div class="flex md:w-max w-full justify-start gap-2">
            <div class="text-start">
                
            </div>

        </div>
        <a href="{{ route('volunteer.show', $user->id) }}" class="flex md:w-max w-full justify-end gap-2">
            <div class="text-end">
                <p>{{ $user->name }}</p>
                <p>{{ $user->division->name }} ({{ $user->attendances->count() }} Aksi)</p>
            </div>
            <div class="w-12 block rounded-full overflow-hidden">
                <img src="{{ $user->photo}}"
                    alt="">
            </div>

        </a>

    </div>
    <div class="flex justify-start flex-row gap-y-3 my-4 gap-x-4 flex-wrap">
        <x-nav-link href="{{ route('reimburse.create') }}">Ajukan Reimburse</x-nav-link>
        @if ($user->role=='super' || $user->division->name=='Friend')
        <x-nav-link href="{{ route('reimburse.index') }}">Reimburse</x-nav-link>
        <x-nav-link href="{{ route('precence.index') }}">Presensi</x-nav-link>
        @endif
        @if ($user->role!='member')
            
        <x-nav-link href="{{ route('volunteer.index') }}">Volunteer</x-nav-link>
        @endif
        @if ($precence==1)
        <x-nav-link href="{{ route('precence.qr', 'scan') }}">Scan QR</x-nav-link>
     
                <x-action-link href="{{ route('precence.qr', 'view') }}">
                    Lihat QR Code
                </x-action-link>
                <x-action-link href="{{ route('precence.qr', 'download') }}">
                        Download QR Code
                </x-action-link>
            
        @endif
    </div>
    <div class="grid sm:grid-cols-2 md:grid-cols-4 grid-cols-1 gap-2 mt-3">
        <x-stat-card 
            icon="{{ asset('assets/donate.svg') }}" 
            label="Food Rescue" 
            value="{{ $donations->count() }} Aksi" 
        />
        
        <x-stat-card 
            icon="{{ asset('assets/food.svg') }}" 
            label="Food Rescue" 
            value="{{ round($foods->sum('weight') / 1000) }} Kg" 
        />
        
        <x-stat-card 
            icon="{{ asset('assets/people.svg') }}" 
            label="Total Heroes" 
            value="{{ $heroes->sum('quantity') }} Orang" 
        />
    </div>
    <h1 class="font-bold text-navy text-xl md:text-2xl my-12">Statistik Heroes {{ count($lastData) }} Bulan Terakhir</h1>
    <div>
        <canvas id="heroStatistics" class="h-max"></canvas>
    </div>
    <h1 class="font-bold text-navy text-xl md:text-2xl my-12">Statistik Makanan {{ count($lastData) }} Bulan Terakhir</h1>
    <div>
        <canvas id="foodStatistics" class="h-max"></canvas>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const heroStatistics = document.getElementById('heroStatistics');
        const foodStatistics = document.getElementById('foodStatistics');
        let monthName = `{{!! json_encode(array_column($lastData, 'bulan')) !!}}`
        let heroSum = `{{!! json_encode(array_column($lastData, 'heroes')) !!}}`
        let foodSum = `{{!! json_encode(array_column($lastData, 'foods')) !!}}`
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
                    tension: 0.25,
                    borderWidth:2.5,
                    backgroundColor:'rgba(33, 86, 138, 0.1)'
                }]
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
                    tension: 0.25,
                    borderWidth:2.5,
                    backgroundColor:'rgba(3, 149, 175, 0.1)'
                }]
            }
        })
        
    </script>
@endsection
