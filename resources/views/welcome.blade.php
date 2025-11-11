<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>SINAPSIS: Especializada en Migraña</title>
    <meta name="description" content="Manejo integral de pacientes, citas, historia clínica y seguimiento a la migraña con la Dra. Patricia Caballero." />

    {{-- Fuentes y librerías externas --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Estilos personalizados con Vite --}}
    @vite(['resources/css/app.css', 'resources/css/layout.css', 'resources/css/welcome.css', 'resources/css/carousel.css', 'resources/css/forms.css', 'resources/css/parallax.css'])
</head>

<body
    class="font-sans text-white antialiased selection:bg-alert-400 selection:text-base-dark bg-base-dark overflow-x-hidden scrollbar-style"
    x-data="{ isMenuOpen: false }">

    <div class="background-overlay"></div>

    {{-- ===========================================================
        HEADER (OPTIMIZADO)
    ============================================================ --}}
    <header
        class="fixed inset-x-0 top-0 z-50 backdrop-blur-xl bg-base-dark/80 shadow-2xl border-b border-white/10 h-20 md:h-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">

            {{-- **LOGOTIPO: SINAPSIS en amarillo** --}}
            <a href="#inicio" class="flex items-center gap-3 group">
                <img src="{{ asset('img\welcome\ICON.png') }}" alt="Logo SINAPSIS Clínica de Migraña" class="h-20 w-20 object-contain">
                <div class="flex flex-col leading-none">
                    <span 
                        {{-- SINAPSIS en amarillo en el header para destacar --}}
                        class="text-2xl font-extrabold tracking-tight text-alert-400 group-hover:text-accent-500 transition-colors duration-300">
                        SINAPSIS
                    </span>
                    <span class="text-xs font-medium tracking-wide text-white/70">
                        Clínica de Migraña
                    </span>
                </div>
            </a>

            {{-- Menú principal (Sin cambios estructurales) --}}
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
                    class="rounded-xl bg-alert-400 px-6 py-2.5 text-lg font-semibold text-base-dark shadow-glow-sm hover:bg-alert-500 transition-all duration-300">
                    <i class="bi bi-box-arrow-in-right mr-1"></i> Iniciar Sesión
                </a>
            </div>

            {{-- Menú móvil (Sin cambios estructurales) --}}
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

        {{-- Menú desplegable móvil (Sin cambios estructurales) --}}
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
                        class="rounded-xl px-4 py-3 text-center font-semibold bg-alert-400 hover:bg-alert-500 shadow-glow text-base-dark transition-all duration-300">
                        <i class="bi bi-box-arrow-in-right mr-1"></i> Iniciar sesión
                    </a>
                </div>
            </nav>
        </div>
    </header>

    {{-- ===========================================================
        🧠 SECCIÓN PRINCIPAL - HERO (CENTRADO Y AMARILLO EN TITULARES)
    ============================================================ --}}
    <section id="inicio" class="relative pt-24 overflow-hidden min-h-screen" x-data="parallaxInit()">
        {{-- Fondo Parallax (Manteniendo la sutileza) --}}
        <div class="section-background-image" data-src="{{ asset('img/welcome/neuronas.png') }}" data-speed="0.08"
            data-brightness="0.6" data-opacity="0.25"></div>

        <div class="relative mx-auto max-w-6xl pt-6 pb-14 px-4 sm:px-6 lg:px-8 z-20 parallax-content">
            <div class="grid items-center gap-10 lg:grid-cols-2">

                {{-- Texto principal (TODO CENTRADO) --}}
                <div class="text-center animate-fadeIn"> 
                    
                    {{-- Doctora Tagline (Ya era amarillo, se mantiene text-alert-400) --}}
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-alert-400/20 bg-alert-400/8 px-3.5 py-1 text-sm font-semibold text-alert-400">
                        <span class="h-2 w-2 rounded-full bg-alert-400"></span> Doctora: Patricia Caballero
                    </div>

                    {{-- Título Principal (H1 - SINAPSIS en amarillo) --}}
                    <h1 class="mt-5 text-4xl font-extrabold leading-tight sm:text-5xl drop-shadow-lg">
                        {{-- CAMBIO CLAVE: SINAPSIS en amarillo puro --}}
                        <span class="text-alert-400 text-shadow-glow-sm">SINAPSIS</span> 
                        
                        <span
                            class="block mt-2 bg-clip-text bg-gradient-to-r from-alert-400 to-accent-500 text-shadow-glow-accent">
                            Clínica Neurológica
                        </span>
                        <span
                            class="block mt-1.5 text-2xl sm:text-3xl font-bold text-white/80 text-shadow-lg animate-float delay-1000">
                            Especializada en Migraña
                        </span>
                    </h1>

                    {{-- NUEVA LISTA DE BENEFICIOS (CENTRADA) --}}
                    <ul class="mt-6 text-white/85 max-w-lg mx-auto lg:mx-0 text-lg leading-relaxed space-y-2 text-center">
                        {{-- Aseguramos que los items se centren con justify-center y los íconos sean amarillos --}}
                        <li class="flex items-center justify-center gap-2">
                            <i class="bi bi-check-circle-fill text-alert-400 text-xl"></i>
                            Manejo integral de pacientes
                        </li>
                        <li class="flex items-center justify-center gap-2">
                            <i class="bi bi-check-circle-fill text-alert-400 text-xl"></i>
                            Citas, historia clínica y seguimiento digital
                        </li>
                        <li class="flex items-center justify-center gap-2">
                            <i class="bi bi-check-circle-fill text-alert-400 text-xl"></i>
                            Enfoque especializado en Migraña
                        </li>
                    </ul>

                    {{-- Botones (Ya estaban centrados con justify-center) --}}
                    <div class="mt-9 flex flex-wrap gap-4 justify-center">
                        {{-- **CTA PRIMARIO: MÁXIMA NOTORIEDAD** --}}
                        <a href="#contacto" class="btn-primary-cta px-7 py-3 text-lg font-extrabold">
                            <i class="bi bi-calendar-check-fill mr-2"></i> Agendar Cita Ahora
                        </a>
                        {{-- **CTA SECUNDARIO: Menos prominente** --}}
                        <a href="#tipos-migrana" class="btn-secondary-outline px-7 py-3 text-lg font-semibold">
                            Ver Tipos de Migraña
                        </a>
                    </div>
                </div>

                {{-- Carrusel (Mantiene la estructura de inclusión de parcial) --}}
                @include('partials.carousel')

            </div>
        </div>
    </section>

            
            {{-- Incluimos el contenido del parcial --}}
            @include('partials.tipos-migrana')

     {{-- Incluimos el contenido del parcial --}}
            @include('partials.quienes-somos')


    {{-- Seccion Contacto --}}
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