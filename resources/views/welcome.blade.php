<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>SINAPSIS - Clínica Neurológica</title>
    <meta name="description" content="Plataforma para gestión de pacientes, citas y seguimiento neurológico." />

    {{-- Fuentes y librerías externas --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Tailwind & Alpine (solo si aún no compilas con Vite) --}}

    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    {{-- Estilos personalizados con Vite --}}
    @vite(['resources/css/app.css', 'resources/css/layout.css', 'resources/css/welcome.css', 'resources/css/carousel.css', 'resources/css/forms.css', 'resources/css/parallax.css'])
</head>

<body
    class="font-sans text-white antialiased selection:bg-alert-400 selection:text-base-dark bg-base-dark overflow-x-hidden scrollbar-style"
    x-data="{ isMenuOpen: false }"> 

    {{-- ===========================================================
        🌟 HEADER
    ============================================================ --}}
    <header
        class="fixed inset-x-0 top-0 z-50 backdrop-blur-xl bg-base-dark/80 shadow-2xl border-b border-white/10 h-20 md:h-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">

            {{-- Logo --}}
            <a href="#inicio" class="flex items-center gap-3 group">
                <img src="{{ asset('img/welcome/ICON.png') }}" alt="Logo SINAPSIS" class="h-10 w-10 object-contain">
                <span 
                    class="text-2xl font-extrabold tracking-tight text-white group-hover:text-alert-400 transition-colors duration-300">
                    SINAPSIS
                </span>
            </a>

            {{-- Menú principal --}}
            <nav class="hidden md:flex items-center gap-9 text-lg font-medium text-white/85">
                <a href="#caracteristicas"
                    class="hover:text-alert-400 transition duration-300 hover:shadow-text-glow-alert">
                    <i class="bi bi-info-circle-fill mr-1"></i> ¿Quiénes Somos?
                </a>
                <a href="#tipos-migrana"
                    class="hover:text-alert-400 transition duration-300 hover:shadow-text-glow-alert">
                    <i class="bi bi-boxes mr-1"></i> Tipos de Migraña
                </a>
                <a href="#contacto" class="hover:text-alert-400 transition duration-300 hover:shadow-text-glow-alert">
                    <i class="bi bi-envelope-fill mr-1"></i> Contacto
                </a>
            </nav>

            {{-- Botón de sesión --}}
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('login') }}"
                    class="rounded-xl bg-alert-400 px-6 py-2.5 text-lg font-semibold text-base-dark shadow-glow-sm hover:bg-alert-500 transition-all duration-300 animate-pulseGlow">
                    <i class="bi bi-box-arrow-in-right mr-1"></i> Iniciar Sesión
                </a>
            </div>

            {{-- Menú móvil --}}
            <button @click="isMenuOpen = !isMenuOpen"
                class="md:hidden rounded-lg border border-white/20 p-2.5 text-white/90 hover:text-alert-400 transition duration-300"
                aria-label="Abrir menú">
                <svg x-show="!isMenuOpen" width="30" height="30" fill="none" stroke="currentColor"
                    class="stroke-alert-400">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke-width="2" stroke-linecap="round" />
                </svg>
                <svg x-show="isMenuOpen" x-cloak width="30" height="30" fill="none" stroke="currentColor"
                    class="stroke-alert-400">
                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        {{-- Menú desplegable móvil --}}
        <div x-show="isMenuOpen" x-cloak x-transition
            class="md:hidden border-t border-white/10 bg-base-dark/95 backdrop-blur-xl">
            <nav class="mx-auto max-w-7xl px-6 py-5 flex flex-col gap-4 text-white text-base">
                <a @click="isMenuOpen = false" href="#caracteristicas" class="menu-mobile-item">
                    <i class="bi bi-info-circle-fill mr-2 text-alert-400"></i> ¿Quiénes Somos?
                </a>
                <a @click="isMenuOpen = false" href="#tipos-migrana" class="menu-mobile-item">
                    <i class="bi bi-boxes mr-2 text-alert-400"></i> Tipos de Migraña
                </a>
                <a @click="isMenuOpen = false" href="#contacto" class="menu-mobile-item">
                    <i class="bi bi-envelope-fill mr-2 text-alert-400"></i> Contacto
                </a>

                <div class="mt-5 flex flex-col gap-3">
                    <a @click="isMenuOpen = false" href="{{ route('login') }}"
                        class="rounded-xl px-4 py-3 text-center font-semibold bg-alert-400 hover:bg-alert-500 shadow-glow text-base-dark transition-all duration-300 animate-pulseGlow">
                        <i class="bi bi-box-arrow-in-right mr-1"></i> Iniciar sesión
                    </a>
                </div>
            </nav>
        </div>
    </header>

    {{-- ===========================================================
         🧠 SECCIÓN PRINCIPAL - HERO PARALLAX
    ============================================================ --}}
    <section id="inicio" class="relative pt-24 overflow-hidden min-h-screen" x-data="parallaxInit()">
        <div class="section-background-image" data-src="{{ asset('img/WELCOME/fondo%201.png') }}" data-speed="0.08"
            data-brightness="0.6" data-opacity="0.25">
        </div>

        <div class="relative mx-auto max-w-6xl pt-6 pb-14 px-4 sm:px-6 lg:px-8 z-20 parallax-content">
            <div class="grid items-center gap-10 lg:grid-cols-2">

                {{-- Texto principal --}}
                <div class="text-center lg:text-left animate-fadeIn">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-alert-400/20 bg-alert-400/8 px-3.5 py-1 text-sm font-semibold text-alert-400 animate-pulseGlow">
                        <span class="h-2 w-2 rounded-full bg-alert-400"></span> Doctora: Patricia Caballero
                    </div>

                    <h1 class="mt-5 text-4xl font-extrabold leading-tight sm:text-5xl drop-shadow-lg">
                        SINAPSIS
                        <span
                            class="block mt-2 text-transparent bg-clip-text bg-gradient-to-r from-alert-400 to-accent-500 text-shadow-glow-accent">
                            Clínica Neurológica
                        </span>
                        <span
                            class="block mt-1.5 text-2xl sm:text-3xl font-bold text-white/80 text-shadow-lg animate-float delay-1000">
                            Especializada en Migraña
                        </span>
                    </h1>

                    <p class="mt-6 text-white/85 max-w-lg mx-auto lg:mx-0 text-lg leading-relaxed">
                        Manejo integral de pacientes, citas, historia clínica y seguimiento a la migraña.
                    </p>

                    <div class="mt-9 flex flex-wrap gap-4 justify-center lg:justify-start">
                        <a href="#contacto" class="btn-gold px-7 py-3 text-lg font-extrabold animate-pulseGlow">
                            <i class="bi bi-calendar-check-fill mr-2"></i> Agendar cita
                        </a>
                        <a href="#tipos-migrana" class="btn-gold-outline px-7 py-3 text-lg font-semibold">
                            Ver Tipos de Migraña
                        </a>
                    </div>
                </div>

                {{-- Carrusel --}}

                @include('partials.carousel')


            </div>

        </div>
    </section>

    {{-- ===========================================================
         🧩 SECCIONES SIGUIENTES
    ============================================================ --}}
    @include('partials.quienes-somos')
    @include('partials.tipos-migrana')
    @include('partials.contacto')

    {{-- ===========================================================
         ⚫ FOOTER
    ============================================================ --}}
    <footer class="border-t border-white/10 bg-base-dark">
        <div
            class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 flex flex-col items-center justify-between gap-5 sm:flex-row">
            <p class="text-white/60 text-sm">© {{ date('Y') }} SINAPSIS. Todos los derechos reservados.</p>
            <div class="flex gap-4 text-white/70 text-base">
                <a href="#" class="hover:text-alert-400 transition duration-200"><i class="bi bi-facebook"></i>
                    Facebook</a>
                <a href="#" class="hover:text-alert-400 transition duration-200"><i
                        class="bi bi-instagram"></i> Instagram</a>
                <a href="#" class="hover:text-alert-400 transition duration-200"><i class="bi bi-linkedin"></i>
                    LinkedIn</a>
            </div>
        </div>
    </footer>

    {{-- Scripts JS (modulares con Vite) --}}
    @vite(['resources/js/app.js', 'resources/js/carousel.js', 'resources/js/parallax.js', 'resources/js/forms.js', 'resources/js/utils.js'])
</body>

</html>
