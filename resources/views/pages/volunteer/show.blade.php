@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ url()->previous() }}" variant="ghost">
            ← Kembali
        </x-btn-link>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-gradient-to-r from-orange-500 to-yellow-400 p-6 text-center text-white font-bold rounded-t-xl">
            <h1 class="text-xl">Edit Volunteer</h1>
            <p class="text-sm font-normal text-white/80 mt-1">{{ $volunteer->name }}</p>
        </div>
        <form method="POST" action="{{ route('volunteer.update', $volunteer->id) }}" 
            class="bg-white shadow-lg px-8 py-8 rounded-b-xl border border-t-0 border-navy-100"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            @if (auth()->user()->role == 'super')
                <x-select name="division_id" label="Pilih Divisi">
                    <option value="">Divisi</option>
                    @foreach ($divisions as $item)
                        <option {{ $volunteer->division_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                            {{ $item->name }}
                        </option>
                    @endforeach
                </x-select>
                
                <x-select name="role" label="Pilih Role">
                    <option {{ $volunteer->role == 'super' ? 'selected' : '' }} value="super">Super</option>
                    <option {{ $volunteer->role == 'core' ? 'selected' : '' }} value="core">Inti</option>
                    <option {{ $volunteer->role == 'staff' ? 'selected' : '' }} value="staff">Staff</option>
                    <option {{ $volunteer->role == 'member' ? 'selected' : '' }} value="member">Volunteer</option>
                </x-select>
            @endif

            <x-input name="email" label="Email" type="email" value="{{ $volunteer->email }}" required />
            <x-input name="phone" label="Whatsapp" type="tel" value="{{ $volunteer->phone }}" required />
            
            <x-btn type="submit" variant="primary" class="w-full mt-4">
                Simpan Perubahan
            </x-btn>
        </form>
    </div>
@endsection
