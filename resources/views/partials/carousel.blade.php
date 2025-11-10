@php
    // Ajusta los nombres exactamente como están en /public/img/carrusel
    $files = ['migrana1.jpg', 'migrana2.jpg', 'migrana3.jpg'];

    // Construimos URLs seguras (codificando espacios, acentos, etc.)
    $images = array_map(function ($f) {
        return asset('img/carrusel/' . rawurlencode($f));
    }, $files);
@endphp


<div x-data="carousel({ images: {{ json_encode($images) }} })" x-init="init()" class="relative mt-10 lg:mt-0 animate-fadeIn">
    {{-- Halo decorativo --}}
    <div
        class="pointer-events-none absolute -inset-7 rounded-[2.3rem] bg-gradient-to-tr from-accent-500/30 to-alert-400/15 blur-2xl">
    </div>

    {{-- Contenedor principal --}}
    <div class="relative glass rounded-[2.3rem] overflow-hidden border border-white/10 shadow-2xl h-[26rem]">

        {{-- Imágenes dinámicas --}}
        <template x-for="(src, index) in images" :key="index">
            <img :src="src" :alt="`Slide ${index+1}`"
                class="absolute inset-0 w-full h-full object-cover rounded-[2rem] transition-all duration-700 ease-in-out"
                :class="{
                    'opacity-100 scale-100 z-10': currentIndex === index,
                    'opacity-0 scale-105 z-0': currentIndex !== index
                }">
        </template>

        {{-- Botón anterior --}}
        <button @click="prev"
            class="absolute top-1/2 left-4 -translate-y-1/2 bg-base-dark/60 hover:bg-base-dark/80 text-alert-400 border border-alert-400/40 rounded-full p-3 transition-all shadow-md hover:scale-110 focus:outline-none focus:ring-2 focus:ring-alert-400/50"
            aria-label="Anterior">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        {{-- Botón siguiente --}}
        <button @click="next"
            class="absolute top-1/2 right-4 -translate-y-1/2 bg-base-dark/60 hover:bg-base-dark/80 text-alert-400 border border-alert-400/40 rounded-full p-3 transition-all shadow-md hover:scale-110 focus:outline-none focus:ring-2 focus:ring-alert-400/50"
            aria-label="Siguiente">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        {{-- Indicadores --}}
        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2.5">
            <template x-for="(_, index) in images" :key="index">
                <button @click="goToSlide(index)"
                    :class="{
                        'bg-alert-400 shadow-glow-sm scale-125': currentIndex === index,
                        'bg-white/30 hover:bg-white/50': currentIndex !== index
                    }"
                    class="w-3 h-3 rounded-full transition-all duration-300 hover:scale-110"
                    :aria-label="'Ir a imagen ' + (index + 1)"></button>
            </template>
        </div>
    </div>
</div>

@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/carousel.js'])
