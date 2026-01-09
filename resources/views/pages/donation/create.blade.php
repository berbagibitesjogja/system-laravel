@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('donation.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-gradient-to-r from-navy-500 to-tosca-500 p-6 text-center text-white font-bold rounded-t-xl">
            <h1 class="text-xl">Tambah Donasi</h1>
            <p class="text-sm font-normal text-white/80 mt-1">Buat aksi food rescue baru</p>
        </div>
        <form method="POST" action="{{ route('donation.store') }}" 
            class="bg-white shadow-lg px-8 py-8 rounded-b-xl border border-t-0 border-navy-100">
            @csrf
            
            <x-select name="sponsor_id" label="Pilih Sponsor">
                <option value="">Donatur / Sponsor</option>
                @foreach ($sponsors as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </x-select>
            
            <x-input name="quota" label="Kuota" type="number" required />
            <x-input name="take" label="Tanggal Pengambilan" type="date" required />
            
            <div class="grid grid-cols-3 gap-4 mb-6">
                <x-input name="hour" label="Jam" type="number" required />
                <div class="flex items-center justify-center text-navy-400 font-bold">:</div>
                <x-input name="minute" label="Menit" type="number" />
            </div>
            
            <x-input name="duration" label="Durasi Pengambilan (menit) *opsional" type="number" />
            <x-input name="location" label="Lokasi" value="Pusat Studi Pancasila" required />
            <x-input name="maps" label="Link Maps" value="https://maps.app.goo.gl/eesnA6CN5fAQrGfP9" required />
            <x-input name="message" label="Pesan Khusus (opsional)" />

            <div class="flex flex-wrap gap-4 mb-6">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" value="charity" name="charity" class="sr-only peer">
                    <div class="relative w-11 h-6 bg-navy-100 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-tosca-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tosca-500"></div>
                    <span class="ms-3 text-sm font-medium text-navy-700">Donasi</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" value="notify" name="notify" class="sr-only peer">
                    <div class="relative w-11 h-6 bg-navy-100 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-tosca-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tosca-500"></div>
                    <span class="ms-3 text-sm font-medium text-navy-700">Umumkan</span>
                </label>
            </div>

            <div class="mb-6">
                <p class="text-sm font-semibold text-navy-700 mb-3">Beneficiaries:</p>
                <div class="flex gap-3 flex-row flex-wrap">
                    @foreach ($universities as $item)
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-navy-100 hover:border-tosca-300 cursor-pointer transition-colors">
                            <input type="checkbox" name="beneficiaries[]" value="{{ $item->id }}" class="rounded text-tosca-500 focus:ring-tosca-300">
                            <span class="text-sm text-navy-700">{{ $item->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            
            @session('error')
                <p class="text-sm italic mb-4 text-red-500 bg-red-50 p-3 rounded-lg">⚠️ Pilih satu beneficiary</p>
            @endsession
            
            <x-btn type="submit" variant="primary" class="w-full">
                Simpan Donasi
            </x-btn>
        </form>
    </div>
@endsection
