<section id="caracteristicas" class="relative bg-base-dark text-white overflow-hidden py-24">
    {{-- Fondo SUTIL: Asegúrate de tener la clase .section-background-image y las imágenes en tus assets --}}
    <div class="section-background-image opacity-5"
        data-src="{{ asset('img/welcome/neuronas.png') }}"
        data-speed="0.1" data-brightness="0.4" data-opacity="0.05">
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-fadeIn delay-300">
            {{-- TITULAR NOTORIO --}}
            <h2
                class="text-4xl sm:text-5xl font-extrabold mb-4 bg-clip-text bg-gradient-to-r from-alert-400 to-accent-500 text-shadow-glow-accent inline-block">
                ¿Quiénes Somos?
            </h2>
            <p class="text-white/70 max-w-3xl mx-auto text-lg leading-relaxed">
                Somos un equipo de vanguardia, fusionando la neurociencia con la tecnología más avanzada para ofrecer
                soluciones de salud innovadoras y centradas en el paciente.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-16 items-start">
            
            {{-- GRÁFICO ABSTRACTO Y CONTEXTUAL --}}
            <div class="relative max-w-lg mx-auto lg:max-w-full animate-float delay-500">
                <div
                    class="absolute -inset-2 rounded-3xl bg-gradient-to-r from-accent-500/20 to-alert-400/10 blur-xl transition-all duration-500 hover:scale-[1.03] animate-pulseGlow">
                </div>
                <img src="{{ asset('img\welcome\DOCTORAS.jpeg') }}"
                    alt="DOCTORA CARLA ENCINAS Y DARLING TICONA"
                    class="relative rounded-3xl shadow-2xl transition-transform duration-500 transform hover:scale-[1.02] border-2 border-white/10" />
            </div>

            {{-- CARACTERÍSTICAS AGRUPADAS EN TARJETAS (AMARILLO UNIFORME) --}}
            <div class="space-y-10 animate-fadeIn delay-700">
                
                {{-- Característica 1: Innovación --}}
                <div class="feature-card">
                    <div class="flex items-start gap-5">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-xl bg-alert-400/15 text-alert-400 text-4xl flex-shrink-0 shadow-glow-sm">
                            <i class="bi bi-lightbulb-fill"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-2xl mb-1 text-alert-400 text-shadow-glow-sm">Innovación en cada paso</h3>
                            <p class="text-white/80 text-base">
                                Utilizamos tecnología de punta, como IA y análisis de datos avanzados, para mejorar
                                drásticamente la eficiencia clínica y administrativa.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Característica 2: Compromiso Humano --}}
                <div class="feature-card">
                    <div class="flex items-start gap-5">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-xl bg-alert-400/15 text-alert-400 text-4xl flex-shrink-0 shadow-glow-sm">
                            <i class="bi bi-person-heart"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-2xl mb-1 text-alert-400 text-shadow-glow-sm">Compromiso humano</h3>
                            <p class="text-white/80 text-base">
                                Cada solución está diseñada pensando en el bienestar del paciente y en potenciar la
                                invaluable labor de nuestros profesionales médicos.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Característica 3: Seguridad --}}
                <div class="feature-card">
                    <div class="flex items-start gap-5">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-xl bg-alert-400/15 text-alert-400 text-4xl flex-shrink-0 shadow-glow-sm">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-2xl mb-1 text-alert-400 text-shadow-glow-sm">Seguridad y confianza</h3>
                            <p class="text-white/80 text-base">
                                Garantizamos la máxima protección de la información médica con protocolos de seguridad
                                de nivel militar y cumplimiento total de normativas globales.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>