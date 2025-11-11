<section id="tipos-migrana" class="relative bg-base-dark py-24">
    <div class="section-background-image"
        data-src="{{ asset('img/welcome/neuronas.png') }}"
        data-speed="0.06" data-brightness="0.4" data-opacity="0.18">
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- TITULAR LLAMATIVO AMARILLO (RESTAURADO) --}}
        <div class="text-center mb-16 animate-fadeIn delay-300">
            <h2
                class="text-4xl sm:text-5xl font-extrabold mb-4 bg-clip-text text-YELLOW bg-gradient-to-r from-alert-400 to-accent-500 text-shadow-glow-accent inline-block">
               TIPOS DE MIGRAÑA
            </h2>
            <p class="text-white/70 max-w-3xl mx-auto text-lg leading-relaxed">
                Comprende los diferentes tipos de migraña y cómo afectan tu vida, para un diagnóstico y tratamiento
                más precisos.
            </p>
        </div>

        {{-- CUADRÍCULA DE 6 TARJETAS --}}
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">

            {{-- 1. Migraña sin Aura (Común) --}}
            <div class="glass card-migrana animate-fadeIn delay-500 hover:scale-[1.03] text-center">
                <div class="text-5xl text-center text-alert-400 mb-5 drop-shadow-lg"><i class="fa-solid fa-head-side-virus"></i></div>
                <h3 class="text-xl font-extrabold mb-3 text-center text-alert-400 text-shadow-glow-sm">Migraña sin Aura</h3>
                <ul class="mt-3.5 space-y-2.5 text-white/90 text-base text-left inline-block">
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>El tipo más común (70-80% de casos)</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Dolor pulsátil de moderado a intenso</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Sensibilidad a luz/sonido (fotofobia/fonofobia)</li>
                </ul>
            </div>

            {{-- 2. Migraña con Aura (Visual/Sensitiva) --}}
            <div class="glass card-migrana animate-fadeIn delay-700 hover:scale-[1.03] text-center">
                <div class="text-5xl text-alert-400 mb-5 drop-shadow-lg"><i class="fa-solid fa-cloud-sun"></i></div>
                <h3 class="text-xl font-extrabold mb-3 text-alert-400 text-shadow-glow-sm">Migraña con Aura</h3>
                <ul class="mt-3.5 space-y-2.5 text-white/90 text-base text-left inline-block">
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Síntomas visuales (escotomas, destellos)</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Ocurre antes del dolor (5 a 60 min.)</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Puede incluir dificultad al hablar o entumecimiento</li>
                </ul>
            </div>

            {{-- 3. Migraña Crónica (Impacto Severo) --}}
            <div class="glass card-migrana animate-fadeIn delay-900 hover:scale-[1.03] text-center">
                <div class="text-5xl text-center text-alert-400 mb-5 drop-shadow-lg"><i class="fa-solid fa-hourglass-half"></i></div>
                <h3 class="text-xl font-extrabold mb-3 text-center text-alert-400 text-shadow-glow-sm">Migraña Crónica</h3>
                <ul class="mt-3.5 space-y-2.5 text-white/90 text-base text-left inline-block">
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Más de 15 días/mes con dolor de cabeza</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Gran impacto en la calidad de vida diaria</li>
                    <li><i class="bi bi-check-circle-fill text-center  text-alert-400 mr-2.5"></i>Requiere manejo farmacológico y especializado continuo</li>
                </ul>
            </div>
            
            {{-- 4. Migraña Hemicránea Continua --}}
            <div class="glass card-migrana animate-fadeIn delay-1100 hover:scale-[1.03] text-center">
                <div class="text-5xl text-center text-alert-400 mb-5 drop-shadow-lg"><i class="fa-solid fa-brain"></i></div>
                <h3 class="text-xl font-extrabold mb-3 text-center text-alert-400 text-shadow-glow-sm">Hemicránea Continua</h3>
                <ul class="mt-3.5 space-y-2.5 text-white/90 text-base text-left inline-block">
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Dolor persistente, diario y unilateral</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Fluctuaciones en la intensidad del dolor</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Respuesta dramática a la Indometacina (Diagnóstico clave)</li>
                </ul>
            </div>

            {{-- 5. Migraña Episódica --}}
            <div class="glass card-migrana animate-fadeIn delay-1300 hover:scale-[1.03] text-center">
                <div class="text-5xl text-center text-alert-400 mb-5 drop-shadow-lg"><i class="fa-solid fa-chart-line"></i></div>
                <h3 class="text-xl font-extrabold mb-3 text-center text-alert-400 text-shadow-glow-sm">Migraña Episódica</h3>
                <ul class="mt-3.5 space-y-2.5 text-white/90 text-base text-left inline-block">
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Menos de 15 días de dolor al mes</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Fácil de manejar con medicación aguda</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Riesgo de progresión a migraña crónica</li>
                </ul>
            </div>

            {{-- 6. Migraña por Abuso de Medicación --}}
            <div class="glass card-migrana animate-fadeIn delay-1500 hover:scale-[1.03] text-center">
                <div class="text-5xl text-center text-alert-400 mb-5 drop-shadow-lg"><i class="fa-solid fa-pills"></i></div>
                <h3 class="text-xl font-extrabold mb-3 text-center text-alert-400 text-shadow-glow-sm">Por Abuso de Medicación</h3>
                <ul class="mt-3.5 space-y-2.5 text-white/90 text-base text-left inline-block">
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Resultado del uso excesivo de medicamentos agudos</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Dolor de cabeza diario o casi diario</li>
                    <li><i class="bi bi-check-circle-fill text-center text-alert-400 mr-2.5"></i>Requiere plan de desintoxicación y manejo preventivo</li>
                </ul>
            </div>
        </div>
        
    </div>
</section>