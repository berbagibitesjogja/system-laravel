@props(['variant' => 'primary'])

@php
    $classes = 'px-4 py-1 text-white rounded-md shadow-md transition duration-300 ';
    switch ($variant) {
        case 'primary':
            $classes .= 'bg-navy hover:bg-navy-700'; // Assuming navy is primary
            break;
        case 'warning':
            $classes .= 'bg-orange-500 hover:bg-orange-700';
            break;
        case 'success':
            $classes .= 'bg-lime-500 hover:bg-lime-700';
            break;
        case 'danger':
            $classes .= 'bg-red-500 hover:bg-red-700';
            break;
        case 'info':
            $classes .= 'bg-blue-500 hover:bg-blue-700';
            break;
        case 'navy':
             $classes .= 'bg-navy hover:bg-navy-600';
             break;
        case 'tosca':
             $classes .= 'bg-tosca-300 hover:bg-tosca-600 p-2';
             break;
        case 'red-action':
             $classes .= 'bg-red-300 hover:bg-red-600 p-2';
             break;
        case 'yellow':
             $classes .= 'bg-yellow-300 hover:bg-yellow-600 p-2';
             break;
        default:
            $classes .= 'bg-gray-500 hover:bg-gray-700';
    }
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
