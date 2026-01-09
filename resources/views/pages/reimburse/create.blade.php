@extends('layouts.main')

@section('container')
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-wrap gap-3 items-center mb-8">
            <x-btn-link href="{{ route('volunteer.home') }}" variant="ghost">
                ← Kembali
            </x-btn-link>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl border border-navy-100 shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-navy-500 to-tosca-500 p-6 text-white">
                    <h1 class="text-xl font-bold">Ajukan Reimburse</h1>
                    <p class="text-sm text-white/80 mt-1">Lengkapi form untuk mengajukan penggantian biaya</p>
                </div>

                <form action="{{ route('reimburse.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    
                    <x-input name="method" label="Metode Pembayaran" placeholder="Contoh: BCA/BNI/ShopeePay/GoPay" />
                    
                    <x-input name="target" label="Nomor Tujuan" placeholder="Contoh: 08912134452/1110003333" />

                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-semibold text-navy-700">Upload Invoice</label>
                        <div class="relative">
                            <input type="file" name="file" id="file"
                                class="w-full text-sm text-navy-600 
                                    file:mr-4 file:py-2.5 file:px-5 
                                    file:rounded-xl file:border-0 
                                    file:text-sm file:font-semibold 
                                    file:bg-tosca-500 file:text-white 
                                    hover:file:bg-tosca-600
                                    file:transition-colors file:duration-300
                                    file:cursor-pointer
                                    border border-navy-200 rounded-xl
                                    focus:outline-none focus:ring-2 focus:ring-tosca-300">
                        </div>
                    </div>

                    <x-btn type="submit" variant="primary" class="w-full">
                        📤 Ajukan Reimburse
                    </x-btn>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-navy-100 shadow-md overflow-hidden">
                <div class="bg-navy-50 p-6 border-b border-navy-100">
                    <h1 class="text-xl font-bold text-navy-900">Riwayat Pengajuan</h1>
                    <p class="text-sm text-navy-400 mt-1">Pantau status pengajuan reimburse Anda</p>
                </div>

                <div class="p-4">
                    @forelse ($reimburse as $item)
                        <div class="p-4 mb-3 rounded-xl border border-navy-100 hover:border-tosca-300 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold text-navy-900">{{ $item->method }}</p>
                                    <p class="text-sm text-navy-400">{{ $item->target }}</p>
                                </div>
                                @if ($item->done)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-lime-400 text-navy-900">
                                        ✓ Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-300 text-navy-900">
                                        ⏳ Proses
                                    </span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-lg text-tosca-600">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </span>
                                <a href="{{ asset('storage/' . $item->file) }}" target="_blank"
                                    class="text-sm text-tosca-500 hover:text-tosca-600 font-medium transition-colors">
                                    📄 Lihat File
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <span class="text-5xl mb-3">📋</span>
                            <p class="text-navy-400">Belum ada pengajuan</p>
                            <p class="text-sm text-navy-300">Ajukan reimburse pertama Anda!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
