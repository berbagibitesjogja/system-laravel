@props(['variant' => 'primary'])

@php
    $base = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 ';
    
    switch ($variant) {
        case 'primary':
            $classes = $base . 'text-white bg-navy-500 hover:bg-navy-600 focus:ring-navy-300';
            break;
        case 'warning':
            $classes = $base . 'text-white bg-orange-500 hover:bg-orange-600 focus:ring-orange-300';
            break;
        case 'success':
            $classes = $base . 'text-navy-900 bg-lime-400 hover:bg-lime-500 focus:ring-lime-300';
            break;
        case 'danger':
            $classes = $base . 'text-white bg-red-500 hover:bg-red-600 focus:ring-red-300';
            break;
        case 'info':
            $classes = $base . 'text-white bg-tosca-500 hover:bg-tosca-600 focus:ring-tosca-300';
            break;
        case 'navy':
            $classes = $base . 'text-white bg-navy-500 hover:bg-navy-600 focus:ring-navy-300';
            break;
        case 'tosca':
            $classes = $base . 'text-white bg-tosca-500 hover:bg-tosca-600 focus:ring-tosca-300 p-2.5';
            break;
        case 'yellow':
            $classes = $base . 'text-navy-900 bg-yellow-400 hover:bg-yellow-500 focus:ring-yellow-300 p-2.5';
            break;
        case 'ghost':
            $classes = $base . 'text-navy-600 bg-transparent hover:bg-navy-50 border border-navy-200';
            break;
        default:
            $classes = $base . 'text-white bg-gray-500 hover:bg-gray-600 focus:ring-gray-300';
    }
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
