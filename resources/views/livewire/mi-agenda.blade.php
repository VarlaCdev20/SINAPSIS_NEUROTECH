<div class="p-6 bg-gradient-to-br from-gray-50 via-violet-50 to-indigo-50 min-h-[90vh]" x-data="{
    showModal: false,
    recomendaciones: [
        '💧 Hidrátate bien, tu cuerpo lo agradecerá.',
        '🧘‍♀️ Dedica 10 minutos al día a relajarte con respiraciones profundas.',
        '🌙 Duerme al menos 7 horas; un buen descanso previene los episodios.',
        '🍎 Mantén una alimentación equilibrada y evita saltarte comidas.',
        '💜 No olvides sonreír: también es parte de tu bienestar.',
        '☕ Evita el exceso de cafeína o bebidas energéticas.',
        '📱 Reduce el brillo de las pantallas y haz pausas visuales cada 20 minutos.',
        '🕯️ Mantén tus espacios tranquilos, evita luces fuertes o parpadeantes.',
        '😴 Establece una rutina fija para dormir y despertar.',
        '🥗 Evita alimentos procesados o con conservantes artificiales.',
        '🌿 Intenta caminar unos minutos al aire libre cada día.',
        '📔 Registra tus síntomas: conocer tus desencadenantes ayuda a prevenir.',
        '💆‍♀️ Un masaje suave en cuello y sienes puede aliviar la tensión.',
        '🎧 Escucha música relajante o sonidos naturales para descansar.',
        '🕰️ No te sobrecargues de tareas, tu cerebro necesita pausas.',
        '🥤 Evita el alcohol y las comidas muy saladas, pueden intensificar el dolor.',
        '🧴 Usa compresas frías en la frente o nuca durante molestias leves.',
        '🌤️ No olvides usar gafas de sol en días muy brillantes.',
        '💻 Ajusta la postura al trabajar, evita tensar el cuello y hombros.',
        '💬 Habla con tu médico si notas nuevos síntomas o cambios en tus episodios.',
        '🧃 Incluye frutas ricas en magnesio como plátano o aguacate.',
        '💆‍♂️ Estiramientos suaves diarios pueden reducir tensión muscular.',
        '🌸 Evita perfumes o aromas intensos si notas que te afectan.',
        '📅 Mantén tus horarios de comida y sueño lo más regulares posible.',
        '🫖 Toma una infusión calmante: manzanilla, menta o jengibre pueden ayudar.',
        '🩵 No ignores señales tempranas: descansa ante los primeros síntomas.',
        '🏞️ Dedica tiempo a actividades que te calmen: dibujar, leer, meditar.',
        '🚫 Evita saltarte tus medicamentos o tratamientos indicados.',
        '📶 Desconéctate un rato del celular y busca silencio para tu mente.',
        '🌬️ Practica respiración profunda para liberar tensión craneal.'
    ],
    consejo: ''
}"
    x-init="consejo = recomendaciones[Math.floor(Math.random() * recomendaciones.length)]">

    <!-- 💜 ENCABEZADO -->
    <div
        class="relative mb-10 overflow-hidden rounded-3xl bg-gradient-to-r from-violet-600 via-fuchsia-600 to-indigo-500 text-white shadow-xl p-8 md:p-12">
        <div class="relative z-10">
            <h2 class="text-4xl font-extrabold mb-3">
                Hola, {{ $paciente->usuario->name ?? (auth()->user()->name ?? 'Paciente') }} 💜
            </h2>
            <p class="text-white/90 text-sm md:text-lg">
                Tu espacio de bienestar — cada semana cuenta 🌿
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-4">
                <button @click="showModal = true"
                    class="bg-white text-violet-600 font-semibold px-5 py-2 rounded-full hover:scale-105 hover:bg-violet-50 transition-all duration-200 shadow-md">
                    <i class="bi bi-plus-circle"></i> Nueva cita
                </button>

                <div class="bg-white/20 px-5 py-2 rounded-xl backdrop-blur-md text-sm shadow-inner" x-data
                    x-init="setInterval(() => Livewire.emit('refreshComponent'), 60000)">

                    <i class="bi bi-calendar-heart"></i> Próxima cita:
                    <strong>
                        {{ $proxCita ? $proxCita->fec_cit->translatedFormat('l d \d\e F, H:i') : 'Sin próximas' }}
                    </strong>

                    @if ($tiempoRestante)
                        <br>
                        <span class="text-xs text-white/80">{{ $tiempoRestante }}</span>
                    @endif
                </div>



                <div class="bg-white/20 px-5 py-2 rounded-xl backdrop-blur-md text-sm shadow-inner">
                    <i class="bi bi-activity"></i> Citas este mes:
                    <strong>{{ $citas->whereBetween('fec_cit', [now()->startOfMonth(), now()->endOfMonth()])->count() }}</strong>
                </div>
            </div>
        </div>

        <div class="absolute inset-0 opacity-30 blur-2xl">
            <div class="absolute w-64 h-64 bg-fuchsia-400 rounded-full top-0 left-20 animate-pulse"></div>
            <div class="absolute w-72 h-72 bg-indigo-400 rounded-full bottom-0 right-20 animate-pulse delay-150"></div>
        </div>
    </div>

    <!-- 🗓️ SEMANA INTERACTIVA -->
    <div class="mb-10">
        <h3 class="text-xl font-bold text-violet-700 mb-4 flex items-center gap-2">
            <i class="bi bi-calendar-week"></i> Tu semana de citas
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
            @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $i => $dia)
                @php
                    $fecha = now()->startOfWeek()->addDays($i);
                    $cita = $citas->first(fn($c) => \Carbon\Carbon::parse($c->fec_cit)->isSameDay($fecha));
                    $estado = $cita->est_cit ?? null;
                    $color = match ($estado) {
                        'atendida' => 'from-emerald-400 to-green-500',
                        'cancelada' => 'from-rose-400 to-red-500',
                        default => 'from-violet-400 to-fuchsia-500',
                    };
                @endphp

                <div
                    class="group relative rounded-2xl p-5 bg-white border border-violet-100 shadow-sm hover:shadow-xl hover:scale-[1.03] transition-all cursor-pointer">
                    <p class="font-semibold text-gray-800 mb-1">{{ $dia }}</p>
                    <p class="text-xs text-gray-500 mb-2">{{ $fecha->format('d/m') }}</p>

                    @if ($cita)
                        <div class="text-sm">
                            <span class="block font-semibold text-violet-600">{{ $cita->mot_cit ?? 'Consulta' }}</span>
                            <span class="text-gray-500 text-xs flex items-center gap-1">
                                <i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($cita->fec_cit)->format('H:i') }}
                            </span>
                        </div>

                        <div
                            class="absolute top-0 left-0 w-1.5 h-full rounded-l-2xl bg-gradient-to-b {{ $color }}">
                        </div>

                        <div
                            class="absolute inset-0 bg-gradient-to-br {{ $color }} opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center rounded-2xl text-white transition">
                            <button class="text-sm font-semibold mb-1 hover:underline"><i class="bi bi-eye"></i>
                                Ver</button>
                            <button class="text-sm font-semibold mb-1 hover:underline"><i
                                    class="bi bi-arrow-repeat"></i> Reprogramar</button>
                            <button class="text-sm font-semibold text-rose-200 hover:text-white hover:underline"><i
                                    class="bi bi-x-circle"></i> Cancelar</button>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400 italic">Libre ✨</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- 📊 ESTADÍSTICAS -->
    <div class="grid md:grid-cols-2 gap-8 mb-12">
        <div class="bg-white rounded-3xl shadow-md p-6 border border-violet-100">
            <h3 class="text-lg font-semibold text-violet-700 mb-4 flex items-center gap-2">
                <i class="bi bi-graph-up"></i> Evolución mensual
            </h3>
            <canvas id="chart-progreso" class="w-full h-60"></canvas>
        </div>

        <div
            class="bg-white rounded-3xl shadow-md p-6 border border-violet-100 text-center flex flex-col items-center justify-center">
            <h3 class="text-lg font-semibold text-violet-700 mb-4 flex items-center gap-2">
                <i class="bi bi-award"></i> Constancia
            </h3>
            <div class="relative w-40 h-40">
                <canvas id="ring-asistencia"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-bold text-violet-600"
                        id="asistenciaPct">{{ round(($asistidas / max($total, 1)) * 100) }}%</span>
                    <span class="text-xs text-gray-500">Asistencia</span>
                </div>
            </div>
            <p class="text-sm text-gray-600 mt-3 italic">
                {{ $asistidas > $canceladas ? '¡Vas excelente, sigue así! 💪' : 'Podemos mejorar tu constancia 💜' }}
            </p>
        </div>
    </div>

    <!-- 💡 RECOMENDACIONES -->
    <div class="bg-white rounded-3xl shadow-md p-6 border border-violet-100 mb-10 text-center">
        <h3 class="text-lg font-semibold text-violet-700 mb-4 flex items-center justify-center gap-2">
            <i class="bi bi-lightbulb"></i> Consejo del día
        </h3>
        <p class="text-gray-700 text-sm italic" x-text="consejo"></p>
    </div>

    <!-- 🪄 MODAL NUEVA CITA -->
    <div x-show="showModal" x-transition.opacity
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div @click.away="showModal = false" class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl relative">
            <h3 class="text-lg font-bold text-violet-700 mb-4"><i class="bi bi-plus-circle"></i> Agendar nueva cita</h3>
            <p class="text-gray-600 text-sm mb-4">Selecciona fecha y hora:</p>
            <input type="datetime-local"
                class="border rounded-lg px-3 py-2 w-full mb-4 focus:ring-2 focus:ring-violet-400">
            <div class="flex justify-end gap-3">
                <button @click="showModal = false" class="px-4 py-2 text-gray-500 hover:text-gray-700">Cancelar</button>
                <button
                    class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition">Guardar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("livewire:load", () => {
            // 📈 Gráfico de progreso
            new Chart(document.getElementById("chart-progreso"), {
                type: "line",
                data: {
                    labels: @json($meses),
                    datasets: [{
                        label: "Citas asistidas",
                        data: @json($conteos),
                        borderColor: "#8B5CF6",
                        backgroundColor: "rgba(139,92,246,0.2)",
                        tension: 0.35,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // 🟣 Anillo de asistencia
            new Chart(document.getElementById("ring-asistencia"), {
                type: "doughnut",
                data: {
                    datasets: [{
                        data: [{{ $asistidas }}, {{ $total - $asistidas }}],
                        backgroundColor: ["#8B5CF6", "#E9D5FF"],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: "75%",
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
@endpush
