@props(['variant' => 'primary'])

@php
    $base = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 cursor-pointer ';
    
    switch ($variant) {
        case 'primary':
            $classes = $base . 'text-white bg-navy-500 hover:bg-navy-600 focus:ring-navy-300';
            break;
        case 'pink':
            $classes = $base . 'text-white bg-pink-500 hover:bg-pink-600 focus:ring-pink-300';
            break;
        case 'navy':
            $classes = $base . 'text-white bg-navy-500 hover:bg-navy-600 focus:ring-navy-300';
            break;
        case 'danger':
            $classes = $base . 'text-white bg-red-500 hover:bg-red-600 focus:ring-red-300';
            break;
        case 'success':
            $classes = $base . 'text-navy-900 bg-lime-400 hover:bg-lime-500 focus:ring-lime-300';
            break;
        default:
            $classes = $base . 'text-white bg-navy-500 hover:bg-navy-600 focus:ring-navy-300';
    }
@endphp

<button {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
