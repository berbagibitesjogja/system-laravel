@extends('layouts.main')
@section('container')
    <div x-data="{ 
        scriptUrl: '{{ 'https://script.google.com/macros/s/AKfycbwQ1vWmRXpzlGAxmyv0VcukMHsa-kcM51yHpyYAOi5oc4qgU-ZEPS5WSQWNJYdAZdsC/exec' }}',
        activeFolder: null,
        activeTitle: '',
        images: [],
        loading: false,
        showModal: false,

        async openFolder(id, title) {
            this.activeFolder = id;
            this.activeTitle = title;
            this.showModal = true;
            this.loading = true;
            this.images = [];
            try {
                const response = await fetch(`${this.scriptUrl}?id=${id}`);
                const data = await response.json();
                if (data.error) throw new data.error;
                this.images = data;
                console.log(this.images);
            } catch (e) {
                console.error('Gallery Error:', e);
                this.images = [];
            } finally {
                this.loading = false;
            }
        }
    }">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-navy-900 tracking-tight">Gallery of <span class="text-tosca-500 italic">Impact</span></h1>
            <p class="text-navy-400 mt-2 font-medium">Kumpulan dokumentasi aksi nyata penyelamatan makanan 📸</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($donations as $donation)
                @php 
                    $folderId = App\Http\Controllers\DonationController::extractFolderId($donation->media);
                @endphp
                <div 
                    x-data="{ 
                        thumb: '', 
                        count: 0,
                        async init() {
                            try {
                                const res = await fetch(`${scriptUrl}?id={{ $folderId }}`);
                                const data = await res.json();
                                this.count = data.length;
                                if (data.length > 0) {
                                    this.thumb = `https://lh3.googleusercontent.com/d/${data[0].id}`;
                                }
                            } catch (e) {}
                        }
                    }"
                    class="group bg-white rounded-[2.5rem] border border-navy-100 overflow-hidden shadow-sm hover:shadow-xl transition-all hover:-translate-y-1"
                >
                    <div class="relative h-48 bg-navy-900 overflow-hidden">
                        {{-- Thumbnail Background --}}
                        <div 
                            class="absolute inset-0 bg-cover bg-center transition-all duration-1000 group-hover:scale-110"
                            :style="thumb ? `background-image: url('${thumb}')` : ''"
                            x-show="thumb"
                            x-transition:enter="transition-opacity ease-out duration-1000"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                        ></div>

                        {{-- Photo Count Badge --}}
                        <div x-show="count > 0" class="absolute top-4 right-4 z-10">
                            <div class="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 rounded-full flex items-center gap-1.5 shadow-lg">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-[10px] font-black text-white" x-text="count + ' MEDIA'"></span>
                            </div>
                        </div>

                        {{-- Default Gradient / Overlay --}}
                        <div class="absolute inset-0 transition-opacity duration-500" :class="thumb ? 'bg-navy-900/40 group-hover:bg-navy-900/20' : 'bg-gradient-to-br from-tosca-400 to-navy-900 opacity-20'">
                            @if(!$donation->media) {{-- This part won't hit due to whereNotNull in controller, but for safety --}}
                                <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/20 rounded-full blur-3xl"></div>
                            @endif
                        </div>
                        
                        <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                            <div x-show="!thumb" class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center mb-4 text-white">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-white font-black text-sm uppercase tracking-widest drop-shadow-lg" :class="thumb ? 'opacity-100' : 'opacity-60'">Aksi Rescue</p>
                            <h3 class="text-white font-bold text-lg leading-tight mt-1 drop-shadow-lg">{{ \Carbon\Carbon::parse($donation->take)->isoFormat('D MMMM Y') }}</h3>
                        </div>

                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all transform translate-y-4 group-hover:translate-y-0">
                            <button 
                                @click="openFolder('{{ $folderId }}', '{{ $donation->sponsor->name }} - {{ \Carbon\Carbon::parse($donation->take)->format('d M/Y') }}')"
                                class="px-6 py-2 bg-white/20 backdrop-blur-md hover:bg-tosca-500 text-white border border-white/30 rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg transition-all active:scale-95"
                            >
                                Lihat Semua Foto
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-navy-900 font-bold line-clamp-1">{{ $donation->sponsor->name }}</p>
                                <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest mt-1">{{ $donation->foods->count() }} Jenis Makanan • {{ round($donation->foods->sum('weight') / 1000) }} Kg</p>
                            </div>
                            @if($donation->sponsor->logo)
                                <img src="{{ $donation->sponsor->logo }}" class="w-8 h-8 rounded-lg object-cover shadow-sm">
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $donations->links() }}
        </div>

        {{-- Photo Modal --}}
        <div 
            x-show="showModal" 
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-navy-900/90 backdrop-blur-sm" @click="showModal = false"></div>
            
            <div class="relative bg-white w-full max-w-5xl max-h-[90vh] rounded-[3rem] shadow-2xl overflow-hidden flex flex-col">
                {{-- Modal Header --}}
                <div class="px-8 py-6 border-b border-navy-50 flex items-center justify-between shrink-0">
                    <div>
                        <h2 class="text-xl font-black text-navy-900 italic" x-text="activeTitle"></h2>
                        <p class="text-xs text-navy-400 font-bold uppercase tracking-widest mt-1">Dokumentasi Google Drive</p>
                    </div>
                    <button @click="showModal = false" class="p-2 hover:bg-navy-50 rounded-full transition-colors">
                        <svg class="w-6 h-6 text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Content --}}
                <div class="flex-1 overflow-y-auto p-8">
                    {{-- Loading State --}}
                    <div x-show="loading" class="flex flex-col items-center justify-center py-20">
                        <div class="w-12 h-12 border-4 border-tosca-100 border-t-tosca-500 rounded-full animate-spin"></div>
                        <p class="text-navy-400 font-bold mt-4 animate-pulse uppercase tracking-widest text-xs">Menghubungkan ke Drive...</p>
                    </div>

                    {{-- Empty State --}}
                    <div x-show="!loading && images.length === 0" class="text-center py-20 grayscale opacity-50">
                        <svg class="w-20 h-20 mx-auto text-navy-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-4 text-navy-400 font-bold italic text-sm">Tidak ada foto yang ditemukan di folder ini.</p>
                    </div>

                    {{-- Grid Images --}}
                    <div x-show="!loading && images.length > 0" class="columns-1 sm:columns-2 lg:columns-3 gap-4 space-y-4">
                        <template x-for="img in images" :key="img.id">
                            <div class="relative group rounded-2xl overflow-hidden break-inside-avoid shadow-sm hover:shadow-xl transition-all">
                                <img :src="'https://lh3.googleusercontent.com/d/' + img.id" 
                                     class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                                    <p class="text-white text-[10px] font-black uppercase tracking-tighter truncate" x-text="img.name"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-8 py-6 bg-navy-50/50 border-t border-navy-50 flex items-center justify-between shrink-0">
                    <p class="text-[10px] font-black text-navy-400 uppercase tracking-widest">Powered by Google Apps Script</p>
                    <a :href="'https://drive.google.com/drive/folders/' + activeFolder" target="_blank" class="text-xs font-black text-tosca-600 hover:text-tosca-700 flex items-center gap-2">
                        Buka di Drive
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
    </style>
@endsection
