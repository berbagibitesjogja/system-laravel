@extends('layouts.main')
@section('container')
    <div x-data="{ activeTab: 'stats' }">
        <div class="flex flex-wrap gap-3 items-center mb-8">
            <x-btn-link href="{{ route('volunteer.index') }}" variant="ghost">
                ← Kembali ke Daftar
            </x-btn-link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Profile Card --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl border border-navy-100 shadow-sm overflow-hidden">
                    <div class="h-32 bg-gradient-to-br from-navy-600 to-tosca-500 relative">
                        <div class="absolute -bottom-12 left-1/2 -translate-x-1/2">
                            <div class="w-24 h-24 rounded-3xl border-4 border-white shadow-xl overflow-hidden bg-white">
                                <img src="{{ $volunteer->photo }}" alt="{{ $volunteer->name }}" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                    <div class="pt-16 pb-8 px-6 text-center">
                        <h1 class="text-2xl font-black text-navy-900">{{ $volunteer->name }}</h1>
                        <p class="text-sm font-bold text-tosca-600 uppercase tracking-widest mt-1">{{ $volunteer->division->name }}</p>
                        
                        <div class="flex justify-center gap-2 mt-4">
                            @php
                                $impact = $volunteer->attendances->count();
                                $level = $impact > 15 ? 'Elite' : ($impact > 10 ? 'Gold' : ($impact > 5 ? 'Silver' : 'Bronze'));
                                $levelColor = match($level) {
                                    'Elite' => 'bg-purple-100 text-purple-600 border-purple-200',
                                    'Gold' => 'bg-yellow-100 text-yellow-600 border-yellow-200',
                                    'Silver' => 'bg-slate-100 text-slate-500 border-slate-200',
                                    default => 'bg-orange-100 text-orange-600 border-orange-200',
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter border {{ $levelColor }}">
                                {{ $level }} Member
                            </span>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter border bg-navy-100 text-navy-600 border-navy-200">
                                {{ strtoupper($volunteer->role) }}
                            </span>
                        </div>
                        <div class="flex flex-col items-center gap-4 mt-6">
                            <x-btn @click="$dispatch('open-share')" variant="tosca" class="!px-6 !py-2 !rounded-2xl text-[10px] uppercase font-black tracking-widest">
                                🤳 Berbagi Impact
                            </x-btn>
                        </div>

                        <x-share-card 
                            title="Hero of the Month"
                            :name="$volunteer->name"
                            :avatar="$volunteer->photo"
                            :stats="[
                                ['label' => 'Total Aksi', 'value' => $impact, 'unit' => 'Aksi'],
                                ['label' => 'Impact Level', 'value' => $level],
                                ['label' => 'Total Makanan', 'value' => 0, 'unit' => 'Kg'],
                                ['label' => 'Rank', 'value' => '#' . $rank],
                            ]"
                        />
                        <div class="px-6 py-4 bg-navy-50/50 border-t border-navy-50 grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-[10px] font-bold text-navy-400 uppercase tracking-widest">Total Aksi</p>
                                <p class="text-xl font-black text-navy-900">{{ $impact }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-[10px] font-bold text-navy-400 uppercase tracking-widest">Rank</p>
                                <p class="text-xl font-black text-tosca-600">#{{ $rank }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="bg-white rounded-3xl border border-navy-100 p-6 space-y-4">
                    <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest border-b border-navy-50 pb-3">Informasi Kontak</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-navy-50 flex items-center justify-center text-navy-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-navy-400 uppercase leading-none">Email</p>
                            <p class="text-sm font-bold text-navy-900">{{ $volunteer->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-lime-50 flex items-center justify-center text-lime-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-navy-400 uppercase leading-none">WhatsApp</p>
                            <p class="text-sm font-bold text-navy-900">{{ $volunteer->phone }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Tabs & Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Tab Navigation --}}
                <div class="flex bg-navy-50 p-1.5 rounded-2xl w-max">
                    <button @click="activeTab = 'stats'" :class="activeTab === 'stats' ? 'bg-white shadow-sm text-navy-900' : 'text-navy-400 hover:text-navy-600'" class="px-6 py-2 rounded-xl text-sm font-bold transition-all">
                        🚀 Progress
                    </button>
                    <button @click="activeTab = 'edit'" :class="activeTab === 'edit' ? 'bg-white shadow-sm text-navy-900' : 'text-navy-400 hover:text-navy-600'" class="px-6 py-2 rounded-xl text-sm font-bold transition-all">
                        ⚙️ Pengaturan
                    </button>
                </div>

                {{-- Stats Tab --}}
                <div x-show="activeTab === 'stats'" x-transition class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-3xl border border-navy-100">
                            <h3 class="text-sm font-black text-navy-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-lime-500 rounded-full"></span>
                                Aksi Terakhir
                            </h3>
                            <div class="space-y-6 relative">
                                <div class="absolute left-3 top-2 bottom-2 w-0.5 bg-navy-50"></div>
                                @forelse($activities as $activity)
                                    <div class="relative pl-8">
                                        <div class="absolute left-1.5 top-1 w-3 h-3 rounded-full border-2 border-white bg-navy-500"></div>
                                        <p class="text-sm font-bold text-navy-900">{{ $activity->description }} {{ class_basename($activity->subject_type) }}</p>
                                        <p class="text-[10px] font-bold text-navy-300 uppercase">{{ $activity->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <p class="text-center text-navy-400 text-sm py-8 italic">Belum ada aktivitas tercatat.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-navy-100 flex flex-col items-center justify-center text-center">
                            <div class="w-32 h-32 relative">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-navy-50" />
                                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="12" fill="transparent" 
                                        stroke-dasharray="364.4" 
                                        stroke-dashoffset="{{ 364.4 * (1 - min($impact/15, 1)) }}" 
                                        class="text-tosca-500 transition-all duration-1000" stroke-linecap="round" />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-3xl font-black text-navy-900">{{ $impact }}</span>
                                    <span class="text-[8px] font-black text-navy-400 uppercase tracking-widest">Level Up At 15</span>
                                </div>
                            </div>
                            <h3 class="mt-6 text-sm font-black text-navy-900 uppercase tracking-widest">Progress Menuju Master</h3>
                            <p class="text-xs text-navy-400 mt-1">Selesaikan {{ 15 - $impact }} aksi lagi untuk rank Elite!</p>
                        </div>
                    </div>
                </div>

                {{-- Edit Tab --}}
                <div x-show="activeTab === 'edit'" x-transition>
                    <div class="bg-white rounded-3xl border border-navy-100 p-8 shadow-sm">
                        <form method="POST" action="{{ route('volunteer.update', $volunteer->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if (auth()->user()->role == 'super')
                                    <x-select name="division_id" label="Divisi">
                                        @foreach ($divisions as $item)
                                            <option {{ $volunteer->division_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </x-select>
                                    
                                    <x-select name="role" label="Role Sistem">
                                        <option {{ $volunteer->role == 'super' ? 'selected' : '' }} value="super">Super Admin</option>
                                        <option {{ $volunteer->role == 'core' ? 'selected' : '' }} value="core">Pengurus Inti</option>
                                        <option {{ $volunteer->role == 'staff' ? 'selected' : '' }} value="staff">Staff</option>
                                        <option {{ $volunteer->role == 'member' ? 'selected' : '' }} value="member">Volunteer</option>
                                    </x-select>
                                @endif

                                <x-input name="email" label="Alamat Email" type="email" value="{{ $volunteer->email }}" required />
                                <x-input name="phone" label="No. WhatsApp" type="tel" value="{{ $volunteer->phone }}" required />
                            </div>
                            
                            <x-btn type="submit" variant="primary" class="w-full h-12 mt-8">
                                💾 Simpan Data Profil
                            </x-btn>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
