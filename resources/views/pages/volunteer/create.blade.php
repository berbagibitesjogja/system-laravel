@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center">
        <x-btn-link href="{{ route('volunteer.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
    </div>

    <div class="mt-8 max-w-lg mx-auto">
        <div class="bg-gradient-to-r from-navy-500 to-tosca-500 p-6 text-center text-white font-bold rounded-t-xl">
            <h1 class="text-xl">Tambah Volunteer</h1>
            <p class="text-sm font-normal text-white/80 mt-1">Daftarkan anggota baru ke tim BBJ</p>
        </div>
        <form method="POST" action="{{ route('volunteer.store') }}" 
            class="bg-white shadow-lg px-8 py-8 rounded-b-xl border border-t-0 border-navy-100"
            enctype="multipart/form-data">
            @csrf
            
            <x-select name="division_id" label="Pilih Divisi">
                <option value="">Pilih Divisi</option>
                @foreach ($divisions as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </x-select>
            
            <x-select name="role" label="Pilih Role">
                <option value="super">Super</option>
                <option value="core">Inti</option>
                <option value="staff">Staff</option>
                <option value="member">Volunteer</option>
            </x-select>
            
            <input type="hidden" name="name" value="BELUM">
            
            <x-input name="email" label="Email" type="email" required />
            
            <x-input name="phone" label="Nomor WhatsApp" type="tel" 
                title="Nomor telepon harus diawali dengan 08"
                required />

            <div class="mt-6">
                <x-btn type="submit" variant="primary" class="w-full">
                    Simpan Volunteer
                </x-btn>
            </div>
        </form>
    </div>
@endsection
