@props(['disabled' => false])

<input
    {{ $disabled ? 'disabled' : '' }}
    autocorrect="off"
    autocapitalize="off"
    spellcheck="false"
    type="email"
    autocomplete="email"
    {!! $attributes->merge(['class' => 'border-gray-200 focus:border-gray-400 focus:ring-white rounded-xl shadow-sm']) !!}
>
