@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('sponsor.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-gradient-to-r from-navy-500 to-tosca-500 p-6 text-center text-white font-bold rounded-t-xl">
            <h1 class="text-xl">Tambah Sponsor BBJ</h1>
            <p class="text-sm font-normal text-white/80 mt-1">Daftarkan donatur atau sponsor baru</p>
        </div>
        <form method="POST" action="{{ route('sponsor.store') }}" 
            class="bg-white shadow-lg px-8 py-8 rounded-b-xl border border-t-0 border-navy-100">
            @csrf
            
            <x-input name="name" label="Nama Sponsor" required />
            <x-input name="address" label="Alamat (opsional)" />
            <x-input name="phone" label="Telepon (opsional)" />
            <x-input name="email" label="Email (opsional)" type="email" />

            <div class="flex flex-wrap gap-4 mb-6">
                <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-navy-100 hover:border-tosca-300 cursor-pointer transition-colors">
                    <input type="checkbox" name="hidden" id="hidden" class="rounded text-tosca-500 focus:ring-tosca-300">
                    <span class="text-sm text-navy-700">🙈 Sembunyikan</span>
                </label>
                <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-navy-100 hover:border-tosca-300 cursor-pointer transition-colors">
                    <input type="checkbox" name="variant" id="variant" class="rounded text-tosca-500 focus:ring-tosca-300">
                    <span class="text-sm text-navy-700">👤 Individu</span>
                </label>
            </div>
            
            <x-btn type="submit" variant="primary" class="w-full">
                Simpan Sponsor
            </x-btn>
        </form>
    </div>
@endsection
