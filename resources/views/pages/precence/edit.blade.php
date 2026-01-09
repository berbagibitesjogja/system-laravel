@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('precence.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-gradient-to-r from-orange-500 to-yellow-400 p-6 text-center text-white font-bold rounded-t-xl">
            <h1 class="text-xl">Edit Presensi</h1>
            <p class="text-sm font-normal text-white/80 mt-1">{{ $precence->title }}</p>
        </div>
        <form method="POST" action="{{ route('precence.update', $precence->id) }}" 
            class="bg-white shadow-lg px-8 py-8 rounded-b-xl border border-t-0 border-navy-100">
            @csrf
            @method('PUT')
            
            <x-input name="title" label="Judul" value="{{ $precence->title }}" required />
            <x-input name="description" label="Deskripsi (opsional)" value="{{ $precence->description }}" />
            
            @if ($user->role == 'super')
                <div class="p-4 mb-6 bg-navy-50 rounded-xl border border-navy-100">
                    <p class="text-sm font-semibold text-navy-700 mb-4">📍 Pengaturan Lokasi (Super Admin)</p>
                    <x-input name="latitude" label="Latitude" value="{{ $precence->latitude }}" />
                    <x-input name="longitude" label="Longitude" value="{{ $precence->longitude }}" />
                    <x-input name="max_distance" label="Jarak Maksimal (meter)" type="number" value="{{ $precence->max_distance }}" />
                </div>
            @endif
            
            <div class="mb-6">
                <p class="text-sm font-semibold text-navy-700 mb-3">Status:</p>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all
                        {{ $precence->status == 'active' ? 'border-lime-500 bg-lime-50' : 'border-navy-100 hover:border-navy-200' }}">
                        <input type="radio" name="status" value="active" {{ $precence->status == 'active' ? 'checked' : '' }}
                            class="text-lime-500 focus:ring-lime-300">
                        <span class="text-sm font-medium text-navy-700">🟢 Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all
                        {{ $precence->status == 'end' ? 'border-navy-500 bg-navy-50' : 'border-navy-100 hover:border-navy-200' }}">
                        <input type="radio" name="status" value="end" {{ $precence->status == 'end' ? 'checked' : '' }}
                            class="text-navy-500 focus:ring-navy-300">
                        <span class="text-sm font-medium text-navy-700">✓ Selesai</span>
                    </label>
                </div>
            </div>
            
            <x-btn type="submit" variant="primary" class="w-full">
                Simpan Perubahan
            </x-btn>
        </form>
    </div>
@endsection
