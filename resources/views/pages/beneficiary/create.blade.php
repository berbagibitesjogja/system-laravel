@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('beneficiary.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-gradient-to-r from-navy-500 to-tosca-500 p-6 text-center text-white font-bold rounded-t-xl">
            <h1 class="text-xl">Tambah Beneficiary</h1>
            <p class="text-sm font-normal text-white/80 mt-1">Daftarkan penerima manfaat baru</p>
        </div>
        <form method="POST" action="{{ route('beneficiary.store') }}" 
            class="bg-white shadow-lg px-8 py-8 rounded-b-xl border border-t-0 border-navy-100">
            @csrf
            
            <x-select name="variant" label="Pilih Jenis">
                <option value="">Jenis Beneficiary</option>
                <option value="student">Universitas</option>
                <option value="foundation">Yayasan</option>
                <option value="society">Masyarakat Umum</option>
            </x-select>
            
            <x-input name="name" label="Nama Beneficiary" required />
            
            <x-btn type="submit" variant="primary" class="w-full">
                Simpan Beneficiary
            </x-btn>
        </form>
    </div>
@endsection
