@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-3 pt-1 text-base leading-5 text-stone-700 focus:outline-none transition duration-150 ease-in-out'  
    : 'inline-flex items-center px-3 pt-1 text-base leading-5 text-gray-700 hover:text-gray-600 focus:outline-none focus:text-gray-700 transition duration-200 ease-in-out';  
@endphp


<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
