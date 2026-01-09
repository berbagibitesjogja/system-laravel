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

<button 
    {{ $attributes->merge(['class' => $classes]) }}
    x-data="{ loading: false }"
    @click="if($el.type === 'submit' && $el.form && $el.form.checkValidity()) { setTimeout(() => loading = true, 10); }"
    :disabled="loading"
    :class="{ 'opacity-70 cursor-not-allowed': loading }"
>
    <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <span :class="{ 'opacity-0': loading && $el.classList.contains('justify-center') }">
        {{ $slot }}
    </span>
</button>
