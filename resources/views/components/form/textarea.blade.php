@props([
    'id' => '',
    'name' => '',
    'rows' => 4,
    'placeholder' => '',
    'required' => false,
])

<textarea
    id="{{ $id }}"
    name="{{ $name }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    @if($required) required @endif
    {{ $attributes->merge(['class' => 'w-full rounded-xl border border-white/20 bg-white/5 px-4.5 py-3 outline-none
        placeholder-white/60 text-base text-white/90 focus:ring-2 focus:ring-alert-400 transition-colors duration-300
        hover:bg-white/10 focus:bg-white/10 resize-none shadow-sm']) }}>
</textarea>
