@props([
    'type' => 'submit',
    'label' => 'Enviar',
    'icon' => 'bi-send-fill',
])

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'w-full rounded-xl bg-alert-400 px-6 py-3.5 font-extrabold text-xl text-base-dark
        hover:bg-alert-500 shadow-glow transition-all duration-300 transform hover:scale-105 animate-pulseGlow']) }}>
    <i class="bi {{ $icon }} mr-2.5"></i> {{ $label }}
</button>
