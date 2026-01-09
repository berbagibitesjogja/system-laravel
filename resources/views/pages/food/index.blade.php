@extends('layouts.main')
@section('container')
    <div x-data="{ 
        slideOpen: false, 
        showDeleteModal: false, 
        deleteUrl: '',
        search: '' 
    }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-navy-900">Surplus Food</h1>
                <p class="text-navy-400 mt-1">Daftar makanan yang berhasil diselamatkan</p>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <div class="relative w-full md:w-64">
                    <input 
                        type="text" 
                        x-model="search" 
                        x-init="$el.focus()"
                        placeholder="Cari makanan..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-navy-100 rounded-xl text-sm focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 transition-all outline-none"
                    >
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                @auth
                    <x-btn @click="slideOpen = true" variant="primary" class="hidden md:flex">
                        + Tambah Makanan
                    </x-btn>
                @endauth
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <div class="p-6 bg-gradient-to-br from-lime-50 to-white rounded-2xl border border-lime-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-4 text-lime-600">
                        <div class="p-2 bg-lime-100 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold uppercase tracking-wider">Total Impact</p>
                    </div>
                    <p class="text-4xl font-black text-navy-900">{{ ceil($total / 100) / 10 }} <span class="text-xl font-bold text-lime-500 italic">kg</span></p>
                    <div class="mt-4 pt-4 border-t border-lime-100">
                        <p class="text-xs text-navy-400 leading-relaxed">Berat makanan layak konsumsi yang berhasil dialokasikan kembali.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                @if($foods->count() > 0)
                    <x-table>
                        <x-slot:head>
                            <x-th>Nama</x-th>
                            <x-th class="hidden sm:table-cell">Jumlah</x-th>
                            <x-th class="hidden sm:table-cell">Berat</x-th>
                            <x-th class="hidden sm:table-cell">Donatur</x-th>
                            @if (in_array(auth()->user()->role, ['super', 'core']))
                                <x-th class="text-center">Aksi</x-th>
                            @endif
                        </x-slot:head>
                        <x-slot:body>
                            @foreach ($foods as $item)
                                <x-tr x-show="search === '' || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                                    <x-td>
                                        <div>
                                            <p class="font-semibold text-navy-900">{{ $item->name }}</p>
                                            @if ($item->notes)
                                                <p class="hidden sm:block text-xs text-navy-400 italic mt-0.5">*{{ $item->notes }}</p>
                                            @endif
                                            <a href="{{ route('donation.show', $item->donation->id) }}" class="sm:hidden text-xs text-tosca-500 hover:text-tosca-600 transition-colors mt-1 block">
                                                {{ $item->donation->sponsor->name }}
                                            </a>
                                        </div>
                                    </x-td>
                                    <x-td class="hidden sm:table-cell">
                                        <span class="text-navy-600">{{ $item->quantity ?? '-' }}</span>
                                    </x-td>
                                    <x-td class="hidden sm:table-cell">
                                        <div class="flex items-baseline gap-1">
                                            <span class="font-bold text-lime-600 text-lg">{{ $item->weight }}</span>
                                            <span class="text-xs font-semibold text-navy-400 uppercase">{{ $item->unit }}</span>
                                        </div>
                                    </x-td>
                                    <x-td class="hidden sm:table-cell">
                                        <a href="{{ route('donation.show', $item->donation->id) }}" class="group inline-block">
                                            <p class="font-medium text-navy-900 group-hover:text-tosca-600 transition-colors">{{ $item->donation->sponsor->name }}</p>
                                            <p class="text-[10px] text-navy-400 font-mono">{{ \Carbon\Carbon::parse($item->donation->take)->format('d/m/Y') }}</p>
                                        </a>
                                    </x-td>
                                    @if (in_array(auth()->user()->role, ['super', 'core']))
                                        <x-td>
                                            <div class="flex justify-center gap-2">
                                                <x-btn-link href="{{ route('food.edit', $item->id) }}" variant="yellow" class="!p-2 shadow-sm shadow-yellow-100">
                                                    <svg width="16" height="16" viewBox="0 0 18 18" fill="currentColor">
                                                        <path d="M2.58333 15.4167H3.88958L12.85 6.45625L11.5438 5.15L2.58333 14.1104V15.4167ZM0.75 17.25V13.3542L12.85 1.27708C13.0333 1.10903 13.2358 0.979167 13.4573 0.8875C13.6788 0.795833 13.9118 0.75 14.1563 0.75C14.4007 0.75 14.6375 0.795833 14.8667 0.8875C15.0958 0.979167 15.2944 1.11667 15.4625 1.3L16.7229 2.58333C16.9063 2.75139 17.0399 2.95 17.124 3.17917C17.208 3.40833 17.25 3.6375 17.25 3.86667C17.25 4.11111 17.208 4.3441 17.124 4.56562C17.0399 4.78715 16.9063 4.98958 16.7229 5.17292L4.64583 17.25H0.75Z"/>
                                                    </svg>
                                                </x-btn-link>
                                                <x-btn type="button" variant="danger" class="!p-2 shadow-sm shadow-red-100" @click="deleteUrl = '{{ route('food.destroy', $item->id) }}'; showDeleteModal = true">
                                                    <svg width="16" height="16" viewBox="0 0 18 17" fill="currentColor">
                                                        <path d="M13.1665 3.50008H17.3332V5.16675H15.6665V16.0001C15.6665 16.2211 15.5787 16.4331 15.4224 16.5893C15.2661 16.7456 15.0542 16.8334 14.8332 16.8334H3.1665C2.94549 16.8334 2.73353 16.7456 2.57725 16.5893C2.42097 16.4331 2.33317 16.2211 2.33317 16.0001V5.16675H0.666504V3.50008H4.83317V1.00008C4.83317 0.779068 4.92097 0.567106 5.07725 0.410826C5.23353 0.254545 5.44549 0.166748 5.6665 0.166748H12.3332C12.5542 0.166748 12.7661 0.254545 12.9224 0.410826C13.0787 0.567106 13.1665 1.00008V3.50008ZM13.9998 5.16675H3.99984V15.1667H13.9998V5.16675ZM6.49984 1.83341V3.50008H11.4998V1.83341H6.49984Z"/>
                                                    </svg>
                                                </x-btn>
                                            </div>
                                        </x-td>
                                    @endif
                                </x-tr>
                            @endforeach
                        </x-slot:body>
                    </x-table>
                @else
                    <div class="flex flex-col items-center justify-center p-16 bg-white rounded-2xl border border-dashed border-navy-200">
                        <div class="w-24 h-24 bg-navy-50 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-12 h-12 text-navy-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-navy-900">Belum ada makanan</h3>
                        <p class="text-navy-400 text-center mt-2 max-w-xs">Data surplus food yang diselamatkan akan tampil di sini.</p>
                        @auth
                            <x-btn @click="slideOpen = true" variant="primary" class="mt-8">
                                Mulai Mencatat
                            </x-btn>
                        @endauth
                    </div>
                @endif
                
                <div class="mt-8">
                    {{ $foods->links() }}
                </div>
            </div>
        </div>

        {{-- Slide-over Panel --}}
        @auth
            <x-slideover id="slideOpen" title="Tambah Surplus Food">
                <form action="{{ route('food.store') }}" method="POST">
                    @csrf
                    
                    <x-select name="donation_id" label="Pilih Donasi Aktif">
                        <option value="">Donasi Aktif</option>
                        @foreach ($donations as $item)
                            <option value="{{ $item->id }}">{{ $item->sponsor->name }} ({{ $item->take }})</option>
                        @endforeach
                    </x-select>
                    
                    <x-input name="name" label="Nama Makanan" required />
                    <x-input name="quantity" label="Jumlah Makanan (Opsional)" placeholder="Contoh: 10 Kotak" />
                    
                    <div class="grid grid-cols-2 gap-4">
                        <x-input name="weight" label="Berat / Volume" type="number" required />
                        <x-select name="unit" label="Unit">
                            <option value="gr">Gram (gr)</option>
                            <option value="ml">Mililiter (ml)</option>
                        </x-select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-navy-100 hover:border-red-300 cursor-pointer transition-all group">
                            <input type="checkbox" name="expired" id="expired" class="rounded text-red-500 focus:ring-red-300">
                            <span class="text-sm font-medium text-navy-700 group-hover:text-red-600 transition-colors">🚫 Makanan Tidak Layak</span>
                        </label>
                    </div>
                    
                    <div class="mb-8">
                        <label class="block mb-2 text-sm font-bold text-navy-700 uppercase tracking-wider">Catatan Tambahan</label>
                        <textarea name="notes" rows="4"
                            class="w-full px-4 py-3 text-sm text-navy-900 bg-white border border-navy-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-tosca-500/10 focus:border-tosca-500 transition-all duration-300"
                            placeholder="Detail kondisi makanan..."></textarea>
                    </div>
                    
                    <x-btn type="submit" variant="primary" class="w-full h-12">
                        + Simpan Data Makanan
                    </x-btn>
                </form>
            </x-slideover>

            {{-- Mobile FAB --}}
            <x-fab onClick="slideOpen = true" label="Tambah Makanan" />
        @endauth

        <x-modal 
            id="showDeleteModal" 
            title="Hapus Makanan" 
            message="Apakah Anda yakin ingin menghapus data makanan ini? Tindakan ini tidak dapat dibatalkan."
            confirmText="Ya, Hapus"
            type="danger"
        >
            $refs.deleteForm.action = deleteUrl; $refs.deleteForm.submit()
        </x-modal>

        <form x-ref="deleteForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
