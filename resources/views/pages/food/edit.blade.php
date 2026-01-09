@extends('layouts.main')
@section('container')
    <div class="flex flex-col lg:flex-row gap-8">
        <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-2xl border border-navy-100 shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-yellow-400 p-6 text-center text-white">
                    <h2 class="text-xl font-bold">Edit Surplus Food</h2>
                    <p class="text-sm text-white/80 mt-1">{{ $food->name }}</p>
                </div>
                
                <form class="p-6" action="{{ route('food.update', $food->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-semibold text-navy-700">Donasi</label>
                        <input type="text"
                            class="w-full px-4 py-3 text-sm text-navy-400 bg-navy-50 border border-navy-200 rounded-xl cursor-not-allowed"
                            value="{{ $donation->sponsor->name }} ({{ $donation->take }})" disabled readonly>
                    </div>
                    
                    <x-input name="name" label="Nama Makanan" value="{{ $food->name }}" required />
                    <x-input name="quantity" label="Jumlah Makanan (Opsional)" value="{{ $food->quantity }}" />
                    
                    <div class="grid grid-cols-2 gap-4">
                        <x-input name="weight" label="Berat / Volume" type="number" value="{{ $food->weight }}" required />
                        <x-select name="unit" label="Unit">
                            <option value="gr" @if ($food->unit == 'gr') selected @endif>gr</option>
                            <option value="ml" @if ($food->unit == 'ml') selected @endif>ml</option>
                        </x-select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-navy-100 hover:border-tosca-300 cursor-pointer transition-colors">
                            <input type="checkbox" name="expired" id="expired" {{ $food->expired ? 'checked' : '' }} class="rounded text-red-500 focus:ring-red-300">
                            <span class="text-sm text-navy-700">🚫 Basi / Tidak Layak</span>
                        </label>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-semibold text-navy-700">Keterangan</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-4 py-3 text-sm text-navy-900 bg-white border border-navy-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 transition-all duration-300"
                            placeholder="Keterangan makanan...">{{ $food->notes }}</textarea>
                    </div>
                    
                    <x-btn type="submit" variant="primary" class="w-full">
                        💾 Simpan Perubahan
                    </x-btn>
                </form>
            </div>
        </div>

        <div class="w-full lg:w-2/3">
            <h2 class="text-xl font-bold text-navy-900 mb-4">Daftar Surplus Food</h2>
            
            <x-table>
                <x-slot:head>
                    <x-th>Nama</x-th>
                    <x-th class="hidden sm:table-cell">Jumlah</x-th>
                    <x-th class="hidden sm:table-cell">Berat</x-th>
                    <x-th class="hidden sm:table-cell">Donatur</x-th>
                    <x-th class="text-center">Aksi</x-th>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($foods as $item)
                        <x-tr>
                            <x-td>
                                <div>
                                    <p class="font-semibold text-navy-900 {{ $item->id == $food->id ? 'text-tosca-600' : '' }}">
                                        {{ $item->name }}
                                        @if ($item->id == $food->id)
                                            <span class="text-xs">(editing)</span>
                                        @endif
                                    </p>
                                    <a href="{{ route('donation.show', $item->donation->id) }}" class="sm:hidden text-sm text-tosca-500 hover:text-tosca-600 transition-colors">
                                        {{ $item->donation->sponsor->name }}
                                        <span class="block text-xs italic text-navy-400">{{ \Carbon\Carbon::parse($item->donation->take)->format('d M Y') }}</span>
                                    </a>
                                </div>
                            </x-td>
                            <x-td class="hidden sm:table-cell">
                                <span class="text-navy-600">{{ $item->quantity }}</span>
                            </x-td>
                            <x-td class="hidden sm:table-cell">
                                <span class="font-semibold text-lime-600">{{ $item->weight }}</span>
                                <span class="text-navy-400">{{ $item->unit }}</span>
                            </x-td>
                            <x-td class="hidden sm:table-cell">
                                <a href="{{ route('donation.show', $item->donation->id) }}" class="hover:text-tosca-600 transition-colors">
                                    <p class="font-medium">{{ $item->donation->sponsor->name }}</p>
                                    <p class="text-xs italic text-navy-400">{{ \Carbon\Carbon::parse($item->donation->take)->format('d M Y') }}</p>
                                </a>
                            </x-td>
                            <x-td>
                                <div class="flex justify-center gap-2">
                                    <x-btn-link href="{{ route('food.edit', $item->id) }}" variant="yellow" class="!p-2">
                                        <svg width="16" height="16" viewBox="0 0 18 18" fill="currentColor">
                                            <path d="M2.58333 15.4167H3.88958L12.85 6.45625L11.5438 5.15L2.58333 14.1104V15.4167ZM0.75 17.25V13.3542L12.85 1.27708C13.0333 1.10903 13.2358 0.979167 13.4573 0.8875C13.6788 0.795833 13.9118 0.75 14.1563 0.75C14.4007 0.75 14.6375 0.795833 14.8667 0.8875C15.0958 0.979167 15.2944 1.11667 15.4625 1.3L16.7229 2.58333C16.9063 2.75139 17.0399 2.95 17.124 3.17917C17.208 3.40833 17.25 3.6375 17.25 3.86667C17.25 4.11111 17.208 4.3441 17.124 4.56562C17.0399 4.78715 16.9063 4.98958 16.7229 5.17292L4.64583 17.25H0.75Z"/>
                                        </svg>
                                    </x-btn-link>
                                    <form action="{{ route('food.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-btn type="submit" variant="danger" class="!p-2" onclick="return confirm('Yakin ingin menghapus?')">
                                            <svg width="16" height="16" viewBox="0 0 18 17" fill="currentColor">
                                                <path d="M13.1665 3.50008H17.3332V5.16675H15.6665V16.0001C15.6665 16.2211 15.5787 16.4331 15.4224 16.5893C15.2661 16.7456 15.0542 16.8334 14.8332 16.8334H3.1665C2.94549 16.8334 2.73353 16.7456 2.57725 16.5893C2.42097 16.4331 2.33317 16.2211 2.33317 16.0001V5.16675H0.666504V3.50008H4.83317V1.00008C4.83317 0.779068 4.92097 0.567106 5.07725 0.410826C5.23353 0.254545 5.44549 0.166748 5.6665 0.166748H12.3332C12.5542 0.166748 12.7661 0.254545 12.9224 0.410826C13.0787 0.567106 13.1665 0.779068 13.1665 1.00008V3.50008ZM13.9998 5.16675H3.99984V15.1667H13.9998V5.16675ZM6.49984 1.83341V3.50008H11.4998V1.83341H6.49984Z"/>
                                            </svg>
                                        </x-btn>
                                    </form>
                                </div>
                            </x-td>
                        </x-tr>
                    @endforeach
                </x-slot:body>
            </x-table>
            
            <div class="mt-6">
                {{ $foods->links() }}
            </div>
        </div>
    </div>
@endsection
