@extends('layouts.main')
@section('container')
    <div class="mt-3 flex gap-3 w-max">
        <x-btn-link href="{{ route('volunteer.index') }}" variant="warning">
            < Kembali
        </x-btn-link>
    </div>

    <div class="mt-5 max-w-md mx-auto bg-navy p-5 text-center text-white font-bold rounded-t-md">
        Tambah Volunteer
    </div>
    <form method="POST" action="{{ route('volunteer.store') }}" class="max-w-md mx-auto shadow-md px-10  py-6 rounded-b-md"
        enctype="multipart/form-data">
        @csrf
        <x-select name="division_id" label="Pilih Divisi">
            <option value="">Divisi</option>
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
        
        <x-input name="phone" label="Whatsapp" type="tel" 
            title="Nomor telepon harus diawali dengan 62 dan diikuti minimal 8 digit angka"
            required />

        <x-btn variant="primary">Submit</x-btn>
    </form>
@endsection
