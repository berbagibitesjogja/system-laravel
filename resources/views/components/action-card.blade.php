@props(['href', 'icon' => null, 'color' => 'navy'])

@php
    $colorClasses = match($color) {
        'navy' => 'bg-navy-500 group-hover:bg-navy-600',
        'tosca' => 'bg-tosca-500 group-hover:bg-tosca-600',
        'orange' => 'bg-orange-500 group-hover:bg-orange-600',
        'lime' => 'bg-lime-500 group-hover:bg-lime-600',
        default => 'bg-navy-500 group-hover:bg-navy-600',
    };
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group block p-4 bg-white rounded-xl border border-navy-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5']) }}>
    <div class="flex items-center gap-4">
        @if($icon)
            <div class="{{ $colorClasses }} rounded-xl p-3 transition-colors duration-300">
                <img src="{{ $icon }}" alt="" class="w-6 h-6 brightness-0 invert">
            </div>
        @endif
        <div class="flex-1">
            <p class="font-semibold text-navy-900 group-hover:text-navy-600 transition-colors duration-300">
                {{ $slot }}
            </p>
        </div>
        <svg class="w-5 h-5 text-navy-300 group-hover:text-navy-500 group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </div>
</a>
