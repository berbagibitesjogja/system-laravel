@extends('layouts.main')
@section('container')
    <div class="min-h-[500px] flex flex-col items-center justify-center text-center p-8">
        <div class="relative">
            <div class="w-32 h-32 bg-gradient-to-tr from-tosca-200 to-lime-200 rounded-full blur-3xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-50 animate-pulse"></div>
            <div class="w-24 h-24 bg-white rounded-2xl shadow-xl flex items-center justify-center relative z-10 mx-auto mb-6 transform rotate-3 hover:rotate-6 transition-transform">
                <span class="text-5xl">🚧</span>
            </div>
        </div>
        
        <h1 class="text-4xl font-black text-navy-900 mb-4 tracking-tight">Coming Soon!</h1>
        <p class="text-lg text-navy-500 max-w-md mx-auto mb-8">
            Fitur ini sedang disiapkan dengan penuh kasih sayang. Tunggu tanggal mainnya ya! ✨
        </p>

        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-6 py-3 bg-navy-50 text-navy-700 rounded-xl font-bold hover:bg-navy-100 transition-colors">
            <span>🔙</span> Kembali dulu
        </a>
    </div>
@endsection