@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Str;

    $statsPaciente = $statsPaciente ?? ['total' => 0, 'programadas' => 0, 'asistidas' => 0, 'canceladas' => 0];

    $proximasCitasPaciente = $proximasCitasPaciente ?? collect();
    $citasPasadasPaciente = $citasPasadasPaciente ?? collect();
    $citasDelMes = $citasDelMes ?? collect();
    $ultimoHistorial = $ultimoHistorial ?? null;
    $historialGeneral = $historialGeneral ?? null;

    $nombre = auth()->user()->name ?? 'Paciente';
    $hoy = ($hoy ?? now())->locale('es');

    // ---- Agregados para charts ----
    // Línea de tendencia (últimos 6 meses): contamos citas pasadas por mes
    $meses = [];
    $conteos = [];
    for ($i = 5; $i >= 0; $i--) {
        $m = now()->copy()->subMonths($i)->startOfMonth();
        $label = Str::ucfirst($m->locale('es')->isoFormat('MMM'));
        $meses[] = $label;
        $conteos[] = $citasPasadasPaciente
            ->filter(function ($c) use ($m) {
                $f = Carbon::parse($c->fec_cit ?? ($c->fec_reg_cit ?? now()));
                return $f->isSameMonth($m);
            })
            ->count();
    }

    // Racha (streak): días consecutivos con al menos una cita asistida en los últimos 30 días
    $asistidasRecientes = $citasPasadasPaciente
        ->filter(fn($c) => Str::lower($c->est_cit ?? '') === 'atendida')
        ->map(fn($c) => Carbon::parse($c->fec_cit)->toDateString())
        ->unique()
        ->sort()
        ->values();
    $streak = 0;
    if ($asistidasRecientes->isNotEmpty()) {
        $cursor = Carbon::parse($asistidasRecientes->last());
        $streak = 1;
        for ($i = $asistidasRecientes->count() - 2; $i >= 0; $i--) {
            $prev = Carbon::parse($asistidasRecientes[$i]);
            if ($prev->diffInDays($cursor) === 1) {
                $streak++;
                $cursor = $prev;
            } else {
                break;
            }
        }
    }

    // Insignias (logros) sencillos en base a stats
    $badges = [];
    if (($statsPaciente['asistidas'] ?? 0) >= 1) {
        $badges[] = ['🏁', 'Primera atención', 'Tu primera cita asistida registrada'];
    }
    if (($statsPaciente['asistidas'] ?? 0) >= 5) {
        $badges[] = ['🌟', 'Constancia 5+', 'Cinco o más citas asistidas'];
    }
    if (($statsPaciente['canceladas'] ?? 0) === 0 && ($statsPaciente['total'] ?? 0) > 0) {
        $badges[] = ['🛡️', 'Cero cancelaciones', '¡Excelente compromiso!'];
    }
    if ($streak >= 3) {
        $badges[] = ['🔥', 'Racha activa', 'Llevas ' . $streak . ' días seguidos con atención'];
    }
@endphp

