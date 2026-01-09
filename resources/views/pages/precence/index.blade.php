@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('volunteer.home') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
        @if ($precences->where('status', 'active')->count() == 0)
            <x-btn-link href="{{ route('precence.create') }}" variant="primary">
                + Tambah Presensi
            </x-btn-link>
        @else
            <x-btn-link href="{{ route('precence.qr', 'download') }}" variant="info">
                📥 Download QR Code
            </x-btn-link>
            <x-btn-link href="{{ route('precence.qr', 'view') }}" variant="navy">
                👁 Lihat QR Code
            </x-btn-link>
        @endif
    </div>
    
    <div class="mb-6">
        {{ $precences->links() }}
    </div>
    
    <x-table>
        <x-slot:head>
            <x-th>Tanggal</x-th>
            <x-th class="hidden sm:table-cell">Judul</x-th>
            <x-th class="hidden sm:table-cell">Hadir</x-th>
            <x-th class="text-center">Aksi</x-th>
        </x-slot:head>
        <x-slot:body>
            @foreach ($precences as $item)
                <x-tr>
                    <x-td>
                        <div class="{{ $item->status == 'active' ? 'font-bold' : '' }}">
                            @if($item->status == 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-lime-400 text-navy-900 mb-1">
                                    🟢 Aktif
                                </span>
                            @endif
                            <p class="text-navy-900">{{ $item->created_at->isoFormat('dddd') }}</p>
                            <p class="text-sm text-navy-400">{{ $item->created_at->isoFormat('DD MMMM Y') }}</p>
                            <p class="sm:hidden text-sm text-navy-500 mt-1">{{ $item->title }}</p>
                        </div>
                    </x-td>
                    <x-td class="hidden sm:table-cell">
                        <p class="font-medium text-navy-900">{{ $item->title }}</p>
                    </x-td>
                    <x-td class="hidden sm:table-cell">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold bg-tosca-100 text-tosca-700">
                            {{ $item->attendance->count() }} orang
                        </span>
                    </x-td>
                    <x-td>
                        <div class="flex justify-center gap-2">
                            <x-btn-link href="{{ route('precence.edit', $item->id) }}" variant="yellow" class="!p-2">
                                <svg width="16" height="16" viewBox="0 0 18 18" fill="currentColor">
                                    <path d="M2.58333 15.4167H3.88958L12.85 6.45625L11.5438 5.15L2.58333 14.1104V15.4167ZM0.75 17.25V13.3542L12.85 1.27708C13.0333 1.10903 13.2358 0.979167 13.4573 0.8875C13.6788 0.795833 13.9118 0.75 14.1563 0.75C14.4007 0.75 14.6375 0.795833 14.8667 0.8875C15.0958 0.979167 15.2944 1.11667 15.4625 1.3L16.7229 2.58333C16.9063 2.75139 17.0399 2.95 17.124 3.17917C17.208 3.40833 17.25 3.6375 17.25 3.86667C17.25 4.11111 17.208 4.3441 17.124 4.56562C17.0399 4.78715 16.9063 4.98958 16.7229 5.17292L4.64583 17.25H0.75Z"/>
                                </svg>
                            </x-btn-link>
                            <x-btn-link href="{{ route('precence.show', $item->id) }}" variant="tosca" class="!p-2">
                                <svg width="18" height="14" viewBox="0 0 20 15" fill="currentColor">
                                    <path d="M9.99972 0C14.4931 0 18.2314 3.23333 19.0156 7.5C18.2322 11.7667 14.4931 15 9.99972 15C5.50639 15 1.76805 11.7667 0.983887 7.5C1.76722 3.23333 5.50639 0 9.99972 0ZM9.99972 13.3333C11.6993 13.333 13.3484 12.7557 14.6771 11.696C16.0058 10.6363 16.9355 9.15689 17.3139 7.5C16.9341 5.84442 16.0038 4.36667 14.6752 3.30835C13.3466 2.25004 11.6983 1.67377 9.99972 1.67377C8.30113 1.67377 6.65279 2.25004 5.3242 3.30835C3.9956 4.36667 3.06536 5.84442 2.68555 7.5C3.06397 9.15689 3.99361 10.6363 5.32234 11.696C6.65106 12.7557 8.30016 13.333 9.99972 13.3333V13.3333ZM9.99972 11.25C9.00516 11.25 8.05133 10.8549 7.34807 10.1516C6.64481 9.44839 6.24972 8.49456 6.24972 7.5C6.24972 6.50544 6.64481 5.55161 7.34807 4.84835C8.05133 4.14509 9.00516 3.75 9.99972 3.75C10.9943 3.75 11.9481 4.14509 12.6514 4.84835C13.3546 5.55161 13.7497 6.50544 13.7497 7.5C13.7497 8.49456 13.3546 9.44839 12.6514 10.1516C11.9481 10.8549 10.9943 11.25 9.99972 11.25Z"/>
                                </svg>
                            </x-btn-link>
                        </div>
                    </x-td>
                </x-tr>
            @endforeach
        </x-slot:body>
    </x-table>
@endsection
