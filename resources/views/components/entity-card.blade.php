@props([
    'name',
    'id',
    'image' => null,
    'variant' => '',
    'stats' => [],
    'showRoute' => '',
    'editRoute' => '',
    'deleteRoute' => '',
    'badge' => null
])

<div {{ $attributes->merge(['class' => 'group bg-white rounded-3xl border border-navy-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col']) }}>
    <div class="h-2 bg-gradient-to-r from-navy-500 to-tosca-500"></div>
    
    <div class="p-6 flex-1">
        <div class="flex items-start justify-between mb-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-navy-50 to-tosca-50 flex items-center justify-center text-navy-600 font-black text-2xl border border-navy-50 group-hover:scale-110 transition-transform duration-500 shrink-0 uppercase">
                @if($image)
                    <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover rounded-2xl">
                @else
                    {{ substr($name, 0, 1) }}
                @endif
            </div>

            @if($badge)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-tosca-100 text-tosca-600 border border-tosca-200">
                    {{ $badge }}
                </span>
            @endif
        </div>

        <h3 class="text-xl font-bold text-navy-900 group-hover:text-tosca-600 transition-colors line-clamp-1">{{ $name }}</h3>
        
        @if($variant)
            <p class="text-xs font-bold text-navy-400 mt-1 uppercase tracking-wider italic opacity-80">{{ $variant }}</p>
        @endif

        <div class="grid grid-cols-2 gap-4 mt-8">
            @foreach($stats as $stat)
                <div>
                    <p class="text-[10px] font-bold text-navy-400 uppercase tracking-widest">{{ $stat['label'] }}</p>
                    <p class="text-lg font-black text-navy-900 leading-none mt-1">
                        {{ $stat['value'] }} <span class="text-[10px] font-bold text-navy-400 uppercase">{{ $stat['unit'] ?? '' }}</span>
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="px-6 py-4 bg-navy-50/50 border-t border-navy-50 flex items-center justify-between gap-2">
        @if($showRoute)
            <a href="{{ $showRoute }}" class="flex-1 text-center py-2 px-4 rounded-xl text-xs font-bold bg-white text-navy-600 border border-navy-100 hover:bg-navy-600 hover:text-white hover:border-navy-600 transition-all">
                🔎 Detail
            </a>
        @endif
        
        @auth
            <div class="flex gap-2">
                @if($editRoute)
                    <a href="{{ $editRoute }}" class="p-2 rounded-xl bg-white text-yellow-500 border border-navy-100 hover:bg-yellow-500 hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </a>
                @endif
                
                @if($deleteRoute && in_array(auth()->user()->role, ['super']))
                    <button type="button" @click="deleteUrl = '{{ $deleteRoute }}'; showDeleteModal = true" class="p-2 rounded-xl bg-white text-red-500 border border-navy-100 hover:bg-red-500 hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                @endif
            </div>
        @endauth
    </div>
</div>
