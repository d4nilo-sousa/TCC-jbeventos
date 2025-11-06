@props(['disabled' => false, 'type' => 'text'])

<input
    {{ $disabled ? 'disabled' : '' }}
    autocorrect="off"
    autocapitalize="off"
    spellcheck="false"
    type="{{ $type }}"
    autocomplete="{{ $attributes->get('autocomplete') ?? 'off' }}"
    {!! $attributes->merge(['class' => 'border-gray-200 focus:border-gray-400 focus:ring-white rounded-xl shadow-sm']) !!}
>
