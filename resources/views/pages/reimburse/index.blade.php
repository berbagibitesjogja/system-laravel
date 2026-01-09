@extends('layouts.main')

@section('container')
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-wrap gap-3 items-center mb-8">
            <x-btn-link href="{{ route('volunteer.home') }}" variant="ghost">
                ← Kembali
            </x-btn-link>
            <h1 class="text-2xl font-bold text-navy-900">Manajemen Reimburse</h1>
        </div>

        <div class="bg-white rounded-2xl border border-navy-100 shadow-md overflow-hidden">
            <x-table>
                <x-slot:head>
                    <x-th>User</x-th>
                    <x-th class="hidden sm:table-cell">Metode</x-th>
                    <x-th class="hidden sm:table-cell">Tujuan</x-th>
                    <x-th>Jumlah</x-th>
                    <x-th class="hidden sm:table-cell">File</x-th>
                    <x-th>Status</x-th>
                    <x-th class="text-center">Aksi</x-th>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($reimburse as $item)
                        <x-tr>
                            <x-td>
                                <div>
                                    <p class="font-semibold text-navy-900">{{ $item->user->name ?? 'Unknown' }}</p>
                                    <p class="sm:hidden text-sm text-navy-400">{{ $item->method }} • {{ $item->target }}</p>
                                </div>
                            </x-td>
                            <x-td class="hidden sm:table-cell">{{ $item->method }}</x-td>
                            <x-td class="hidden sm:table-cell">{{ $item->target }}</x-td>
                            <x-td>
                                <span class="font-bold text-tosca-600">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                            </x-td>
                            <x-td class="hidden sm:table-cell">
                                <a href="{{ asset('storage/' . $item->file) }}" target="_blank"
                                    class="text-tosca-500 hover:text-tosca-600 font-medium transition-colors">
                                    📄 Lihat
                                </a>
                            </x-td>
                            <x-td>
                                @if ($item->done == true)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-lime-400 text-navy-900">
                                        ✓ Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-300 text-navy-900">
                                        ⏳ Proses
                                    </span>
                                @endif
                            </x-td>
                            <x-td>
                                <div class="flex justify-center gap-2">
                                    @if ($item->done == false)
                                        <form action="{{ route('reimburse.update', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <x-btn type="submit" variant="success" class="!px-3 !py-1.5 text-xs">
                                                ✓ Sudah
                                            </x-btn>
                                        </form>

                                        <form action="{{ route('reimburse.destroy', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-btn type="submit" variant="danger" class="!px-3 !py-1.5 text-xs">
                                                ✕ Batal
                                            </x-btn>
                                        </form>
                                    @else
                                        <span class="text-navy-400 text-sm">—</span>
                                    @endif
                                </div>
                            </x-td>
                        </x-tr>
                    @empty
                        <x-tr>
                            <x-td colspan="7" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-4xl">📋</span>
                                    <p class="text-navy-400">Belum ada pengajuan reimburse</p>
                                </div>
                            </x-td>
                        </x-tr>
                    @endforelse
                </x-slot:body>
            </x-table>
        </div>
    </div>
@endsection