<!-- HERO con gradiente y microcopy -->
<div
    class="relative overflow-hidden rounded-3xl p-6 mb-8 border border-violet-200 bg-gradient-to-br from-violet-600 via-fuchsia-600 to-indigo-600 text-white">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight">¡Hola, {{ $nombre }}! 💜</h2>
            <p class="opacity-90 mt-1 text-sm md:text-base">
                {{ $hoy->isoFormat('dddd D [de] MMMM [de] YYYY') }} — Nos alegra verte. Estamos cuidando cada detalle
                para tu bienestar. ✨
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="rounded-2xl px-4 py-2 bg-white/15 backdrop-blur ring-1 ring-white/30">
                <span class="text-xs uppercase opacity-90">Citas Totales</span>
                <div class="text-2xl font-bold">{{ $statsPaciente['total'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl px-4 py-2 bg-white/15 backdrop-blur ring-1 ring-white/30">
                <span class="text-xs uppercase opacity-90">Racha Salud</span>
                <div class="text-2xl font-bold">{{ $streak }} <span class="text-sm">día(s)</span></div>
            </div>
            <div class="rounded-2xl px-4 py-2 bg-white/15 backdrop-blur ring-1 ring-white/30">
                <span class="text-xs uppercase opacity-90">Próxima</span>
                <div class="text-sm font-medium">
                    @php $prox = $proximasCitasPaciente->first(); @endphp
                    {{ $prox ? Carbon::parse($prox->fec_cit)->format('d/m H:i') : 'Aún no hay registros' }}
                </div>
            </div>
        </div>
    </div>
    <svg class="absolute -top-16 -right-16 w-72 h-72 opacity-30 blur-2xl" viewBox="0 0 200 200"
        xmlns="http://www.w3.org/2000/svg">
        <path fill="#FFFFFF"
            d="M38.1,-63.7C51.1,-57.8,63.1,-51.3,71.9,-41.2C80.7,-31.1,86.3,-17.3,85.8,-4C85.3,9.3,78.6,18.6,72,28.6C65.3,38.7,58.7,49.4,49.1,58.4C39.4,67.5,26.8,74.8,13.6,76.6C0.3,78.4,-13.6,74.7,-25.9,68.2C-38.2,61.7,-48.9,52.4,-58.1,41.3C-67.2,30.1,-74.7,17,-76.2,2.8C-77.7,-11.5,-73.1,-26.8,-65.1,-38.8C-57.2,-50.9,-45.9,-59.7,-33.4,-65.5C-20.9,-71.3,-10.5,-74.1,0.2,-74.4C10.9,-74.8,21.8,-72.6,38.1,-63.7Z"
            transform="translate(100 100)" />
    </svg>
</div>

<!-- KPIs + Anillo de progreso -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <!-- KPI Cards -->
    <div class="xl:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $kpis = [
                [
                    'label' => 'Programadas',
                    'valor' => $statsPaciente['programadas'],
                    'icon' => 'bi-calendar-check',
                    'b' => 'violet',
                ],
                [
                    'label' => 'Asistidas',
                    'valor' => $statsPaciente['asistidas'],
                    'icon' => 'bi-heart-pulse',
                    'b' => 'emerald',
                ],
                [
                    'label' => 'Canceladas',
                    'valor' => $statsPaciente['canceladas'],
                    'icon' => 'bi-x-octagon',
                    'b' => 'rose',
                ],
                ['label' => 'Totales', 'valor' => $statsPaciente['total'], 'icon' => 'bi-collection', 'b' => 'indigo'],
            ];
        @endphp
        @foreach ($kpis as $k)
            <div class="bg-white/90 backdrop-blur p-4 rounded-2xl shadow-sm border border-{{ $k['b'] }}-100">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase text-gray-500">{{ $k['label'] }}</span>
                    <i class="bi {{ $k['icon'] }} text-{{ $k['b'] }}-600 text-xl"></i>
                </div>
                <div class="mt-1 text-3xl font-bold text-gray-900">{{ $k['valor'] }}</div>
            </div>
        @endforeach
    </div>

    <!-- Progreso circular -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center">
        <h5 class="font-semibold text-gray-800 mb-2">Compromiso con tu salud</h5>
        <canvas id="chart-ring" class="w-48 h-48"></canvas>
        <p class="text-xs text-gray-500 mt-3">
            Mostrando porcentaje de citas asistidas sobre el total.
        </p>
    </div>
</div>

<!-- Mosaico: Calendario + Estadísticas Pro -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Calendario -->
    <div class="lg:col-span-2 bg-white rounded-3xl shadow-md border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-semibold text-indigo-700 text-lg flex items-center gap-2">
                <i class="bi bi-calendar3 text-indigo-600 text-xl"></i> Tu calendario
            </h5>
            <span class="text-xs text-gray-500 italic">Toca un evento para ver detalles</span>
        </div>
        <div id="calendar-paciente" class="rounded-xl overflow-hidden calendar-theme min-h-[420px]"></div>
    </div>

    <!-- Estadísticas -->
    <div class="bg-white rounded-3xl shadow-md border border-gray-200 p-5 flex flex-col gap-6">
        <div>
            <h5 class="font-semibold text-gray-800 mb-1">Distribución de tus citas</h5>
            <canvas id="chart-donut" class="h-48 mt-3"></canvas>
        </div>
        <div class="border-t pt-4">
            <h5 class="font-semibold text-gray-800 mb-1">Tendencia últimos 6 meses</h5>
            <canvas id="chart-line" class="h-36 mt-3"></canvas>
        </div>
    </div>
</div>

<!-- Próximas citas (DataTable) -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
    <div class="flex justify-between items-center mb-4">
        <h5 class="font-semibold text-gray-800 flex items-center gap-2">
            <i class="bi bi-calendar2-week text-[#6D28D9]"></i> Próximas Citas
        </h5>
        <div class="flex flex-wrap gap-2">
            <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-violet-50" data-filter="all">
                <i class="bi bi-list-ul me-1"></i> Todas
            </button>
            <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-indigo-50" data-filter="virtual">
                <i class="bi bi-camera-video me-1 text-indigo-600"></i> Virtuales
            </button>
            <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-emerald-50" data-filter="presencial">
                <i class="bi bi-hospital me-1 text-emerald-600"></i> Presenciales
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm" id="tabla-citas-paciente">
            <thead>
                <tr class="text-left text-gray-600 uppercase text-xs border-b">
                    <th class="py-2 px-3">Tipo</th>
                    <th class="py-2 px-3">Fecha</th>
                    <th class="py-2 px-3">Hora</th>
                    <th class="py-2 px-3">Motivo</th>
                    <th class="py-2 px-3">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proximasCitasPaciente as $cita)
                    @php
                        $tipo = Str::lower($cita->tip_cit ?? 'presencial');
                        $estado = Str::lower($cita->est_cit ?? 'programada');
                        $estadoStyle = match ($estado) {
                            'programada' => 'bg-violet-100 text-violet-700',
                            'atendida' => 'bg-emerald-100 text-emerald-700',
                            'cancelada' => 'bg-rose-100 text-rose-700',
                            default => 'bg-gray-100 text-gray-700',
                        };
                        $fecha = Carbon::parse($cita->fec_cit ?? ($cita->fec_reg_cit ?? now()));
                    @endphp
                    <tr class="border-b hover:bg-gray-50 transition" data-tipo="{{ $tipo }}">
                        <td class="py-2 px-3 capitalize">{{ $tipo }}</td>
                        <td class="py-2 px-3">{{ $fecha->format('d/m/Y') }}</td>
                        <td class="py-2 px-3">{{ $fecha->format('H:i') }}</td>
                        <td class="py-2 px-3 truncate max-w-[320px]">{{ $cita->mot_cit ?? 'Consulta' }}</td>
                        <td class="py-2 px-3">
                            <span class="px-2.5 py-1 rounded-full text-xs {{ $estadoStyle }}">
                                {{ ucfirst($estado) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-3">Aún no hay registros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Insignias + Timeline de historial -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    <!-- Insignias -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="bi bi-stars text-[#6D28D9]"></i> Tus logros
        </h5>
        @if (empty($badges))
            <p class="text-center text-gray-500">Aún no hay registros.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($badges as [$ic, $tit, $sub])
                    <div class="rounded-xl border border-violet-100 p-3 hover:shadow-sm transition">
                        <div class="text-2xl leading-none">{{ $ic }}</div>
                        <div class="font-semibold text-gray-800">{{ $tit }}</div>
                        <div class="text-xs text-gray-500">{{ $sub }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Último historial -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="bi bi-journal-medical text-[#6D28D9]"></i> Último historial clínico
        </h5>
        @if ($ultimoHistorial)
            <div class="grid sm:grid-cols-3 gap-3">
                <div>
                    <div class="text-xs text-gray-500">Inicio</div>
                    <div class="font-semibold text-gray-900">
                        {{ Carbon::parse($ultimoHistorial->FEC_INI_EPI)->format('d/m/Y H:i') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Médico</div>
                    <div class="font-semibold text-gray-900">{{ $ultimoHistorial->COD_MED ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Descripción</div>
                    <div class="font-semibold text-gray-900 line-clamp-2">{{ $ultimoHistorial->DES_EPI ?? '—' }}</div>
                </div>
            </div>
        @else
            <p class="text-center text-gray-500">Aún no hay registros de historial.</p>
        @endif

        <div class="mt-4 border-t pt-4">
            <h6 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                <i class="bi bi-clock-history text-indigo-600"></i> Resumen general
            </h6>
            @if ($historialGeneral)
                <div class="text-sm text-gray-700">
                    <span class="font-medium">Creado:</span>
                    {{ Carbon::parse($historialGeneral->FEC_CRE_HIS)->format('d/m/Y H:i') }} ·
                    <span class="font-medium">Alergias:</span>
                    {{ $historialGeneral->ALE_HIS ? Str::limit($historialGeneral->ALE_HIS, 80) : '—' }} ·
                    <span class="font-medium">Tipo de sangre:</span> {{ $historialGeneral->TIP_SAN_HIS ?? '—' }}
                </div>
            @else
                <p class="text-gray-500 text-sm">Aún no hay registros en tu historia clínica general.</p>
            @endif
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdn.jsdelivr.net/npm/datatables.net-dt@2.0.3/css/dataTables.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.0.3/js/dataTables.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        const total = Number(@json($statsPaciente['total'] ?? 0));
        const asistidas = Number(@json($statsPaciente['asistidas'] ?? 0));
        const ring = document.getElementById("chart-ring");
        if (ring) {
            new Chart(ring, {
                type: "doughnut",
                data: {
                    labels: ["Asistidas", "Restantes"],
                    datasets: [{
                        data: [asistidas, Math.max(total - asistidas, 0)],
                        backgroundColor: ["#8B5CF6", "#EDE9FE"],
                        borderWidth: 2,
                        borderColor: "#fff"
                    }]
                },
                options: {
                    cutout: "72%",
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ctx.label + ': ' + ctx.raw
                            }
                        }
                    }
                },
                plugins: [{
                    id: 'centerText',
                    afterDraw(chart) {
                        const {
                            ctx,
                            chartArea: {
                                left,
                                right,
                                top,
                                bottom
                            }
                        } = chart;
                        ctx.save();
                        const pct = total > 0 ? Math.round((asistidas / total) * 100) : 0;
                        ctx.font = "600 22px Poppins, sans-serif";
                        ctx.fillStyle = "#4C1D95";
                        ctx.textAlign = "center";
                        ctx.fillText(pct + "%", (left + right) / 2, (top + bottom) / 2);
                        ctx.restore();
                    }
                }]
            });
        }

        // ---- Filtro de tabla ----
        const btns = document.querySelectorAll("[data-filter]");
        const rows = document.querySelectorAll("#tabla-citas-paciente tbody tr[data-tipo]");
        btns.forEach(btn => {
            btn.addEventListener("click", () => {
                const tipo = btn.dataset.filter;
                rows.forEach(r => r.style.display = (tipo === "all" || r.dataset.tipo ===
                    tipo) ? "" : "none");
                btns.forEach(b => b.classList.remove("ring-2", "ring-violet-300",
                    "bg-violet-50"));
                btn.classList.add("ring-2", "ring-violet-300", "bg-violet-50");
            });
        });

        // ---- Gráfico Donut ----
        // ==== 🎨 Distribución de tus Citas (versión dinámica paciente) ====
        // ==== 💜 Distribución de tus Citas (versión mejorada y animada) ====
        const donut = document.getElementById("chart-donut");
        if (donut) {
            const programadas = Number(@json($statsPaciente['programadas'] ?? 0));
            const asistidas = Number(@json($statsPaciente['asistidas'] ?? 0));
            const canceladas = Number(@json($statsPaciente['canceladas'] ?? 0));
            const total = programadas + asistidas + canceladas || 1;

            // Crear gradientes reales 🎨
            const ctx = donut.getContext("2d");
            const gradProgramadas = ctx.createLinearGradient(0, 0, 200, 200);
            gradProgramadas.addColorStop(0, "#C4B5FD");
            gradProgramadas.addColorStop(1, "#8B5CF6");

            const gradAsistidas = ctx.createLinearGradient(0, 0, 200, 200);
            gradAsistidas.addColorStop(0, "#6EE7B7");
            gradAsistidas.addColorStop(1, "#10B981");

            const gradCanceladas = ctx.createLinearGradient(0, 0, 200, 200);
            gradCanceladas.addColorStop(0, "#FCA5A5");
            gradCanceladas.addColorStop(1, "#DC2626");

            const chart = new Chart(donut, {
                type: "doughnut",
                data: {
                    labels: ["Programadas", "Asistidas", "Canceladas"],
                    datasets: [{
                        data: [programadas, asistidas, canceladas],
                        backgroundColor: [gradProgramadas, gradAsistidas, gradCanceladas],
                        borderColor: "#fff",
                        borderWidth: 3,
                        hoverOffset: 16
                    }]
                },
                options: {
                    responsive: true,
                    cutout: "68%",
                    layout: {
                        padding: 10
                    },
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                color: "#4C1D95",
                                font: {
                                    size: 13,
                                    weight: "600"
                                },
                                padding: 16,
                                usePointStyle: true,
                                pointStyle: "circle"
                            }
                        },
                        tooltip: {
                            backgroundColor: "#4C1D95",
                            titleFont: {
                                size: 13,
                                weight: "600"
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: (ctx) => {
                                    const value = ctx.raw;
                                    const pct = ((value / total) * 100).toFixed(1);
                                    return `${ctx.label}: ${value} (${pct}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 1800,
                        easing: "easeOutElastic"
                    },
                    hover: {
                        mode: "nearest",
                        intersect: true,
                        animationDuration: 400
                    }
                },
                plugins: [{
                    id: "centerText",
                    afterDraw(chart) {
                        const {
                            ctx,
                            chartArea: {
                                left,
                                right,
                                top,
                                bottom
                            }
                        } = chart;
                        const pct = ((asistidas / total) * 100).toFixed(1);

                        // Color dinámico del porcentaje
                        let color = "#4C1D95";
                        if (pct >= 70) color = "#059669";
                        else if (pct >= 40) color = "#EAB308";
                        else color = "#DC2626";

                        ctx.save();
                        ctx.font = "700 22px 'Poppins', sans-serif";
                        ctx.fillStyle = color;
                        ctx.textAlign = "center";
                        ctx.textBaseline = "middle";
                        ctx.shadowColor = "rgba(0,0,0,0.15)";
                        ctx.shadowBlur = 6;
                        ctx.fillText(`${pct}%`, (left + right) / 2, (top + bottom) / 2 - 5);
                        ctx.shadowBlur = 0;
                        ctx.font = "500 13px 'Poppins', sans-serif";
                        ctx.fillStyle = "#4C1D95";
                        ctx.fillText("asistidas", (left + right) / 2, (top + bottom) / 2 + 18);
                        ctx.restore();
                    }
                }]
            });

            // 💫 Efecto flotante (latido suave del anillo)
            let grow = true;
            setInterval(() => {
                const scale = grow ? 1.02 : 0.98;
                donut.style.transform = `scale(${scale})`;
                donut.style.transition = "transform 0.8s ease-in-out";
                grow = !grow;
            }, 1000);

            // 🌈 Brillo al pasar el cursor
            donut.addEventListener("mouseenter", () => {
                donut.style.filter = "drop-shadow(0 0 12px rgba(139,92,246,0.4))";
            });
            donut.addEventListener("mouseleave", () => {
                donut.style.filter = "none";
            });
        }



        // ---- Línea tendencia ----
        const line = document.getElementById("chart-line");
        if (line) {
            new Chart(line, {
                type: "line",
                data: {
                    labels: @json($meses),
                    datasets: [{
                        label: "Citas por mes",
                        data: @json($conteos),
                        tension: 0.35,
                        fill: true,
                        backgroundColor: "rgba(139,92,246,0.15)",
                        borderColor: "#8B5CF6",
                        pointBackgroundColor: "#8B5CF6",
                        pointRadius: 3,
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // ---- FullCalendar ----
        const eventos = [
            @foreach ($citasDelMes as $fecha => $items)
                @foreach ($items as $c)
                    {
                        title: {!! json_encode($c->mot_cit ?? 'Consulta') !!},
                        start: {!! json_encode($c->fec_cit ?? $c->fec_reg_cit) !!},
                        extendedProps: {
                            estado: {!! json_encode(Str::lower($c->est_cit ?? 'programada')) !!},
                            tipo: {!! json_encode(Str::lower($c->tip_cit ?? 'presencial')) !!}
                        }
                    },
                @endforeach
            @endforeach
        ];

        const calendarEl = document.getElementById("calendar-paciente");
        if (calendarEl) {
            const cal = new FullCalendar.Calendar(calendarEl, {
                initialView: "dayGridMonth",
                locale: "es",
                height: "auto",
                events: eventos,
                eventDidMount: (info) => {
                    const est = info.event.extendedProps?.estado || 'programada';
                    info.el.style.borderRadius = "10px";
                    info.el.style.border = "none";
                    const map = {
                        programada: "#8B5CF6",
                        atendida: "#10B981",
                        cancelada: "#F43F5E"
                    };
                    info.el.style.backgroundColor = map[est] || "#6366F1";
                },
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: ""
                }
            });
            cal.render();

            // 💜 Popup dinámico
            calendarEl.addEventListener("click", (e) => {
                const cell = e.target.closest(".fc-daygrid-day");
                if (!cell) return;

                const fechaISO = cell.getAttribute("data-date");
                const delDia = eventos.filter(ev => (ev.start || '').slice(0, 10) === fechaISO);
                mostrarAvisoPaciente(cell, delDia, fechaISO);
            });

            function mostrarAvisoPaciente(celda, citas, fechaISO) {
                document.querySelectorAll(".popup-paciente").forEach(p => p.remove());
                const popup = document.createElement("div");
                popup.classList.add("popup-paciente");
                Object.assign(popup.style, {
                    position: "absolute",
                    zIndex: "9999",
                    background: "linear-gradient(135deg, #EDE9FE, #F5F3FF)",
                    border: "2px solid #A78BFA",
                    borderRadius: "12px",
                    boxShadow: "0 6px 16px rgba(139,92,246,0.25)",
                    padding: "12px 16px",
                    fontSize: "15px",
                    color: "#4C1D95",
                    maxWidth: "280px",
                    textAlign: "center",
                    backdropFilter: "blur(8px)",
                    opacity: "0",
                    transform: "translateY(-8px)",
                    transition: "all 0.3s ease"
                });

                if (citas.length === 0) {
                    popup.innerHTML = `
                    <div>
                        <div class="text-lg font-semibold mb-1">💜 Sin citas este día</div>
                        <div class="text-sm text-gray-600">Aprovecha el descanso y sigue cuidando tu bienestar 🧘‍♀️</div>
                    </div>`;
                } else {
                    let citasHTML = "";
                    citas.forEach(ev => {
                        const e = ev.extendedProps || {};
                        const tipo = e.tipo === "virtual" ? "💻 Virtual" : "🏥 Presencial";
                        const hora = ev.start?.slice(11, 16) || "--:--";
                        citasHTML += `
                        <div class="text-sm mt-1">
                            📅 <strong>${fechaISO}</strong> · 🕒 ${hora}
                        </div>
                        <div class="text-gray-600">${tipo}</div>
                    `;
                    });

                    popup.innerHTML = `
                    <div>
                        <div class="text-lg font-semibold mb-1">
                            ✨ ¡Tienes ${citas.length} cita${citas.length > 1 ? 's' : ''}!
                        </div>
                        ${citasHTML}
                        <a href="#"
                           class="inline-block mt-3 px-3 py-1.5 bg-gradient-to-r from-violet-500 to-fuchsia-500 text-white rounded-full text-xs font-medium shadow-md hover:scale-[1.05] hover:shadow-lg transition">
                           Ver detalles
                        </a>
                        <div class="mt-2 text-xs text-violet-700 font-medium">
                            Recuerda asistir a tiempo 💪
                        </div>
                    </div>`;
                }

                const rect = celda.getBoundingClientRect();
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                const scrollLeft = window.scrollX || document.documentElement.scrollLeft;
                popup.style.top = `${rect.top + scrollTop + 50}px`;
                popup.style.left = `${rect.left + scrollLeft + rect.width / 2 - 120}px`;

                document.body.appendChild(popup);
                setTimeout(() => {
                    popup.style.opacity = "1";
                    popup.style.transform = "translateY(0)";
                }, 50);
                setTimeout(() => popup.remove(), 4000);
            }
        }

        // ---- DataTables ----
        const t = $("#tabla-citas-paciente").DataTable({
            paging: true,
            searching: true,
            info: false,
            pageLength: 5,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            columnDefs: [{
                targets: [0],
                searchable: true
            }]
        });

        document.querySelectorAll("[data-filter]").forEach(btn => {
            btn.addEventListener("click", () => {
                const tipo = btn.dataset.filter;
                if (tipo === "all") t.column(0).search("").draw();
                else t.column(0).search(tipo, true, false).draw();
                document.querySelectorAll("[data-filter]").forEach(b => b.classList.remove(
                    "ring-2", "ring-violet-300", "bg-violet-50"));
                btn.classList.add("ring-2", "ring-violet-300", "bg-violet-50");
            });
        });
    });
</script>



<style>
    /* FullCalendar estilizado violeta-neón */
    #chart-donut:hover {
        filter: drop-shadow(0 0 10px rgba(139, 92, 246, 0.3)) drop-shadow(0 0 20px rgba(139, 92, 246, 0.2));
        transition: filter 0.3s ease-in-out;
    }

    .calendar-theme .fc-toolbar-title {
        font-size: 1rem;
        color: #6D28D9;
        font-weight: 800;
        letter-spacing: .2px;
    }

    .calendar-theme .fc-button {
        background-color: #EDE9FE;
        color: #4C1D95;
        border: none;
        border-radius: .75rem;
        padding: .35rem .65rem;
        font-size: .75rem;
    }

    .calendar-theme .fc-button:hover {
        background-color: #DDD6FE;
    }

    .calendar-theme .fc-daygrid-day {
        transition: background-color .2s ease;
    }

    .calendar-theme .fc-daygrid-day:hover {
        background-color: #F5F3FF;
        cursor: pointer;
    }

    .calendar-theme .fc-day-today {
        background-color: #F5F3FF !important;
        outline: 2px dashed #A78BFA;
        outline-offset: -2px;
        border: none !important;
    }

    /* DataTables afinado */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #E5E7EB;
        border-radius: .75rem;
        padding: .35rem .6rem;
        outline: none;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 1px solid #EDE9FE !important;
        border-radius: .6rem !important;
        padding: .25rem .5rem !important;
        margin: 0 .15rem !important;
        color: #4C1D95 !important;
        background: #fff !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #EDE9FE !important;
    }

    /* Utilidades */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Popup del calendario del paciente */
    .popup-paciente {
        backdrop-filter: blur(8px);
        animation: fadeUp 0.3s ease forwards;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(-10px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .popup-paciente {
        animation: fadeUp 0.3s ease forwards;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(-10px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>
