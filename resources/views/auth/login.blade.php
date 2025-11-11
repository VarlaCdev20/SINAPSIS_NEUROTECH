<x-guest-layout>
    <section class="relative min-h-screen flex items-center justify-center bg-base-dark overflow-hidden text-white">
        {{-- Fondo parallax mezclado (neuronas + overlay global) --}}
        <div class="section-background-image"
            data-src="{{ asset('img/welcome/neuronas.png') }}"
            data-speed="0.08" data-brightness="0.6" data-opacity="0.35">
        </div>
        <div class="background-overlay"></div>
        <div class="pointer-events-none absolute -top-40 left-1/2 h-[40rem] w-[40rem] -translate-x-1/2 rounded-full bg-accent-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-10 right-10 h-64 w-64 rounded-full bg-alert-400/40 blur-2xl"></div>

        <div class="relative z-10 w-full max-w-6xl mx-auto px-6 py-20 grid gap-10 lg:grid-cols-2 items-center">

          <div class="glass rounded-2xl p-10 shadow-[0_0_40px_rgba(213,167,213,0.25),0_0_80px_rgba(255,215,0,0.15)] animate-fadeIn">
    <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-gold-primary/20 bg-gold-primary/10 px-3 py-1 text-sm text-primary-gold">
        <span class="h-2 w-2 rounded-full bg-primary-gold"></span> Dra. Patricia Caballero
    </div>

                <h1 class="text-4xl text-center font-extrabold leading-tight text-white">
                    Bienvenido a
                   <span class="block bg-clip-text text-center bg-gradient-to-r from-primary-gold to-light-gold">
                       SINAPSIS
                   </span>
                </h1>

                <p class="mt-4 text-center text-white/80 text-lg max-w-md">
                    Clínica neurológica especializada en migraña.
                    Accede a tu cuenta para gestionar tus citas, revisar tu historial y continuar tu seguimiento médico.
                </p>

                <img src="{{ asset('img/welcome/ICON.png') }}" alt="Logo SINAPSIS"
                    class="mt-10 mx-auto h-28 w-28 object-contain animate-float drop-shadow-lg">

                <div class="mt-8 grid grid-cols-3 gap-4 text-center text-sm">
                    <div class="glass rounded-xl p-4">
                        <div class="text-2xl font-extrabold text-alert-400">24/7</div>
                        <div class="text-white/70">Agenda Online</div>
                    </div>
                    <div class="glass rounded-xl p-4">
                        <div class="text-1xl font-extrabold text-alert-400">EXPERTOS EN EL AREA</div>
                        <div class="text-white/70">Datos Seguros</div>
                    </div>
                    <div class="glass rounded-xl p-4">
                        <div class="text-2xl font-extrabold text-alert-400">+99%</div>
                        <div class="text-white/70">Disponibilidad</div>
                    </div>
                </div>
            </div>

            <div class="glass rounded-2xl p-10 shadow-[0_0_40px_rgba(213,167,213,0.25),0_0_80px_rgba(255,215,0,0.15)] animate-fadeIn delay-300">
                <h2 class="text-3xl text-center font-bold mb-2 text-primary-gold">Iniciar Sesión</h2> {{-- Título dorado --}}
                <p class="text-white/70 text-center text-sm mb-6">Accede con tu correo y contraseña registrados.</p>

                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-red-400/30 bg-red-400/10 px-4 py-3 text-red-200">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-white/90">Correo</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                            class="form-input-sinapsis"> {{-- Clase personalizada para inputs --}}
                    </div>

                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-white/90">Contraseña</label>
                        <input id="password" name="password" type="password" required
                            class="form-input-sinapsis pr-12"> {{-- Clase personalizada para inputs --}}
                        <button type="button" id="togglePassword"
                            class="absolute right-3 top-9 text-white/50 hover:text-white" aria-label="Mostrar/ocultar contraseña">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-between text-sm text-white/80">
                        <label class="inline-flex items-center gap-2">
                            <input id="remember_me" name="remember" type="checkbox"
                                class="rounded border-white/20 bg-white/10 text-primary-gold focus:ring-primary-gold"> {{-- Checkbox dorado --}}
                            <span>Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="hover:underline text-primary-gold hover:text-light-gold" href="{{ route('password.request') }}"> {{-- Enlace dorado --}}
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-primary-sinapsis w-full"> {{-- Clase unificada para botón --}}
                        <i class="bi bi-box-arrow-in-right mr-1"></i> Entrar
                    </button>
                </form>

                <div class="mt-8 text-center text-sm text-white/70">
                    ¿Aún no tienes cuenta?
                    <a href="{{ route('register') }}" class="text-primary-gold hover:text-light-gold font-semibold">Regístrate aquí</a> {{-- Enlace dorado --}}
                </div>
            </div>
        </div>
    </section>

    @vite(['resources/css/login.css', 'resources/js/login.js', 'resources/js/parallax.js'])
</x-guest-layout>