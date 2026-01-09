@php
    use Carbon\Carbon;
@endphp
@extends('layouts.form')
@section('container')

    <div class="max-w-lg mx-auto pb-12">
        {{-- Header Logo or Branding Area --}}
        <div class="text-center pt-8 pb-4">
            {{-- Assuming there's a logo or just use text --}}
            <h1 class="text-3xl font-bold text-navy-900">BBJ <span class="text-tosca-500">Action</span></h1>
        </div>

        @if ($donations->count() > 0)
            @if ($donations->contains('id', session('donation')))
                @php
                    $donation = $donations->find(session('donation'));
                @endphp

                {{-- Success State --}}
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in-up">
                    <div class="bg-gradient-to-r from-navy-500 to-tosca-600 p-8 text-center text-white relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                        <h2 class="text-xl font-bold relative z-10">BBJ X {{ $donation->sponsor->name }}</h2>
                        <div class="inline-block bg-white/20 px-4 py-1.5 rounded-full text-xs font-semibold mt-3 backdrop-blur-sm relative z-10">
                            {{ Carbon::parse($donation->take)->isoFormat('dddd, DD MMMM Y') }}
                        </div>
                    </div>

                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-lime-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <span class="text-4xl">🎉</span>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-navy-900 mb-2">Terima Kasih Heroes!</h3>
                        <p class="text-navy-500 mb-6">Kamu telah terdaftar untuk aksi hari ini.</p>
                        
                        @if ($donation->message)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 text-sm text-yellow-800">
                                "{{ $donation->message }}"
                            </div>
                        @endif

                        <div class="space-y-4">
                            <h4 class="font-semibold text-navy-800">Langkah Terakhir: Verifikasi</h4>
                            <p class="text-sm text-navy-400 mb-4">
                                Kirim pesan ke bot kami untuk mendapatkan kode penukaran makanan.
                            </p>
                            
                            <a href="https://wa.me/6285117773642?text={{ rawurlencode("> Verify\n\nHalo Minje! 👋\nAku mau konfirmasi pendaftaran sebagai Food Heroes.\n\n*Signature* _" . session('code') . '_') }}" 
                               class="block w-full bg-navy-600 hover:bg-navy-700 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg transform transition hover:-translate-y-0.5">
                                🚀 Verifikasi Sekarang
                            </a>
                            
                            <a href="{{ route('hero.cancel') }}" 
                               class="block w-full text-red-500 font-medium py-2 hover:text-red-700 text-sm transition-colors">
                                Batalkan Pendaftaran
                            </a>
                        </div>
                    </div>

                    {{-- Info Card --}}
                    <div class="bg-gray-50 p-6 border-t border-gray-100">
                        <h4 class="font-semibold text-navy-900 mb-4 flex items-center gap-2">
                             ℹ️ Informasi Pengambilan
                        </h4>
                        <div class="space-y-3">
                            <a href="{{ $donation->maps }}" target="_blank" class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-200 hover:border-tosca-300 transition-colors group">
                                <span class="text-lg group-hover:scale-110 transition-transform">📍</span>
                                <div>
                                    <p class="text-sm font-semibold text-navy-700">Lokasi</p>
                                    <p class="text-sm text-navy-500">{{ $donation->location }}</p>
                                </div>
                            </a>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-200">
                                <span class="text-lg">⏰</span>
                                <div>
                                    <p class="text-sm font-semibold text-navy-700">Waktu</p>
                                    <p class="text-sm text-navy-500">{{ $donation->hour }}.{{ str_pad($donation->minute, 2, '0', STR_PAD_LEFT) }} WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                {{-- Listings --}}
                <div x-data="{ activeTab: 0 }">
                    {{-- Tabs --}}
                    <div class="flex flex-wrap gap-2 mb-6 justify-center">
                        @foreach ($donations as $id => $donation)
                            <button @click="activeTab = {{ $id }}"
                                :class="{ 'bg-navy-600 text-white shadow-lg scale-105': activeTab === {{ $id }}, 'bg-white text-navy-600 hover:bg-gray-50': activeTab !== {{ $id }} }"
                                class="px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 border border-navy-100 text-sm">
                                {{ $donation->sponsor->name }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Cards --}}
                    <div class="relative min-h-[500px]">
                        @foreach ($donations as $id => $donation)
                            <div x-show="activeTab === {{ $id }}" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-4"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute w-full top-0 left-0">
                                
                                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-navy-50">
                                    <div class="bg-gradient-to-r from-navy-500 to-tosca-600 p-8 text-center text-white relative">
                                        <div class="absolute top-0 right-0 p-4 opacity-20">
                                            <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                                        </div>
                                        <h2 class="text-2xl font-bold relative z-10">BBJ X {{ $donation->sponsor->name }}</h2>
                                        <div class="inline-block bg-white/20 px-4 py-1.5 rounded-full text-xs font-semibold mt-3 backdrop-blur-sm relative z-10">
                                            {{ Carbon::parse($donation->take)->isoFormat('dddd, DD MMMM Y') }}
                                        </div>
                                    </div>

                                    <div class="p-8">
                                        {{-- Quota Display --}}
                                        <div class="flex justify-center mb-8">
                                            <div class="text-center">
                                                <p class="text-xs uppercase tracking-wide text-navy-400 font-semibold mb-1">Sisa Kuota</p>
                                                <div class="text-4xl font-black text-tosca-500">
                                                    {{ $donation->quota - $donation->remain }}<span class="text-navy-200 text-2xl">/{{ $donation->quota }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($donation->remain > 0)
                                            <form action="{{ route('hero.store') }}" method="POST" id="bbjForm{{ $id }}" class="space-y-5">
                                                @csrf
                                                <input type="hidden" name="donation" value="{{ $donation->id }}">
                                                
                                                <div class="form-group">
                                                    <label class="block text-xs font-bold text-navy-400 uppercase mb-2">Asal</label>
                                                    <div class="relative">
                                                        <select class="university-select w-full px-4 py-3.5 rounded-xl bg-gray-50 border-gray-200 text-navy-900 font-medium focus:ring-2 focus:ring-tosca-500 focus:border-transparent transition-shadow outline-none appearance-none" required>
                                                            <option value="">Pilih Asal Universitas/Instansi...</option>
                                                            @foreach (App\Models\Heroes\University::whereIn('id', json_decode($donation->beneficiaries))->get() as $item)
                                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-navy-400">▼</div>
                                                    </div>
                                                </div>

                                                <div class="form-group hidden faculty-group">
                                                    <label class="block text-xs font-bold text-navy-400 uppercase mb-2">Fakultas/Bagian</label>
                                                    <div class="relative">
                                                        <select name="faculty" class="faculty-select w-full px-4 py-3.5 rounded-xl bg-gray-50 border-gray-200 text-navy-900 font-medium focus:ring-2 focus:ring-tosca-500 focus:border-transparent transition-shadow outline-none appearance-none" required>
                                                            {{-- Populated via JS --}}
                                                        </select>
                                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-navy-400">▼</div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="block text-xs font-bold text-navy-400 uppercase mb-2">Nama Lengkap</label>
                                                    <input type="text" name="name" 
                                                           class="w-full px-4 py-3.5 rounded-xl bg-gray-50 border-gray-200 text-navy-900 font-medium placeholder:text-gray-400 focus:ring-2 focus:ring-tosca-500 focus:border-transparent transition-shadow outline-none"
                                                           placeholder="Masukkan nama lengkap..." required>
                                                </div>

                                                <div class="form-group">
                                                    <label class="block text-xs font-bold text-navy-400 uppercase mb-2">WhatsApp</label>
                                                    <div class="relative">
                                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-navy-500">+62</span>
                                                        <input type="tel" name="phone" 
                                                               class="w-full pl-14 pr-4 py-3.5 rounded-xl bg-gray-50 border-gray-200 text-navy-900 font-medium placeholder:text-gray-400 focus:ring-2 focus:ring-tosca-500 focus:border-transparent transition-shadow outline-none"
                                                               placeholder="812345678" required>
                                                    </div>
                                                    @error('phone')
                                                        <p class="text-xs text-red-500 mt-2 font-medium">⚠️ Format nomor tidak valid</p>
                                                    @enderror
                                                </div>

                                                <button type="button" @click="$dispatch('open-modal', { formId: 'bbjForm{{ $id }}' })"
                                                        class="w-full bg-navy-600 hover:bg-navy-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-navy-200 transform transition hover:-translate-y-1 active:translate-y-0 mt-8">
                                                    🎁 Ambil Bagian
                                                </button>
                                            </form>

                                        @else
                                            {{-- Sold Out State --}}
                                            <div class="text-center py-8">
                                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <span class="text-4xl">😔</span>
                                                </div>
                                                <h3 class="text-lg font-bold text-navy-900 mb-2">Kuota Terpenuhi</h3>
                                                <p class="text-navy-400 text-sm mb-6">Jangan sedih! Masih ada kesempatan lain kali.</p>
                                                
                                                <a href="{{ route('notify.form') }}" class="inline-flex items-center justify-center px-6 py-3 border border-navy-200 rounded-xl text-navy-700 font-semibold hover:bg-navy-50 transition-colors">
                                                    🔔 Ingatkan Saya Nanti
                                                </a>
                                                <p class="text-xs text-gray-400 mt-4 px-8">
                                                    *Hanya berlaku untuk email UGM (mail.ugm.ac.id)
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-3xl shadow-xl p-12 text-center border border-navy-50">
                <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-5xl">🍽️</span>
                </div>
                <h2 class="text-2xl font-bold text-navy-900 mb-3">Belum Ada Aksi Hari Ini</h2>
                <p class="text-navy-500 mb-8 max-w-xs mx-auto">Kami sedang mempersiapkan makanan lezat untuk diselamatkan. Cek lagi nanti ya!</p>
                
                <a href="{{ route('notify.form') }}" class="inline-block bg-tosca-500 hover:bg-tosca-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-tosca-200 transition-all transform hover:-translate-y-1">
                    🔔 Beritahu Saya Jika Ada
                </a>
            </div>
        @endif
    </div>

    {{-- Stats Section --}}
    <div class="mt-20 pb-20">
        <h2 class="text-center text-2xl font-bold text-navy-900 mb-8">Dampak Bersama</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-navy-50 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-tosca-100 rounded-xl flex items-center justify-center text-2xl">⚡</div>
                <div>
                    <p class="text-xs font-bold text-navy-400 uppercase tracking-wider">Total Aksi</p>
                    <p class="text-2xl font-black text-navy-900">{{ $donations_sum }}</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-navy-50 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-2xl">🥪</div>
                <div>
                    <p class="text-xs font-bold text-navy-400 uppercase tracking-wider">Makanan Diselamatkan</p>
                    <p class="text-2xl font-black text-navy-900">{{ $foods }} <span class="text-sm font-semibold text-navy-400">kg</span></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-navy-50 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-navy-100 rounded-xl flex items-center justify-center text-2xl">🦸‍♂️</div>
                <div>
                    <p class="text-xs font-bold text-navy-400 uppercase tracking-wider">Total Heroes</p>
                    <p class="text-2xl font-black text-navy-900">{{ $heroes }} <span class="text-sm font-semibold text-navy-400">orang</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Component --}}
    <div x-data="{ open: false, formId: null }"
         @open-modal.window="open = true; formId = $event.detail.formId"
         x-show="open" 
         class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;">
        
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-navy-900/60 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-3xl shadow-2xl max-w-sm w-full relative z-10 overflow-hidden">
            
            <div class="bg-orange-500 p-6 text-white text-center">
                <div class="text-4xl mb-2">⚠️</div>
                <h3 class="text-xl font-bold">Komitmen Heroes</h3>
            </div>

            <div class="p-6">
                <ul class="space-y-4 text-sm text-navy-700">
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 mt-0.5">🔹</span>
                        <p>Pengambilan yang terlambat akan <strong>dialihkan</strong> ke Heroes lain.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 mt-0.5">🔹</span>
                        <p>Makanan wajib dikonsumsi <strong>segera</strong> setelah diterima.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 mt-0.5">🔹</span>
                        <p>BBJ tidak bertanggung jawab atas kelalaian penyimpanan/konsumsi.</p>
                    </li>
                </ul>

                <div class="grid grid-cols-2 gap-3 mt-8">
                    <button @click="open = false" class="px-4 py-3 rounded-xl border border-gray-200 font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button @click="document.getElementById(formId).submit()" class="px-4 py-3 rounded-xl bg-navy-600 text-white font-bold hover:bg-navy-700 transition-colors">
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.querySelectorAll('.university-select').forEach(select => {
            select.addEventListener('change', function() {
                const form = this.closest('form');
                const facultyGroup = form.querySelector('.faculty-group');
                const facultySelect = form.querySelector('.faculty-select');
                
                if (this.value) {
                    facultyGroup.classList.remove('hidden');
                    facultySelect.innerHTML = '<option value="">Memuat...</option>';
                    
                    fetch(`/api/university/${this.value}/faculty`)
                        .then(r => r.json())
                        .then(data => {
                            let options = '<option value="">Pilih Fakultas/Bagian...</option>';
                            data.filter(e => !['Volunteer', 'RZIS', 'Lainnya'].includes(e.name)).forEach(e => {
                                options += `<option value="${e.id}">${e.name}</option>`;
                            });
                            facultySelect.innerHTML = options;
                        });
                } else {
                    facultyGroup.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
