@extends('layouts.main')
@section('container')
    <div class="mb-12 text-center">
        <h1 class="text-4xl font-black text-navy-900 tracking-tight">Hall of <span class="text-tosca-500 italic">Fame</span></h1>
        <p class="text-navy-400 mt-2 font-medium italic">Merayakan pahlawan yang memberi dampak nyata setiap hari 🌟</p>
    </div>

    {{-- The Podium --}}
    <div class="flex flex-col md:flex-row items-end justify-center gap-4 mb-20 px-4">
        {{-- Rank 2 --}}
        @if(isset($topVolunteers[1]))
            <div class="flex-1 max-w-[200px] flex flex-col items-center group">
                <div class="relative mb-4">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden border-4 border-slate-200 shadow-lg group-hover:scale-110 transition-transform">
                        <img src="{{ $topVolunteers[1]->photo }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center text-slate-600 font-black shadow-md border-2 border-white">2</div>
                </div>
                <div class="w-full bg-slate-100 rounded-t-3xl p-6 text-center border-x border-t border-slate-200 shadow-sm min-h-[140px] flex flex-col justify-center">
                    <p class="font-bold text-navy-900 text-sm line-clamp-1 lowercase italic">"{{ $topVolunteers[1]->name }}"</p>
                    <p class="text-2xl font-black text-slate-500 mt-1">{{ $topVolunteers[1]->attendances_count }}</p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</p>
                </div>
            </div>
        @endif

        {{-- Rank 1 (The King) --}}
        @if(isset($topVolunteers[0]))
            <div class="flex-1 max-w-[240px] flex flex-col items-center group relative z-10 -order-1 md:order-none">
                <div class="absolute -top-12 text-4xl group-hover:bounce transition-all">👑</div>
                <div class="relative mb-6">
                    <div class="w-28 h-28 rounded-3xl overflow-hidden border-4 border-yellow-400 shadow-2xl group-hover:scale-110 transition-transform ring-4 ring-yellow-400/20">
                        <img src="{{ $topVolunteers[0]->photo }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -top-3 -right-3 w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-white font-black shadow-lg border-2 border-white">1</div>
                </div>
                <div class="w-full bg-gradient-to-b from-yellow-50 to-white rounded-t-[2.5rem] p-8 text-center border-x border-t border-yellow-200 shadow-xl min-h-[180px] flex flex-col justify-center">
                    <p class="font-black text-navy-900 text-lg line-clamp-1 lowercase italic">"{{ $topVolunteers[0]->name }}"</p>
                    <p class="text-4xl font-black text-yellow-600 mt-1">{{ $topVolunteers[0]->attendances_count }}</p>
                    <p class="text-xs font-black text-yellow-700 uppercase tracking-[0.2em]">Master Impact</p>
                </div>
            </div>
        @endif

        {{-- Rank 3 --}}
        @if(isset($topVolunteers[2]))
            <div class="flex-1 max-w-[180px] flex flex-col items-center group">
                <div class="relative mb-4">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden border-4 border-orange-200 shadow-md group-hover:scale-110 transition-transform">
                        <img src="{{ $topVolunteers[2]->photo }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -top-2 -right-2 w-7 h-7 bg-orange-200 rounded-full flex items-center justify-center text-orange-700 font-black shadow-sm border-2 border-white">3</div>
                </div>
                <div class="w-full bg-orange-50 rounded-t-2xl p-4 text-center border-x border-t border-orange-100 shadow-sm min-h-[120px] flex flex-col justify-center">
                    <p class="font-bold text-navy-900 text-xs line-clamp-1 lowercase italic">"{{ $topVolunteers[2]->name }}"</p>
                    <p class="text-xl font-black text-orange-600 mt-1">{{ $topVolunteers[2]->attendances_count }}</p>
                    <p class="text-[9px] font-black text-orange-400 uppercase tracking-widest">Aksi</p>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Remaining Top Volunteers --}}
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-xs font-black text-navy-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-3">
                Top Volunteers
                <span class="flex-1 h-px bg-navy-50"></span>
            </h3>
            
            <div class="bg-white rounded-[2rem] border border-navy-100 divide-y divide-navy-50 overflow-hidden shadow-sm">
                @foreach($topVolunteers->skip(3) as $index => $item)
                    <div class="flex items-center gap-4 p-5 hover:bg-navy-50 transition-colors group">
                        <span class="w-8 text-sm font-black text-navy-300">#{{ $index }}</span>
                        <div class="w-10 h-10 rounded-xl overflow-hidden shadow-sm">
                            <img src="{{ $item->photo }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-navy-900 group-hover:text-tosca-600 transition-colors">{{ $item->name }}</p>
                            <p class="text-[10px] font-bold text-navy-400 uppercase tracking-widest">{{ $item->division->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-black text-navy-900 leading-none">{{ $item->attendances_count }}</p>
                            <p class="text-[10px] font-black text-navy-400 uppercase tracking-tighter">Aksi</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top Partners / Sponsors --}}
        <div class="space-y-4">
            <h3 class="text-xs font-black text-navy-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-3">
                Top Partners
                <span class="flex-1 h-px bg-navy-50"></span>
            </h3>

            <div class="space-y-4">
                @foreach($topSponsors as $index => $sponsor)
                    <div class="bg-white p-5 rounded-3xl border border-navy-100 shadow-sm flex items-center gap-4 group hover:-translate-y-1 transition-all">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-black font-black text-xl shadow-lg relative shrink-0">
                            @if($sponsor->logo)
                                <img src="{{ $sponsor->logo }}" class="w-max h-max object-cover rounded-2xl">
                            @else
                                {{ substr($sponsor->name, 0, 1) }}
                            @endif
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-tosca-500 rounded-full border-2 border-white flex items-center justify-center text-[10px] font-black">
                                {{ $index + 1 }}
                            </div>
                        </div>
                        <div>
                            <p class="font-bold text-navy-900 group-hover:text-tosca-600 transition-colors">{{ $sponsor->name }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-black text-tosca-600">{{ $sponsor->donation_count }}</span>
                                <span class="text-[10px] font-black text-navy-400 uppercase">Donasi Berhasil</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- CTA / Motivational Box --}}
            <div class="mt-8 bg-gradient-to-br from-navy-900 to-navy-800 rounded-[2rem] p-8 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="text-lg font-black leading-tight italic">"Setiap gigitan yang diselamatkan adalah satu senyuman yang tercipta."</h4>
                    <p class="text-xs text-navy-300 mt-4 leading-relaxed font-medium">Jadilah bagian dari Hall of Fame berikutnya dengan berkontribusi pada aksi terdekat!</p>
                    <x-btn-link href="{{ route('donation.rescue') }}" variant="tosca" class="w-full mt-6 !rounded-2xl !py-3">
                        Lihat Aksi Tersedia
                    </x-btn-link>
                </div>
                {{-- Decorative circles --}}
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-tosca-500/20 rounded-full blur-2xl"></div>
                <div class="absolute top-4 left-4 w-12 h-12 bg-white/5 rounded-full blur-xl"></div>
            </div>
        </div>
    </div>
@endsection
