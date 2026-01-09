@extends('layouts.form')
@section('container')
    <div class="max-w-md mx-auto pt-10 pb-12">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-navy-50">
            <div class="bg-gradient-to-r from-navy-600 to-navy-800 p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-white opacity-5 mix-blend-overlay" 
                     style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
                <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <span class="text-3xl">🔔</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Pasang Pengingat</h1>
                <p class="text-navy-100 text-sm">Jangan lewatkan kesempatan berbagi berikutnya!</p>
            </div>

            <form action="" class="p-8">
                <div class="mb-6">
                    <label class="block text-xs font-bold text-navy-400 uppercase mb-2">WhatsApp</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="font-bold text-navy-500 group-focus-within:text-tosca-500 transition-colors">+62</span>
                        </div>
                        <input type="tel" name="phone" 
                               class="w-full pl-14 pr-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 text-navy-900 font-semibold placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-tosca-500 focus:border-transparent transition-all"
                               placeholder="812345678" required>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-tosca-500 hover:bg-tosca-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-tosca-200 transform transition hover:-translate-y-1 active:translate-y-0 flex items-center justify-center gap-2">
                    <span>✨</span> Aktifkan Notifikasi
                </button>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-400 bg-gray-50 py-2 px-4 rounded-lg inline-block">
                        *Hanya berlaku untuk email UGM (mail.ugm.ac.id)
                    </p>
                </div>
            </form>
        </div>
        
        <div class="text-center mt-8">
            <a href="/" class="text-sm font-semibold text-navy-400 hover:text-navy-600 transition-colors">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection
