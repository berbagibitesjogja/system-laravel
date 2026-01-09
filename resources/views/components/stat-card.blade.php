@props(['icon', 'label', 'value'])

<div class="bg-gradient-to-br from-white to-lime-100 rounded-xl shadow-md hover:shadow-lg p-5 w-full flex gap-4 items-center border border-navy-100 transition-all duration-300 group">
    <div class="bg-tosca-500 rounded-xl p-3 w-max flex justify-center items-center group-hover:scale-110 transition-transform duration-300">
        <img width="32px" src="{{ $icon }}" alt="{{ $label }}" class="brightness-0 invert">
    </div>
    <div>
        <p class="text-navy-400 text-xs font-medium uppercase tracking-wide">{{ $label }}</p>
        <p class="font-bold text-2xl text-navy-900">{{ $value }}</p>
    </div>
</div>
