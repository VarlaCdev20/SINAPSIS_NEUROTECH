<div class="p-6 bg-gray-50 min-h-screen" x-data="{ mostrarDetalle: false }">

    <!-- 🩺 ENCABEZADO -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-700 text-white rounded-3xl p-8 shadow-lg mb-10">
        <h2 class="text-3xl font-bold mb-1">Dr. {{ $medico->usuario->name ?? 'Médico' }}</h2>
        <p class="text-sm text-white/80">Panel profesional de seguimiento clínico y gestión de citas</p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-sm text-white/80">Total citas</p>
                <p class="text-2xl font-bold">{{ $total }}</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-sm text-white/80">Asistidas</p>
                <p class="text-2xl font-bold text-emerald-300">{{ $asistidas }}</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-sm text-white/80">Canceladas</p>
                <p class="text-2xl font-bold text-rose-300">{{ $canceladas }}</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-sm text-white/80">Próxima cita</p>
                <p class="text-sm font-medium">
                    {{ $proxCita ? $proxCita->fec_cit->translatedFormat('d M H:i') : 'Sin próximas' }}
                </p>
                @if ($tiempoRestante)
                    <p class="text-xs text-white/70 mt-1">{{ $tiempoRestante }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- 🔍 BUSCADOR -->
    <div class="mb-10">
        <div class="relative max-w-lg mx-auto">
            <input type="text" wire:model.live.debounce.400ms="buscar"
                class="w-full px-5 py-3 rounded-2xl border border-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                placeholder="Buscar paciente o solicitante..." />
            <i class="bi bi-search absolute right-4 top-3.5 text-gray-400"></i>
        </div>

        @if (strlen($buscar) >= 2)
            <div class="mt-5 bg-white rounded-2xl shadow border border-gray-100 p-4">
                <h4 class="font-semibold text-gray-700 mb-3">Resultados:</h4>

                @forelse ($resultados as $res)
                    <div class="flex items-center justify-between border-b border-gray-100 py-2">
                        <div>
                            <p class="font-medium text-gray-800 flex items-center gap-2">
                                {{ $res['nombre'] ?? 'Sin nombre' }}
                                <span
                                    class="text-[10px] px-2 py-0.5 rounded-full font-semibold
                                    {{ ($res['tipo'] ?? '') === 'Paciente' ? 'bg-blue-100 text-blue-700' : 'bg-fuchsia-100 text-fuchsia-700' }}">
                                    {{ $res['tipo'] ?? '—' }}
                                </span>
                            </p>
                            <p class="text-xs text-gray-500">
                                Próxima cita:
                                {{ !empty($res['proxima_cita'])
                                    ? \Carbon\Carbon::parse($res['proxima_cita'])->translatedFormat('d M H:i')
                                    : 'Sin próxima cita' }}
                            </p>
                        </div>

                        <span
                            class="text-xs px-2 py-1 rounded-full
                            {{ ($res['estado'] ?? '') === 'Programada'
                                ? 'bg-blue-100 text-blue-700'
                                : (($res['estado'] ?? '') === 'Atendida'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-rose-100 text-rose-700') }}">
                            {{ ucfirst(strtolower($res['estado'] ?? '')) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 italic">Sin coincidencias encontradas.</p>
                @endforelse
            </div>
        @endif
    </div>

    <!-- 🗓️ AGENDA SEMANAL -->
    <div class="mb-10">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="bi bi-calendar-week text-indigo-600"></i> Agenda Semanal
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-4">
            @foreach ($semana as $fecha)
                @php
                    $citasDia = $citas->filter(fn($c) => \Carbon\Carbon::parse($c->fec_cit)->isSameDay($fecha));
                @endphp

                <div
                    class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition duration-200">
                    <div class="flex justify-between items-center mb-2">
                        <p class="font-semibold text-gray-800">{{ $fecha->translatedFormat('D d') }}</p>
                        <span class="text-xs text-indigo-600 font-semibold">{{ $citasDia->count() }} citas</span>
                    </div>

                    @forelse ($citasDia as $cita)
                        @php
                            $esPaciente = !empty($cita->cod_pac ?? $cita->COD_PAC);
                            $color = match (strtolower($cita->est_cit ?? '')) {
                                'atendida' => 'border-emerald-500 bg-emerald-50',
                                'cancelada' => 'border-rose-500 bg-rose-50',
                                default => 'border-blue-500 bg-blue-50',
                            };
                        @endphp

                        <div class="p-2 mb-2 rounded-lg border-l-4 {{ $color }}">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">
                                        {{ $esPaciente ? $cita->paciente->usuario->name ?? 'Paciente' : $cita->solicitante->nom_sol ?? 'Solicitante' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <i class="bi bi-clock"></i>
                                        {{ \Carbon\Carbon::parse($cita->fec_cit)->format('H:i') }}
                                        <span
                                            class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold
                                            {{ $esPaciente ? 'bg-blue-100 text-blue-700' : 'bg-fuchsia-100 text-fuchsia-700' }}">
                                            {{ $esPaciente ? 'Paciente' : 'Solicitante' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic text-center py-3">Sin citas</p>
                    @endforelse
                </div>
            @endforeach
        </div>
    </div>

    <!-- 📊 ESTADÍSTICAS -->
    <div class="grid md:grid-cols-2 gap-8 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow">
            <h3 class="font-bold text-indigo-700 mb-3"><i class="bi bi-graph-up"></i> Citas por mes</h3>
            <canvas id="graficoCitas"></canvas>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow text-center">
            <h3 class="font-bold text-indigo-700 mb-3"><i class="bi bi-person-check"></i> Pacientes atendidos</h3>
            <p class="text-5xl font-extrabold text-indigo-600">{{ $pacientesAtendidosMes }}</p>
            <p class="text-sm text-gray-500">En el mes actual</p>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("livewire:load", () => {
            new Chart(document.getElementById("graficoCitas"), {
                type: "bar",
                data: {
                    labels: @json($meses),
                    datasets: [{
                        label: "Citas",
                        data: @json($conteos),
                        backgroundColor: "rgba(37,99,235,0.6)",
                        borderRadius: 8
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
        });
    </script>
@endpush