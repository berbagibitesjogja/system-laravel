@props(['variant' => 'primary'])

@php
    $classes = 'focus:ring-4 focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center transition duration-300 ';
    switch ($variant) {
        case 'primary':
            $classes .= 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-blue-300';
            break;
        case 'pink':
             $classes .= 'text-white bg-pink-700 hover:bg-pink-800 focus:ring-pink-300';
             break;
        case 'navy':
             $classes .= 'text-white bg-navy hover:bg-navy-700'; // custom
             break;
        default:
            $classes .= 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-blue-300';
    }
@endphp

<button {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
