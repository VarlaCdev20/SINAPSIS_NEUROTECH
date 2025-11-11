    <section id="contacto" class="relative bg-base-dark py-20">
        <div class="section-background-image"
            data-src="{{ asset('img/welcome/neuronas.png') }}"
            data-speed="0.07" data-brightness="0.5" data-opacity="0.22"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid gap-14 lg:grid-cols-2">
                <div class="animate-fadeIn delay-300">
                    <h2
                        class="text-3xl sm:text-4xl font-extrabold bg-clip-text text-center bg-gradient-to-r from-alert-400 to-accent-500 text-shadow-glow-alert">
                        Agenda tu Consulta
                    </h2>
                    <p class="mt-3.5 text-white/80 max-w-xl text-lg text-center leading-relaxed">
                        Completa nuestro formulario seguro para agendar tu cita. Nuestro equipo médico se pondrá en
                        contacto para confirmar detalles y prepararte para tu visita.
                    </p>
                    <div class="mt-7 glass rounded-3xl p-7 shadow-xl border border-white/10">
                        <h3 class="text-xl font-semibold mb-4 text-alert-400 text-shadow-glow-sm"><i
                                class="bi bi-geo-alt-fill mr-2.5"></i> Ubicación</h3>
                        <div class="relative w-full h-72 rounded-2xl overflow-hidden shadow-lg border border-white/10">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3825.4138512979716!2d-68.1179073!3d-16.5051916!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x915f21e0cf8298ef%3A0x385a3cb0998652ce!2sBIENESTAR%20AUDITIVO!5e0!3m2!1ses-419!2sbo!4v1762365339011!5m2!1ses-419!2sbo"
                                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                            <div
                                class="absolute inset-0 bg-base-dark/50 flex items-center justify-center text-white/70 text-base font-semibold pointer-events-none">
                                <span class="animate-pulse">Cargando Mapa...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <form
                    class="glass rounded-3xl p-9 space-y-7 shadow-glow-sm border border-white/15 animate-fadeIn delay-500"
                    action="{{ route('contacto.store') }}" method="POST" id="formContacto">
                    @if ($errors->any())
                        <div style="color: yellow; margin-bottom: 1rem;">
                            @foreach ($errors->all() as $error)
                                <div>• {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @csrf

                    <div class="grid gap-5 sm:grid-cols-2">

                        <input placeholder="Nombre" type="text" id="nombre" name="nombre"
                            class="w-full rounded-xl border border-white/20 bg-white/5 px-5 py-3 outline-none placeholder-white/60 text-base focus:ring-2 focus:ring-alert-400 transition-colors"
                            required>

                        <input placeholder="Apellido Paterno" type="text" id="ap_paterno" name="ap_paterno"
                            class="w-full rounded-xl border border-white/20 bg-white/5 px-5 py-3 outline-none placeholder-white/60 text-base focus:ring-2 focus:ring-alert-400 transition-colors"
                            required>

                        <input placeholder="Apellido Materno" type="text" id="ap_materno" name="ap_materno"
                            class="w-full rounded-xl border border-white/20 bg-white/5 px-5 py-3 outline-none placeholder-white/60 text-base focus:ring-2 focus:ring-alert-400 transition-colors"
                            required>

                        <input placeholder="Fecha de Nacimiento" type="date" id="fecha_nacimiento"
                            name="fecha_nacimiento"
                            class="w-full rounded-xl border border-white/20 bg-white/5 px-5 py-3 outline-none text-white/80 text-base focus:ring-2 focus:ring-alert-400 transition-colors [&::-webkit-calendar-picker-indicator]:invert-[80%] [&::-webkit-calendar-picker-indicator]:hover:invert-[100%]"
                            required>

                        <input placeholder="Dirección" type="text" id="direccion" name="direccion"
                            class="w-full rounded-xl border border-white/20 bg-white/5 px-5 py-3 outline-none placeholder-white/60 text-base focus:ring-2 focus:ring-alert-400 transition-colors"
                            required>

                        <input placeholder="Cédula de Identidad" type="text" id="ci" name="ci"
                            class="w-full rounded-xl border border-white/20 bg-white/5 px-5 py-3 outline-none placeholder-white/60 text-base focus:ring-2 focus:ring-alert-400 transition-colors"
                            required>

                        <input placeholder="Celular" type="text" id="celular" name="celular"
                            class="w-full rounded-xl border border-white/20 bg-white/5 px-5 py-3 outline-none placeholder-white/60 text-base focus:ring-2 focus:ring-alert-400 transition-colors"
                            required>

                        <input placeholder="Correo Electrónico" type="email" id="correo" name="correo"
                            class="w-full rounded-xl border border-white/20 bg-white/5 px-5 py-3 outline-none placeholder-white/60 text-base focus:ring-2 focus:ring-alert-400 transition-colors"
                            required>

                    </div>

                    <textarea rows="4" id="descripcion" name="descripcion"
                        class="w-full rounded-xl border border-white/20 bg-white/5 px-5 py-3 outline-none placeholder-white/60 text-base focus:ring-2 focus:ring-alert-400 transition-colors"
                        placeholder="Comparte brevemente tu motivo de consulta o síntomas (opcional, pero ayuda a prepararnos)"></textarea>

                    <button type="submit"
                        class="w-full rounded-xl bg-alert-400 px-6 py-3.5 font-extrabold text-xl text-base-dark hover:bg-alert-500 shadow-glow transition-all duration-300 transform hover:scale-105 animate-pulseGlow">
                        Enviar Solicitud de Cita
                    </button>
                </form>
                <style>
                    /* Estilo para errores de validación */
                    /* Se mantiene aquí por ser dependiente del HTML y no usar clases de Tailwind para los errores */
                    .input-error {
                        border-color: #FFEB3B !important;
                        box-shadow: 0 0 0 3px rgba(255, 235, 59, 0.2) !important;
                    }

                    .error-message {
                        color: #FFEB3B;
                        font-size: 0.85rem;
                        margin-top: 0.3rem;
                        margin-bottom: -0.3rem;
                        font-weight: 500;
                        animation: fadeIn 0.3s ease-out;
                    }
                </style>
            </div>
        </div>
    </section>
