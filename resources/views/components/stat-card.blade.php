@props(['icon', 'label', 'value'])

@php
    // Extract numbers from value for animation
    preg_match('/(\d+)/', $value, $matches);
    $number = $matches[0] ?? 0;
    $suffix = str_replace($number, '', $value);
@endphp

<div 
    x-data="{ count: 0, target: {{ $number }}, duration: 1500 }"
    x-init="
        let start = null;
        const step = (timestamp) => {
            if (!start) start = timestamp;
            const progress = Math.min((timestamp - start) / duration, 1);
            count = Math.floor(progress * target);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    "
    class="bg-gradient-to-br from-white to-lime-50 rounded-2xl shadow-md hover:shadow-xl p-6 w-full flex gap-5 items-center border border-navy-50 transition-all duration-300 group hover:-translate-y-1"
>
    <div class="bg-tosca-500 rounded-2xl p-4 w-max flex justify-center items-center group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 shadow-lg shadow-tosca-100">
        <img width="32px" src="{{ $icon }}" alt="{{ $label }}" class="brightness-0 invert">
    </div>
    <div>
        <p class="text-navy-400 text-xs font-bold uppercase tracking-wider mb-1">{{ $label }}</p>
        <p class="font-bold text-3xl text-navy-900 flex items-baseline gap-1">
            <span x-text="count">0</span>
            <span class="text-lg font-semibold text-navy-400">{{ $suffix }}</span>
        </p>
    </div>
</div>
