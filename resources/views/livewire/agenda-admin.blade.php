<div class="p-6 bg-gradient-to-br from-slate-50 via-cyan-50 to-indigo-50 min-h-[90vh]">

    {{-- ENCABEZADO EJECUTIVO --}}
    <div class="relative mb-8 rounded-3xl bg-gradient-to-r from-slate-800 via-indigo-800 to-cyan-700 text-white shadow-xl p-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h2 class="text-3xl md:text-4xl font-extrabold leading-tight">
                    Panel de administración
                </h2>
                <p class="text-white/90 mt-1">Visión global de citas, rendimiento y actividad del sistema</p>

                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                    <div class="bg-white/20 px-4 py-1.5 rounded-full backdrop-blur">
                        <i class="bi bi-calendar-event"></i>
                        Próxima cita global:
                        <strong>
                            {{ $proxCita ? \Carbon\Carbon::parse($proxCita->fec_cit)->translatedFormat('ddd D MMM, H:mm') : 'Sin próximas' }}
                        </strong>
                        @if($tiempoRestante)
                            <span class="ml-2 text-white/80">({{ $tiempoRestante }})</span>
                        @endif
                    </div>
                </div>
<<<<<<< HEAD
            </div>
=======
            </div> 
>>>>>>> Victor-Developer

            <div class="text-right">
                <p class="text-sm text-white/80">Administrador:</p>
                <p class="text-lg font-bold">{{ $administrador->usuario->name ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- KPIs GLOBALES --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow border border-slate-200">
            <p class="text-xs uppercase text-slate-500 font-bold">Total citas (sistema)</p>
            <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow border border-slate-200">
            <p class="text-xs uppercase text-slate-500 font-bold">Programadas</p>
            <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $programadas }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow border border-slate-200">
            <p class="text-xs uppercase text-slate-500 font-bold">Asistidas</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $asistidas }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow border border-slate-200">
            <p class="text-xs uppercase text-slate-500 font-bold">Canceladas</p>
            <p class="text-3xl font-extrabold text-rose-600 mt-1">{{ $canceladas }}</p>
        </div>
    </div>

    {{-- DISTRIBUCIÓN + ACTIVIDAD --}}
    <div class="grid md:grid-cols-2 gap-8 mb-10">
        <div class="bg-white rounded-2xl shadow border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="bi bi-graph-up"></i> Actividad mensual (asistidas + canceladas)
            </h3>
            <canvas id="admin-actividad" class="w-full h-60"></canvas>
        </div>

        <div class="bg-white rounded-2xl shadow border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="bi bi-pie-chart"></i> Distribución de estados
            </h3>
            <canvas id="admin-distribucion" class="w-full h-60"></canvas>
        </div>
    </div>

    {{-- ÚLTIMAS CITAS (tabla simple) --}}
    <div class="bg-white rounded-2xl shadow border border-slate-200 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i class="bi bi-clock-history"></i> Últimas citas registradas
        </h3>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b">
                        <th class="py-2 pr-4">Fecha</th>
                        <th class="py-2 pr-4">Motivo</th>
                        <th class="py-2 pr-4">Estado</th>
                        <th class="py-2 pr-4">Médico</th>
                        <th class="py-2 pr-4">Paciente/Solicitante</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citas->sortByDesc('fec_cit')->take(12) as $ci)
                        @php
                            $fecha = \Carbon\Carbon::parse($ci->fec_cit)->translatedFormat('D d MMM, H:mm');
                            $estado = strtolower($ci->est_cit ?? 'programada');
                            $esPaciente = !empty($ci->cod_pac ?? $ci->COD_PAC);
                        @endphp
                        <tr class="border-b last:border-0">
                            <td class="py-2 pr-4">{{ $fecha }}</td>
                            <td class="py-2 pr-4">{{ $ci->mot_cit ?? 'Consulta' }}</td>
                            <td class="py-2 pr-4 capitalize">{{ $estado }}</td>
                            <td class="py-2 pr-4">{{ $ci->cod_med ?? $ci->COD_MED ?? '—' }}</td>
                            <td class="py-2 pr-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $esPaciente ? 'bg-blue-100 text-blue-700' : 'bg-fuchsia-100 text-fuchsia-700' }}">
                                    {{ $esPaciente ? 'Paciente' : 'Solicitante' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-slate-400 italic" colspan="5">Sin registros</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("livewire:load", () => {
                // Actividad mensual
                const ctx1 = document.getElementById("admin-actividad");
                if (ctx1) {
                    new Chart(ctx1, {
                        type: "line",
                        data: {
                            labels: @json($meses),
                            datasets: [{
                                label: "Citas (mes)",
                                data: @json($conteos),
                                borderColor: "#0284C7",
                                backgroundColor: "rgba(2,132,199,0.15)",
                                tension: 0.35, fill: true, pointRadius: 5, pointHoverRadius: 7
                            }]
                        },
                        options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
                    });
                }

                // Distribución de estados
                const ctx2 = document.getElementById("admin-distribucion");
                if (ctx2) {
                    new Chart(ctx2, {
                        type: "doughnut",
                        data: {
                            labels: ["Programadas","Asistidas","Canceladas"],
                            datasets: [{
                                data: [{{ $programadas }}, {{ $asistidas }}, {{ $canceladas }}],
                                backgroundColor: ["#93C5FD","#34D399","#FCA5A5"],
                                borderWidth: 0
                            }]
                        },
                        options: { cutout: "70%", plugins:{legend:{position:"bottom"}} }
                    });
                }
            });
        </script>
    @endpush
</div>

