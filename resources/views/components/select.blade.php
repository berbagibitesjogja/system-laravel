@props(['name', 'label'])

<div class="mb-6">
    <label for="{{ $name }}" class="block mb-2 text-sm font-semibold text-navy-700">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}"
        {{ $attributes->merge(['class' => 'w-full px-4 py-3 text-sm text-navy-900 bg-white border border-navy-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 transition-all duration-300 cursor-pointer']) }}>
        {{ $slot }}
    </select>
</div>
