@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('donation.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-gradient-to-r from-orange-500 to-yellow-400 p-6 text-center text-white font-bold rounded-t-xl">
            <h1 class="text-xl">Edit Donasi</h1>
            <p class="text-sm font-normal text-white/80 mt-1">{{ $donation->sponsor->name }}</p>
        </div>
        <form method="POST" action="{{ route('donation.update', $donation->id) }}" 
            class="bg-white shadow-lg px-8 py-8 rounded-b-xl border border-t-0 border-navy-100">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label class="block mb-2 text-sm font-semibold text-navy-700">Donatur</label>
                <input value="{{ $donation->sponsor->name }}" type="text"
                    class="w-full px-4 py-3 text-sm text-navy-400 bg-navy-50 border border-navy-200 rounded-xl cursor-not-allowed"
                    disabled readonly>
            </div>
            
            <div class="mb-6">
                <label class="block mb-2 text-sm font-semibold text-navy-700">Kuota Saat Ini</label>
                <input value="{{ $donation->quota }}" type="text"
                    class="w-full px-4 py-3 text-sm text-navy-400 bg-navy-50 border border-navy-200 rounded-xl cursor-not-allowed"
                    disabled readonly>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <x-input name="add" label="Tambah Kuota" type="number" />
                <x-input name="diff" label="Kurangi Kuota" type="number" />
            </div>
            
            <x-input name="take" label="Tanggal Pengambilan" type="date" value="{{ $donation->take }}" required />
            
            <div class="grid grid-cols-3 gap-4 mb-6">
                <x-input name="hour" label="Jam" type="number" value="{{ $donation->hour }}" required />
                <div class="flex items-center justify-center text-navy-400 font-bold">:</div>
                <x-input name="minute" label="Menit" type="number" value="{{ sprintf('%02d', $donation->minute) }}" />
            </div>
            
            <x-input name="message" label="Pesan Khusus (opsional)" value="{{ $donation->message }}" />

            <div class="flex flex-wrap gap-4 mb-6">
                <label class="inline-flex items-center cursor-pointer">
                    <input {{ $donation->charity ? 'checked' : '' }} type="checkbox" value="charity" name="charity" class="sr-only peer">
                    <div class="relative w-11 h-6 bg-navy-100 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-tosca-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tosca-500"></div>
                    <span class="ms-3 text-sm font-medium text-navy-700">Donasi</span>
                </label>
            </div>
            
            @if ($donation->beneficiaries)
                <div class="mb-6">
                    <p class="text-sm font-semibold text-navy-700 mb-3">Beneficiaries:</p>
                    <div class="flex gap-3 flex-row flex-wrap">
                        @foreach ($universities as $item)
                            <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-navy-100 hover:border-tosca-300 cursor-pointer transition-colors">
                                <input type="checkbox" name="beneficiaries[]" value="{{ $item->id }}"
                                    {{ in_array($item->id, json_decode($donation->beneficiaries)) ? 'checked' : '' }}
                                    class="rounded text-tosca-500 focus:ring-tosca-300">
                                <span class="text-sm text-navy-700">{{ $item->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <div class="mb-6">
                <p class="text-sm font-semibold text-navy-700 mb-3">Status:</p>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all
                        {{ $donation->status == 'aktif' ? 'border-lime-500 bg-lime-50' : 'border-navy-100 hover:border-navy-200' }}">
                        <input type="radio" name="status" value="aktif" {{ $donation->status == 'aktif' ? 'checked' : '' }}
                            class="text-lime-500 focus:ring-lime-300">
                        <span class="text-sm font-medium text-navy-700">🟢 Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all
                        {{ $donation->status == 'selesai' ? 'border-navy-500 bg-navy-50' : 'border-navy-100 hover:border-navy-200' }}">
                        <input type="radio" name="status" value="selesai" {{ $donation->status == 'selesai' ? 'checked' : '' }}
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
