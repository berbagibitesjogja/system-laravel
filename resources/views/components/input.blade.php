@props(['name', 'label', 'type' => 'text'])

<div class="relative w-full mb-6 group">
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
        {{ $attributes->merge(['class' => 'block w-full px-4 py-3 text-sm text-navy-900 bg-white border border-navy-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 peer transition-all duration-300']) }}
        placeholder=" " />
    <label for="{{ $name }}"
        class="absolute text-sm text-navy-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-tosca-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-3 font-medium">
        {{ $label }}
    </label>
</div>
