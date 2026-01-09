@props(['icon' => 'plus', 'label' => '', 'onClick' => ''])

<button 
    @click="{{ $onClick }}"
    {{ $attributes->merge(['class' => 'fixed bottom-8 right-8 z-[50] flex items-center justify-center w-14 h-14 bg-gradient-to-r from-navy-600 to-tosca-600 text-white rounded-full shadow-2xl hover:scale-110 active:scale-95 transition-all duration-300 md:hidden group']) }}
>
    @if($icon === 'plus')
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
    @else
        {{ $slot }}
    @endif
    
    @if($label)
        <span class="absolute right-full mr-3 px-3 py-1 bg-navy-900 text-white text-xs font-bold rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
            {{ $label }}
        </span>
    @endif
</button>
