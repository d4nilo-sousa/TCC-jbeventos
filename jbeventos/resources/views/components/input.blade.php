@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-red-600 focus:ring-red-600 rounded-md shadow-sm']) !!}>
