@props([
    'id' => '',
    'name' => '',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
])

<input
    id="{{ $id }}"
    name="{{ $name }}"
    type="{{ $type }}"
    placeholder="{{ $placeholder }}"
    @if($required) required @endif
    {{ $attributes->merge(['class' => 'w-full rounded-xl border border-white/20 bg-white/5 px-4.5 py-3 outline-none
        placeholder-white/60 text-base text-white/90 focus:ring-2 focus:ring-alert-400 transition-colors duration-300
        hover:bg-white/10 focus:bg-white/10 shadow-sm']) }}
/>
