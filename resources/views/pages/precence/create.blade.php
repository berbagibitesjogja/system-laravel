@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('precence.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-gradient-to-r from-navy-500 to-tosca-500 p-6 text-center text-white font-bold rounded-t-xl">
            <h1 class="text-xl">Tambah Presensi</h1>
            <p class="text-sm font-normal text-white/80 mt-1">Buat sesi presensi baru</p>
        </div>
        <form method="POST" action="{{ route('precence.store') }}" 
            class="bg-white shadow-lg px-8 py-8 rounded-b-xl border border-t-0 border-navy-100">
            @csrf
            
            <x-input name="title" label="Judul" required />
            <x-input name="description" label="Deskripsi (opsional)" />
            
            @if ($user->role == 'super')
                <div class="p-4 mb-6 bg-navy-50 rounded-xl border border-navy-100">
                    <p class="text-sm font-semibold text-navy-700 mb-4">📍 Pengaturan Lokasi (Super Admin)</p>
                    <x-input name="latitude" label="Latitude" />
                    <x-input name="longitude" label="Longitude" />
                    <x-input name="max_distance" label="Jarak Maksimal (meter)" type="number" />
                </div>
            @endif
            
            <x-btn type="submit" variant="primary" class="w-full">
                Simpan Presensi
            </x-btn>
        </form>
    </div>
@endsection
