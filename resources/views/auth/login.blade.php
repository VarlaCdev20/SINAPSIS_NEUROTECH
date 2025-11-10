<x-guest-layout>
    {{-- Si tu layout principal no incluye Bootstrap Icons, agrega en la plantilla base: --}}

    <style>
        .hero-gradient {
            background:
                radial-gradient(1200px 600px at 80% -10%, rgba(124, 58, 237, .35), transparent 60%),
                radial-gradient(900px 500px at 0% 0%, rgba(99, 102, 241, .30), transparent 60%),
                linear-gradient(180deg, #0b0720 0%, #120a2e 60%, #1a1440 100%);
        }

        .glass {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        @keyframes pulseDot {

            0%,
            100% {
                opacity: .35;
                transform: scale(.9)
            }

            50% {
                opacity: .9;
                transform: scale(1.1)
            }
        }
    </style>

    <section class="relative min-h-screen hero-gradient text-white">
        <!-- Canvas decorativo -->
        <canvas id="neuralCanvas" class="absolute inset-0 w-full h-full"></canvas>
        <div class="pointer-events-none absolute -top-24 left-1/2 h-[42rem] w-[42rem] -translate-x-1/2 rounded-full bg-purple-600/20 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-10 right-10 h-64 w-64 rounded-full bg-indigo-600/60 blur-2xl"></div>

        <!-- Header compacto -->
        <header class="fixed inset-x-0 top-0 z-50 backdrop-blur-lg bg-[#0e0a26]/70 border-b border-white/10">
            <div class="mx-auto max-w-6xl px-6 h-16 flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 shadow-md overflow-hidden">
                        <img src="{{ asset('img/welcome/ICON.png') }}" alt="Logo SINAPSIS" class="h-8 w-8 object-contain">
                    </span>
                    <span class="text-lg font-extrabold tracking-tight">SINAPSIS</span>
                </a>
            </div>
        </header>

        <!-- Contenido -->
        <div class="relative mx-auto max-w-5xl px-6 pb-16 pt-28">
            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Card lateral (branding) -->
                <div class="relative glass rounded-[1.75rem] p-8 shadow-[0_0_40px_rgba(139,92,246,0.35),0_0_80px_rgba(99,102,241,0.25)]">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1 text-xs text-white/80">
                        <span class="h-2 w-2 rounded-full bg-purple-500 animate-pulse"></span> Dr(a). Patricia Caballero
                    </div>
                    <h1 class="text-3xl font-extrabold leading-tight">
                        Bienvenido a <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-400">SINAPSIS</span>
                    </h1>
                    <p class="mt-3 text-white/80">
                        Siempre al pendiente de tu salud mental. Accede a tu cuenta para gestionar tus citas y mantener tu bienestar.
                        <br>Juntos, construimos un espacio seguro y confiable para tu cuidado.
                    </p>

                    <!-- Ilustración -->
                    <div class="mt-8">
                        <svg viewBox="0 0 600 360" class="mx-auto h-64 w-full">
                            <defs>
                                <radialGradient id="glogin" cx="50%" cy="50%">
                                    <stop offset="0%" stop-color="#a78bfa" />
                                    <stop offset="100%" stop-color="#6366f1" stop-opacity="0" />
                                </radialGradient>
                            </defs>
                            <ellipse cx="300" cy="180" rx="230" ry="140" fill="url(#glogin)" opacity=".35" />
                            <path d="M120,180c0-70,70-120,160-120s160,50,160,120-70,120-160,120-160-50-160-120Z"
                                stroke="#c7d2fe" stroke-width="2.2" fill="none" opacity=".8" />
                            <g fill="#ffffff">
                                <circle cx="210" cy="150" r="4" style="animation:pulseDot 3s infinite .2s" />
                                <circle cx="260" cy="120" r="4" style="animation:pulseDot 3s infinite .6s" />
                                <circle cx="310" cy="160" r="4" style="animation:pulseDot 3s infinite .9s" />
                                <circle cx="360" cy="200" r="4" style="animation:pulseDot 3s infinite .3s" />
                                <circle cx="260" cy="215" r="4" style="animation:pulseDot 3s infinite 1.1s" />
                            </g>
                            <g stroke="#a5b4fc" stroke-width="1.6" opacity=".9">
                                <line x1="210" y1="150" x2="260" y2="120" />
                                <line x1="260" y1="120" x2="310" y2="160" />
                                <line x1="310" y1="160" x2="360" y2="200" />
                                <line x1="310" y1="160" x2="260" y2="215" />
                            </g>
                        </svg>
                    </div>

                    <div class="mt-6 grid grid-cols-3 gap-4 text-center text-sm">
                        <div class="glass rounded-xl p-4">
                            <div class="text-2xl font-extrabold">24/7</div>
                            <div class="text-white/70">Agenda online</div>
                        </div>
                        <div class="glass rounded-xl p-4">
                            <div class="text-2xl font-extrabold">HIPAA</div>
                            <div class="text-white/70">Datos seguros</div>
                        </div>
                        <div class="glass rounded-xl p-4">
                            <div class="text-2xl font-extrabold">+99%</div>
                            <div class="text-white/70">Uptime</div>
                        </div>
                    </div>
                </div>

                <!-- Formulario -->
                <div class="relative glass rounded-[1.75rem] p-8 shadow-[0_0_40px_rgba(139,92,246,0.35),0_0_80px_rgba(99,102,241,0.25)]">
                    <h2 class="text-2xl font-semibold">Iniciar sesión</h2>
                    <p class="text-sm text-white/70 mt-1">Accede con tu cuenta institucional.</p>

                    {{-- Estado de sesión --}}
                    @if (session('status'))
                    <div class="mt-4 rounded-lg border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-200">
                        {{ session('status') }}
                    </div>
                    @endif

                    {{-- Errores --}}
                    @if ($errors->any())
                    <div class="mt-4 rounded-lg border border-red-400/30 bg-red-400/10 px-4 py-3 text-red-200">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-white/90">Correo</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                autocomplete="username"
                                class="mt-1 w-full rounded-xl border border-white/15 bg-white/5 px-4 py-3 text-white placeholder-white/50 outline-none focus:ring-2 focus:ring-purple-500" />
                        </div>

                        <div class="relative">
                            <label for="password" class="block text-sm font-medium text-white/90">Contraseña</label>
                            <input id="password" name="password" type="password" required autocomplete="current-password"
                                class="mt-1 w-full rounded-xl border border-white/15 bg-white/5 px-4 py-3 pr-12 text-white placeholder-white/50 outline-none focus:ring-2 focus:ring-purple-500" />
                            <button type="button" id="togglePassword"
                                class="absolute right-3 top-9 text-white/50 hover:text-white" aria-label="Mostrar/ocultar contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>

                        <div class="flex items-center justify-between text-sm text-white/80">
                            <label class="inline-flex items-center gap-2">
                                <input id="remember_me" name="remember" type="checkbox"
                                    class="rounded border-white/20 bg-white/10 text-purple-500 focus:ring-purple-500">
                                <span>Recordarme</span>
                            </label>

                            @if (Route::has('password.request'))
                            <a class="hover:underline hover:text-white" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                            @endif
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-purple-600 px-5 py-3 font-semibold shadow-[0_0_30px_rgba(139,92,246,0.35)] hover:bg-purple-700 transition">
                            <i class="bi bi-box-arrow-in-right mr-1"></i> Entrar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Canvas “neuronas” -->
    <script>
        const c = document.getElementById('neuralCanvas');
        const ctx = c.getContext('2d', {
            alpha: true
        });
        let nodes = [],
            W, H, mx = -9999,
            my = -9999;

        function resize() {
            W = c.width = c.offsetWidth;
            H = c.height = c.offsetHeight;
            const count = Math.floor((W * H) / 20000);
            nodes = Array.from({
                length: count
            }).map(() => ({
                x: Math.random() * W,
                y: Math.random() * H,
                vx: (Math.random() - .5) * .35,
                vy: (Math.random() - .5) * .35,
                r: 1.2 + Math.random() * 1.6
            }));
        }
        window.addEventListener('resize', resize, {
            passive: true
        });
        resize();
        c.addEventListener('mousemove', e => {
            const r = c.getBoundingClientRect();
            mx = e.clientX - r.left;
            my = e.clientY - r.top;
        });

        function step() {
            ctx.clearRect(0, 0, W, H);
            for (let i = 0; i < nodes.length; i++) {
                const a = nodes[i];
                a.x += a.vx;
                a.y += a.vy;
                if (a.x < 0 || a.x > W) a.vx *= -1;
                if (a.y < 0 || a.y > H) a.vy *= -1;

                const dx = a.x - mx,
                    dy = a.y - my,
                    d2 = dx * dx + dy * dy;
                if (d2 < 120 * 120) {
                    const inv = 1 / Math.sqrt(d2 + 0.1);
                    a.vx += (dx * inv) * -0.02;
                    a.vy += (dy * inv) * -0.02;
                }

                for (let j = i + 1; j < nodes.length; j++) {
                    const b = nodes[j],
                        x = a.x - b.x,
                        y = a.y - b.y,
                        dist = Math.hypot(x, y);
                    if (dist < 120) {
                        const o = 1 - (dist / 120);
                        ctx.strokeStyle = `rgba(167,139,250,${o*0.35})`;
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(a.x, a.y);
                        ctx.lineTo(b.x, b.y);
                        ctx.stroke();
                    }
                }
            }
            for (const p of nodes) {
                const g = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r * 3);
                g.addColorStop(0, 'rgba(255,255,255,.95)');
                g.addColorStop(1, 'rgba(167,139,250,.15)');
                ctx.fillStyle = g;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fill();
            }
            requestAnimationFrame(step);
        }
        step();

        // Mostrar/ocultar contraseña
        const tp = document.getElementById('togglePassword');
        const pw = document.getElementById('password');
        tp?.addEventListener('click', () => {
            const isPass = pw.type === 'password';
            pw.type = isPass ? 'text' : 'password';
            tp.innerHTML = isPass ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    </script>
</x-guest-layout>