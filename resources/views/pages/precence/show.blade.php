@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('precence.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
        <x-btn-link href="{{ route('precence.qr', $precence->id) }}" variant="navy">
            📥 Download QR
        </x-btn-link>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6 mb-8">
        <h1 class="text-2xl font-bold text-navy-900 mb-2">{{ $precence->title }}</h1>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
            <div class="bg-gradient-to-br from-white to-navy-50 p-4 rounded-xl border border-navy-100">
                <p class="text-sm text-navy-400">Tanggal</p>
                <p class="text-lg font-bold text-navy-900">{{ $precence->created_at->isoFormat('D MMMM Y') }}</p>
            </div>
            <div class="bg-gradient-to-br from-white to-tosca-50 p-4 rounded-xl border border-navy-100">
                <p class="text-sm text-navy-400">Hadir</p>
                <p class="text-2xl font-bold text-tosca-600">{{ $precence->attendance->count() }} <span class="text-sm text-navy-400">Orang</span></p>
            </div>
        </div>
    </div>

    @if ($user->role == 'super' || $user->division->name == 'Friend')
        <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6 mb-8">
            <form method="POST" action="{{ route('attendance.manual', ['precence' => $precence->id]) }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <x-select name="user_id" label="Masukkan Manual">
                        <option value="">Pilih Volunteer</option>
                        @foreach ($yet as $vol)
                            <option value="{{ $vol->id }}">{{ $vol->name }}</option>
                        @endforeach
                    </x-select>
                </div>
                <x-btn type="submit" variant="primary">
                    + Tambah
                </x-btn>
            </form>
        </div>
    @endif
    
    <x-table>
        <x-slot:head>
            <x-th>Nama</x-th>
            <x-th>Waktu</x-th>
            <x-th>Jarak</x-th>
            <x-th class="hidden sm:table-cell">Poin</x-th>
        </x-slot:head>
        <x-slot:body>
            @foreach ($precence->attendance as $item)
                <x-tr>
                    <x-td>
                        <p class="font-semibold text-navy-900">{{ $item->user->name }}</p>
                    </x-td>
                    <x-td>
                        <span class="text-navy-600">{{ $item->created_at->isoFormat('HH:mm') }}</span>
                    </x-td>
                    <x-td>
                        <span class="font-medium {{ $item->distance <= 100 ? 'text-lime-600' : 'text-orange-500' }}">
                            {{ $item->distance }} m
                        </span>
                    </x-td>
                    <x-td class="hidden sm:table-cell">
                        <form action="{{ route('precence.update', $precence->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="attendance_id" value="{{ $item->id }}">
                            <input type="number" 
                                class="w-20 px-3 py-2 text-center border border-navy-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 transition-all duration-300" 
                                placeholder="0" name="point" value="{{ $item->point }}">
                        </form>
                    </x-td>
                </x-tr>
            @endforeach
        </x-slot:body>
    </x-table>
@endsection
