@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-8">
        <x-btn-link href="{{ route('report.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
        <x-btn-link href="{{ route('report.clean') }}" variant="danger">
            🗑️ Hapus Semua File Laporan
        </x-btn-link>
    </div>
    
    <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6">
        <h2 class="text-xl font-bold text-navy-900 mb-4">File Laporan Tersedia</h2>
        
        @if(count($reportFiles) > 0)
            <div class="flex flex-row flex-wrap gap-3">
                @foreach ($reportFiles as $item)
                    <a download="{{ $item }}" href="/storage/reports/{{ $item }}"
                        class="inline-flex items-center gap-2 px-4 py-3 bg-gradient-to-br from-tosca-500 to-tosca-600 text-white rounded-xl font-medium hover:from-tosca-600 hover:to-tosca-700 transition-all duration-300 shadow-md hover:shadow-lg">
                        📄 {{ $item }}
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <span class="text-5xl mb-3 block">📋</span>
                <p class="text-navy-400">Belum ada file laporan</p>
                <p class="text-sm text-navy-300">Generate laporan terlebih dahulu</p>
            </div>
        @endif
    </div>
@endsection
