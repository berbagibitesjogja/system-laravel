@props(['icon', 'label', 'value'])

<div class="bg-white rounded-lg shadow-md p-4 w-full flex gap-2 hover:shadow-lg transition-shadow duration-300">
    <div class="bg-tosca rounded-full px-2.5 py-2 w-max flex justify-center items-center">
        <img width="36px" src="{{ $icon }}" alt="{{ $label }}">
    </div>
    <div>
        <p class="text-slate-600 italic text-xs sm:text-xs md:text-sm">{{ $label }}</p>
        <p class="font-bold text-md sm:text-lg md:text-xl">{{ $value }}</p>
    </div>
</div>
