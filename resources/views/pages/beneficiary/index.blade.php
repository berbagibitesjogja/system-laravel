@extends('layouts.main')
@section('container')
    <div x-data="{ search: '', showDeleteModal: false, deleteUrl: '' }">
        <div class="flex flex-wrap gap-4 items-center justify-between mb-8">
            <div class="flex flex-wrap gap-3 items-center">
                @if (in_array(auth()->user()->role, ['super', 'core']))
                    <x-btn-link href="{{ route('beneficiary.create') }}" variant="success">
                        + Tambah Penerima
                    </x-btn-link>
                @endif
            </div>

            <div class="relative w-full md:w-72">
                <input 
                    type="text" 
                    x-model="search" 
                    placeholder="Cari penerima..." 
                    class="w-full pl-10 pr-4 py-2 bg-white border border-navy-100 rounded-xl text-sm focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 transition-all outline-none"
                >
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <button x-show="search" @click="search = ''" class="absolute right-3 top-2.5 text-navy-300 hover:text-navy-500">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($beneficiaries as $item)
                <x-entity-card 
                    x-show="search === '' || $el.innerText.toLowerCase().includes(search.toLowerCase())"
                    :name="$item->name"
                    :id="$item->id"
                    :variant="$item->variant"
                    :badge="$item->heroes->sum('quantity') > 100 ? 'Platinum Hero' : ($item->heroes->sum('quantity') > 50 ? 'Gold' : 'Hero')"
                    :stats="[
                        ['label' => 'Heroes', 'value' => $item->heroes->sum('quantity'), 'unit' => 'Orang'],
                        ['label' => 'Impact', 'value' => round($item->foods() / 1000), 'unit' => 'Kg'],
                    ]"
                    :showRoute="route('beneficiary.show', $item->id)"
                    :editRoute="route('beneficiary.edit', $item->id)"
                    :deleteRoute="route('beneficiary.destroy', $item->id)"
                />
            @empty
                <div class="col-span-full flex flex-col items-center justify-center p-16 bg-white rounded-3xl border-2 border-dashed border-navy-100">
                    <div class="w-16 h-16 bg-navy-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy-900">Belum ada penerima</h3>
                    <p class="text-navy-400 mt-1">Daftar penerima manfaat akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $beneficiaries->links() }}
        </div>

        <x-modal 
            id="showDeleteModal" 
            title="Hapus Penerima" 
            message="Apakah Anda yakin ingin menghapus data penerima ini? Tindakan ini tidak dapat dibatalkan."
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
