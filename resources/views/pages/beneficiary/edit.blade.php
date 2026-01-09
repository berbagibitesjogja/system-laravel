@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('beneficiary.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-gradient-to-r from-orange-500 to-yellow-400 p-6 text-center text-white font-bold rounded-t-xl">
            <h1 class="text-xl">Ubah Beneficiary</h1>
            <p class="text-sm font-normal text-white/80 mt-1">{{ $beneficiary->name }}</p>
        </div>
        <form method="POST" action="{{ route('beneficiary.update', $beneficiary->id) }}" 
            class="bg-white shadow-lg px-8 py-8 rounded-b-xl border border-t-0 border-navy-100">
            @csrf
            @method('PUT')
            
            <x-select name="variant" label="Pilih Jenis">
                <option value="">Jenis Beneficiary</option>
                <option {{ $beneficiary->variant == 'student' ? 'selected' : '' }} value="student">Universitas</option>
                <option {{ $beneficiary->variant == 'foundation' ? 'selected' : '' }} value="foundation">Yayasan</option>
                <option {{ $beneficiary->variant == 'society' ? 'selected' : '' }} value="society">Masyarakat Umum</option>
            </x-select>
            
            <x-input name="name" label="Nama Beneficiary" value="{{ $beneficiary->name }}" required />
            
            <x-btn type="submit" variant="primary" class="w-full">
                Simpan Perubahan
            </x-btn>
        </form>
    </div>
@endsection
