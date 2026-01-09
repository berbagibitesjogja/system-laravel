@extends('layouts.main')
@section('container')
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-navy-900">Daftar Heroes {{ $donation->sponsor->name }}</h1>
        <p class="text-navy-400 italic">{{ \Carbon\Carbon::parse($donation->take)->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
    
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('donation.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
        
        @if ($donation->partner_id)
            <div class="px-4 py-2 bg-navy-500 text-white rounded-xl shadow-md font-semibold">
                Heroes: {{ $donation->partner->quota - $donation->partner->remain }} / {{ $donation->partner->quota }}
            </div>
        @else
            <div class="px-4 py-2 bg-navy-500 text-white rounded-xl shadow-md font-semibold">
                Heroes: {{ $donation->quota - $donation->remain }} / {{ $donation->quota }}
            </div>
        @endif
        
        @if ($donation->media)
            <x-btn-link href="{{ $donation->media }}" variant="info">
                📸 Dokumentasi
            </x-btn-link>
        @endif

        <x-btn @click="$dispatch('open-share')" variant="tosca" class="flex">
            🤳 Bagikan Impact
        </x-btn>

        <x-share-card 
            title="Impact Report"
            subtitle="Aksi Food Rescue"
            :name="$donation->sponsor->name"
            :avatar="$donation->sponsor->logo ?? null"
            :stats="[
                ['label' => 'Heroes', 'value' => $donation->quota - $donation->remain, 'unit' => 'Orang'],
                ['label' => 'Surplus Food', 'value' => round($foods->sum('weight') / 1000), 'unit' => 'Kg'],
                ['label' => 'Jenis Makanan', 'value' => $foods->count()],
                ['label' => 'Tanggal', 'value' => \Carbon\Carbon::parse($donation->take)->format('d/m/Y')],
            ]"
        />
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6 mb-8">
        <form method="POST" action="{{ route('donation.update', $donation->id) }}">
            @csrf
            @method('PUT')
            
            @if ($donation->partners->count() > 0)
                <div class="mb-4">
                    <p class="text-sm font-semibold text-navy-700 mb-2">Partner:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($donation->partners as $item)
                            <x-btn-link href="{{ route('donation.show', $item->id) }}" variant="navy">
                                {{ $item->sponsor->name }}
                            </x-btn-link>
                        @endforeach
                    </div>
                </div>
            @endif
            
            @if ($donation->quota == 0)
                <x-select name="partner_id" label="Partner">
                    <option value="">Donasi Partner</option>
                    @foreach ($donations as $item)
                        <option {{ $item->id == $donation->partner_id ? 'selected' : '' }} value="{{ $item->id }}">
                            {{ $item->sponsor->name }} - {{ \Carbon\Carbon::parse($item->take)->isoFormat('dddd, DD MMMM Y') }}
                        </option>
                    @endforeach
                </x-select>
                @if ($donation->partner_id)
                    <x-btn-link href="{{ route('donation.show', $donation->partner_id) }}" variant="info" class="mb-4">
                        Lihat Partner
                    </x-btn-link>
                @endif
            @endif
            
            <div class="mb-4">
                <label class="block mb-2 text-sm font-semibold text-navy-700">Catatan</label>
                <textarea name="notes" rows="3" placeholder="Catatan...."
                    class="w-full px-4 py-3 text-sm text-navy-900 bg-white border border-navy-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 transition-all duration-300">{{ $donation->notes }}</textarea>
            </div>
            
            <x-btn type="submit" variant="primary">
                💾 Simpan Catatan
            </x-btn>
        </form>
    </div>

    <div x-data="{ showConfirmModal: false, confirmAction: '', confirmTitle: '', confirmMessage: '', confirmType: 'danger' }">
        <x-table>
            <x-slot:head>
                <x-th class="hidden sm:table-cell">No</x-th>
                <x-th>Nama</x-th>
                <x-th class="hidden sm:table-cell">Asal</x-th>
                <x-th class="hidden sm:table-cell">Kode</x-th>
                @if ($heroes->where('status', 'belum')->count() > 0)
                    <x-th class="text-center">Aksi</x-th>
                @endif
            </x-slot:head>
            <x-slot:body>
                @forelse ($heroes as $number => $item)
                    <x-tr>
                        <x-td class="hidden sm:table-cell text-navy-400">{{ $number + 1 }}</x-td>
                        <x-td>
                            <div>
                                <p class="font-semibold text-navy-900">{{ $item->name }}</p>
                                <p class="sm:hidden text-sm text-navy-400">
                                    {{ $item->faculty->name }}
                                    @if ($item->quantity > 1)
                                        <span class="text-tosca-500">({{ $item->quantity }} Orang)</span>
                                    @endif
                                </p>
                                <p class="sm:hidden text-xs text-navy-300">{{ $item->code }}</p>
                            </div>
                        </x-td>
                        <x-td class="hidden sm:table-cell">
                            <p class="font-medium">{{ $item->faculty->name }}</p>
                            <p class="text-sm text-navy-400">({{ $item->faculty->university->name }})</p>
                            @if ($item->quantity > 1)
                                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-lime-400 text-navy-900">
                                    {{ $item->quantity }} Orang
                                </span>
                            @endif
                        </x-td>
                        <x-td class="hidden sm:table-cell">
                            <code class="px-2 py-1 bg-navy-50 rounded text-navy-600 text-sm">{{ $item->code }}</code>
                        </x-td>
                        @if ($heroes->where('status', 'belum')->count() > 0)
                            <x-td>
                                <div class="flex justify-center gap-2">
                                    @if ($item->status == 'belum')
                                        <form action="{{ route('hero.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <x-btn type="submit" variant="success" class="!p-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                                                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                                                </svg>
                                            </x-btn>
                                        </form>
                                        <x-btn type="button" variant="danger" class="!p-2" 
                                            @click="confirmAction = 'submitHeroDelete{{ $item->id }}'; confirmTitle = 'Hapus Hero'; confirmMessage = 'Yakin ingin menghapus hero {{ $item->name }}?'; confirmType = 'danger'; showConfirmModal = true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M9.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0M6.44 3.752A.75.75 0 0 1 7 3.5h1.445c.742 0 1.32.643 1.243 1.38l-.43 4.083a1.8 1.8 0 0 1-.088.395l-.318.906.213.242a.8.8 0 0 1 .114.175l2 4.25a.75.75 0 1 1-1.357.638l-1.956-4.154-1.68-1.921A.75.75 0 0 1 6 8.96l.138-2.613-.435.489-.464 2.786a.75.75 0 1 1-1.48-.246l.5-3a.75.75 0 0 1 .18-.375l2-2.25Z" />
                                                <path d="M6.25 11.745v-1.418l1.204 1.375.261.524a.8.8 0 0 1-.12.231l-2.5 3.25a.75.75 0 1 1-1.19-.914zm4.22-4.215-.494-.494.205-1.843.006-.067 1.124 1.124h1.44a.75.75 0 0 1 0 1.5H11a.75.75 0 0 1-.531-.22Z" />
                                            </svg>
                                        </x-btn>
                                        <form id="submitHeroDelete{{ $item->id }}" action="{{ route('hero.destroy', $item->id) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </x-td>
                        @endif
                    </x-tr>
                @empty
                    <x-tr>
                        <x-td colspan="5" class="py-12 text-center text-navy-400">
                            Belum ada Heroes yang mendaftar donasi ini.
                        </x-td>
                    </x-tr>
                @endforelse
            </x-slot:body>
        </x-table>

        <div class="mt-12">
            <div class="flex flex-wrap gap-3 items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-navy-900">Daftar Surplus Food</h2>
                <x-btn-link href="{{ route('food.index') }}" variant="success">
                    + Tambah
                </x-btn-link>
            </div>
            
            @if($foods->count() > 0)
                <x-table>
                    <x-slot:head>
                        <x-th>Nama</x-th>
                        <x-th class="hidden sm:table-cell">Jumlah</x-th>
                        <x-th>Berat</x-th>
                        <x-th class="hidden sm:table-cell">Keterangan</x-th>
                        <x-th class="text-center">Aksi</x-th>
                    </x-slot:head>
                    <x-slot:body>
                        @foreach ($foods as $item)
                            <x-tr>
                                <x-td>
                                    <p class="font-semibold text-navy-900">{{ $item->name }}</p>
                                </x-td>
                                <x-td class="hidden sm:table-cell">
                                    <span class="text-tosca-600 font-medium">{{ $item->quantity }}</span>
                                </x-td>
                                <x-td>
                                    <span class="font-semibold text-lime-600">{{ $item->weight }}</span>
                                    <span class="text-navy-400">{{ $item->unit }}</span>
                                </x-td>
                                <x-td class="hidden sm:table-cell">
                                    <span class="text-navy-400">{{ $item->notes }}</span>
                                </x-td>
                                <x-td>
                                    <div class="flex justify-center gap-2">
                                        <x-btn-link href="{{ route('food.edit', $item->id) }}" variant="yellow" class="!p-2">
                                            <svg width="16" height="16" viewBox="0 0 18 18" fill="currentColor">
                                                <path d="M2.58333 15.4167H3.88958L12.85 6.45625L11.5438 5.15L2.58333 14.1104V15.4167ZM0.75 17.25V13.3542L12.85 1.27708C13.0333 1.10903 13.2358 0.979167 13.4573 0.8875C13.6788 0.795833 13.9118 0.75 14.1563 0.75C14.4007 0.75 14.6375 0.795833 14.8667 0.8875C15.0958 0.979167 15.2944 1.11667 15.4625 1.3L16.7229 2.58333C16.9063 2.75139 17.0399 2.95 17.124 3.17917C17.208 3.40833 17.25 3.6375 17.25 3.86667C17.25 4.11111 17.208 4.3441 17.124 4.56562C17.0399 4.78715 16.9063 4.98958 16.7229 5.17292L4.64583 17.25H0.75Z"/>
                                            </svg>
                                        </x-btn-link>
                                        <x-btn type="button" variant="danger" class="!p-2" 
                                            @click="confirmAction = 'submitFoodDelete{{ $item->id }}'; confirmTitle = 'Hapus Makanan'; confirmMessage = 'Yakin ingin menghapus {{ $item->name }}?'; confirmType = 'danger'; showConfirmModal = true">
                                            <svg width="16" height="16" viewBox="0 0 18 17" fill="currentColor">
                                                <path d="M13.1665 3.50008H17.3332V5.16675H15.6665V16.0001C15.6665 16.2211 15.5787 16.4331 15.4224 16.5893C15.2661 16.7456 15.0542 16.8334 14.8332 16.8334H3.1665C2.94549 16.8334 2.73353 16.7456 2.57725 16.5893C2.42097 16.4331 2.33317 16.2211 2.33317 16.0001V5.16675H0.666504V3.50008H4.83317V1.00008C4.83317 0.779068 4.92097 0.567106 5.07725 0.410826C5.23353 0.254545 5.44549 0.166748 5.6665 0.166748H12.3332C12.5542 0.166748 12.7661 0.254545 12.9224 0.410826C13.0787 0.567106 13.1665 0.779068 13.1665 1.00008V3.50008ZM13.9998 5.16675H3.99984V15.1667H13.9998V5.16675ZM6.49984 1.83341V3.50008H11.4998V1.83341H6.49984Z"/>
                                            </svg>
                                        </x-btn>
                                        <form id="submitFoodDelete{{ $item->id }}" action="{{ route('food.destroy', $item->id) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-slot:body>
                </x-table>
            @else
                <div class="px-6 py-10 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <p class="text-navy-400">Belum ada Surplus Food untuk donasi ini.</p>
                </div>
            @endif
        </div>

        <x-modal 
            id="showConfirmModal" 
            ::title="confirmTitle" 
            ::message="confirmMessage"
            confirmText="Selesaikan"
            ::type="confirmType"
        >
            document.getElementById(confirmAction).submit()
        </x-modal>
    </div>
@endsection
