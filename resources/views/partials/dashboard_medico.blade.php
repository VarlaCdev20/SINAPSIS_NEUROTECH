    {{-- resources/views/partials/dashboard_medico.blade.php --}}
    @php
        use Illuminate\Support\Carbon;

        // Salvaguardas de datos
        $stats = $stats ?? ['virtual' => 0, 'presencial' => 0, 'canceladas' => 0, 'total' => 0];
        $proximasCitas = $proximasCitas ?? collect();
        $solicitantesPendientes = ($solicitantesPendientes ?? collect())->filter(
            fn($s) => strtolower($s->est_sol ?? '') === 'pendiente',
        );
        $ultimosPacientes = $ultimosPacientes ?? collect();
        $hoyTotal = $hoyTotal ?? 0;
        $hoyVirtual = $hoyVirtual ?? 0;
        $hoyPresencial = $hoyPresencial ?? 0;
        $hoyCanceladas = $hoyCanceladas ?? 0;

        // Eventos para calendario
        $calendarEvents = $proximasCitas
            ->map(function ($c) {
                $esPaciente = !empty($c->cod_pac);
                $tipoUsuario = $esPaciente ? 'Paciente' : 'Solicitante';
                $nombre = $esPaciente
                    ? $c->paciente->usuario->name ?? ($c->paciente->usuario->NOM_USU ?? 'Sin nombre')
                    : ($c->solicitante->nombre_completo ??
                    trim(
                        ($c->solicitante->nom_sol ?? '') .
                            ' ' .
                            ($c->solicitante->ap_pat_sol ?? '') .
                            ' ' .
                            ($c->solicitante->ap_mat_sol ?? ''),
                    ) ?:
                    'Sin nombre');

                $start = (string) ($c->fec_cit ?? ($c->FEC_CIT ?? $c->fec_reg_cit));

                return [
                    'id' => $c->cod_cit ?? ($c->COD_CIT ?? null),
                    'title' => '[' . $tipoUsuario . '] ' . $nombre,
                    'start' => $start,
                    'extendedProps' => [
                        'tipoCita' => strtolower($c->tip_cit ?? 'presencial'),
                        'motivo' => $c->mot_cit ?? 'Consulta general',
                        'estado' => strtolower($c->est_cit ?? 'registrado'),
                        'tipoUsuario' => $tipoUsuario,
                        'nombre' => $nombre,
                        'hora' => \Illuminate\Support\Str::of($start)->substr(11, 5),
                        'fecha' => \Illuminate\Support\Str::of($start)->substr(0, 10),
                    ],
                ];
            })
            ->values();
    @endphp

    <!-- Encabezado -->
    <div class="mb-6">
        <h2 class="text-3xl font-semibold text-[#4C1D95] tracking-tight">Panel Médico</h2>
        <p class="text-gray-600 mt-1 text-sm">
            Gestiona tus <span class="font-semibold text-[#6D28D9]">citas</span>, revisa tus
            <span class="font-semibold text-[#0891B2]">solicitantes</span> y controla el flujo de atención.
        </p>
    </div>

    <!-- KPIs del día -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white/90 backdrop-blur p-4 rounded-2xl shadow-sm border border-violet-100">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase text-gray-500">Citas hoy</span>
                <i class="bi bi-calendar2-week text-[#6D28D9] text-xl"></i>
            </div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ $hoyTotal }}</div>
        </div>

        <div class="bg-white/90 backdrop-blur p-4 rounded-2xl shadow-sm border border-cyan-100">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase text-gray-500">Reuniones Virtuales</span>
                <i class="bi bi-camera-video text-[#0891B2] text-xl"></i>
            </div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ $hoyVirtual }}</div>
        </div>

        <div class="bg-white/90 backdrop-blur p-4 rounded-2xl shadow-sm border border-emerald-100">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase text-gray-500">Presenciales</span>
                <i class="bi bi-hospital text-emerald-600 text-xl"></i>
            </div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ $hoyPresencial }}</div>
        </div>

        <div class="bg-white/90 backdrop-blur p-4 rounded-2xl shadow-sm border border-rose-100">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase text-gray-500">Canceladas</span>
                <i class="bi bi-x-octagon text-rose-600 text-xl"></i>
            </div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ $hoyCanceladas }}</div>
        </div>
    </div>

    <!-- 🔹 Mosaico principal: Agenda + Estadísticas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <!-- ✅ Agenda compacta: FullCalendar Moderno -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-md border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h5 class="font-semibold text-indigo-700 text-lg flex items-center gap-2">
                    <i class="bi bi-calendar3-range text-indigo-600 text-xl"></i>
                    Agenda rápida
                </h5>
                <span class="text-xs text-gray-500 italic">Toque o clic en una fecha</span>
            </div>

            <div id="calendar-mini" class="rounded-xl overflow-hidden calendar-compact-theme min-h-[380px]"></div>
        </div>

        <!-- 🔹 Estadísticas -->
        <div class="bg-white rounded-3xl shadow-md border border-gray-200 p-5 flex flex-col gap-6">
            <div>
                <h5 class="font-semibold text-gray-800 mb-1">Estadísticas de citas</h5>
                <p class="text-xs text-gray-500">Distribución actual</p>
                <canvas id="chart-citas" class="h-48 mt-3"></canvas>
            </div>

            <div class="border-t pt-4">
                <h5 class="font-semibold text-gray-800 mb-1">Tabla de Solicitantes</h5>
                <p class="text-xs text-gray-500">Tus Solicitantes Pendientes</p>
                <canvas id="chart-conversion" class="h-36 mt-3"></canvas>
            </div>
        </div>
    </div>


    <!-- Próximas citas -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h5 class="font-semibold text-gray-800">📅 Próximas Citas</h5>
            <div class="flex flex-wrap gap-2">
                <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-violet-50" data-filter="all">
                    <i class="bi bi-list-ul me-1"></i> Todas
                </button>
                <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-cyan-50" data-filter="virtual">
                    <i class="bi bi-camera-video me-1 text-cyan-600"></i> Virtuales
                </button>
                <button class="px-3 py-1.5 rounded-lg border text-sm hover:bg-emerald-50" data-filter="presencial">
                    <i class="bi bi-hospital me-1 text-emerald-600"></i> Presenciales
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-600 uppercase text-xs border-b">
                        <th class="py-2 px-3">Tipo</th>
                        <th class="py-2 px-3">Nombre</th>
                        <th class="py-2 px-3">Día</th>
                        <th class="py-2 px-3">Hora</th>
                        <th class="py-2 px-3">Motivo</th>
                        <th class="py-2 px-3">Estado</th>
                        <th class="py-2 px-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-citas">
                    @forelse($proximasCitas as $cita)
                        @php
                            $esPaciente = !empty($cita->cod_pac);
                            $tipoUsuario = $esPaciente ? 'Paciente' : 'Solicitante';
                            $nombre = $esPaciente
                                ? $cita->paciente->usuario->name ?? ($cita->paciente->usuario->NOM_USU ?? 'Sin nombre')
                                : ($cita->solicitante->nombre_completo ??
                                trim(
                                    ($cita->solicitante->nom_sol ?? '') .
                                        ' ' .
                                        ($cita->solicitante->ap_pat_sol ?? '') .
                                        ' ' .
                                        ($cita->solicitante->ap_mat_sol ?? ''),
                                ) ?:
                                'Sin nombre');
                            $fecha = Carbon::parse($cita->fec_cit ?? $cita->fec_reg_cit);
                            $dia = $fecha->format('d/m/Y');
                            $hora = $fecha->format('H:i');
                            $tipoCita = strtolower($cita->tip_cit ?? 'presencial');
                            $estado = strtolower($cita->est_cit ?? 'registrado');
                            $estadoStyle = match ($estado) {
                                'registrado' => 'bg-violet-100 text-violet-700',
                                'confirmado' => 'bg-emerald-100 text-emerald-700',
                                'reprogramada', 'reprogramado' => 'bg-amber-100 text-amber-700',
                                'cancelada', 'cancelado' => 'bg-rose-100 text-rose-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition" data-tipo="{{ $tipoCita }}">
                            <td class="py-2 px-3">
                                <span
                                    class="inline-flex items-center gap-1 text-xs font-medium {{ $esPaciente ? 'text-purple-700' : 'text-indigo-700' }}">
                                    <i class="bi {{ $esPaciente ? 'bi-person-badge' : 'bi-person' }}"></i>
                                    [{{ $tipoUsuario }}]
                                </span>
                            </td>
                            <td class="py-2 px-3">{{ $nombre }}</td>
                            <td class="py-2 px-3">{{ $dia }}</td>
                            <td class="py-2 px-3">{{ $hora }}</td>
                            <td class="py-2 px-3 truncate max-w-[280px]">{{ $cita->mot_cit ?? 'Consulta general' }}
                            </td>
                            <td class="py-2 px-3">
                                <span class="px-2.5 py-1 rounded-full text-xs {{ $estadoStyle }}">
                                    {{ ucfirst($estado) }}
                                </span>
                            </td>
                            <td class="py-2 px-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-pill"
                                        title="Editar" data-action="editar" data-id="{{ $cita->cod_cit }}"><i
                                            class="bi bi-pencil-square"></i></a>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill" title="Reprogramar"
                                        data-action="reprogramar" data-id="{{ $cita->cod_cit }}"><i
                                            class="bi bi-arrow-repeat"></i></button>
                                    <button class="btn btn-outline-danger btn-sm rounded-pill" title="Cancelar"
                                        data-action="cancelar" data-id="{{ $cita->cod_cit }}"><i
                                            class="bi bi-x-circle"></i></button>
                                    <a href="#" class="btn btn-light btn-sm rounded-pill" title="Más detalles"><i
                                            class="bi bi-search"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-4">No hay citas programadas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Solicitantes pendientes -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
        <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="bi bi-hourglass-split text-[#6D28D9]"></i> Solicitantes Pendientes
        </h5>

        @forelse($solicitantesPendientes as $sol)
            @php
                $nombreSol =
                    $sol->nombre_completo ??
                    trim(($sol->nom_sol ?? '') . ' ' . ($sol->ap_pat_sol ?? '') . ' ' . ($sol->ap_mat_sol ?? ''));
            @endphp
            <div
                class="flex justify-between items-center border-b border-gray-100 py-3 hover:bg-gray-50 transition rounded-lg">
                <div>
                    <h6 class="font-semibold text-gray-800 mb-1">[Solicitante] {{ $nombreSol ?: 'Sin nombre' }}</h6>
                    <p class="text-sm text-gray-600"><strong>📞</strong> {{ $sol->cel_sol ?? 'Sin número' }}</p>
                    <p class="text-sm text-gray-600"><strong>💬</strong> {{ $sol->des_sol ?? 'Sin descripción' }}</p>
                </div>
                <span class="text-xs font-medium text-gray-700 bg-amber-100 px-3 py-1 rounded-full">Pendiente</span>
            </div>
        @empty
            <p class="text-center text-gray-500 py-3">No hay solicitantes pendientes.</p>
        @endforelse
    </div>

    <!-- Últimos pacientes atendidos -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-10">
        <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="bi bi-person-heart text-[#6D28D9]"></i> Últimos pacientes atendidos
        </h5>
        @if ($ultimosPacientes->count() > 0)
            <div class="divide-y">
                @foreach ($ultimosPacientes as $p)
                    @php
                        $nombreP = $p->usuario->name ?? ($p->usuario->NOM_USU ?? 'Sin nombre');
                        $ultima = isset($p->ultima_cita) ? Carbon::parse($p->ultima_cita)->format('d/m/Y H:i') : '—';
                    @endphp
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium text-gray-900">[Paciente] {{ $nombreP }}</div>
                            <div class="text-xs text-gray-500">Última cita: {{ $ultima }}</div>
                        </div>
                        <a href="#" class="text-sm text-[#6D28D9] hover:underline">Más detalles</a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">No hay registros recientes.</p>
        @endif
    </div>

    {{-- Librerías necesarias --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // === 📊 Gráfico de distribución de citas ===
        (function() {
            const ctx = document.getElementById('chart-citas');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Virtual', 'Presencial', 'Canceladas'],
                    datasets: [{
                        data: [
                            Number(@json($stats['virtual'] ?? 0)),
                            Number(@json($stats['presencial'] ?? 0)),
                            Number(@json($stats['canceladas'] ?? 0))
                        ],
                        backgroundColor: ['#3B82F6', '#EC4899', '#F59E0B'],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    cutout: '62%'
                }
            });
        })();

        // === 📈 Gráfico de conversión clínica ===
        (function() {
            const ctx = document.getElementById('chart-conversion');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Pendientes', 'Aprobados', 'Rechazados'],
                    datasets: [{
                        data: [
                            Number(@json($solPendientes ?? 0)),
                            Number(@json($solAprobados ?? 0)),
                            Number(@json($solRechazados ?? 0))
                        ],
                        backgroundColor: ['#6366F1', '#16A34A', '#DC2626'],
                        borderColor: ['#4338CA', '#0F7A36', '#B91C1C'],
                        borderWidth: 1.5
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
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        })();

        // === 📅 Calendario compacto semanal ===
        (function() {
            const eventos = @json($calendarEvents);
            const el = document.getElementById('calendar-mini');
            if (!el) return;

            const cal = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                contentHeight: "auto",
                height: "auto",
                expandRows: true,
                locale: 'es',
                firstDay: 1,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek,dayGridMonth'
                },
                events: eventos,
                eventDisplay: 'block',

                // 👉 Click en una fecha
                dateClick: function(info) {
                    const fecha = info.dateStr.slice(0, 10);
                    const delDia = eventos.filter(ev => (ev.start || '').slice(0, 10) === fecha);
                    mostrarAvisoCalendario(info, delDia, fecha);
                }
            });

            cal.render();

            // 🪄 Popup personalizado dentro del calendario
            function mostrarAvisoCalendario(info, citas, fechaISO) {
                // Elimina cualquier popup anterior
                document.querySelectorAll('.popup-cita').forEach(p => p.remove());

                const popup = document.createElement('div');
                popup.classList.add('popup-cita');
                Object.assign(popup.style, {
                    position: 'absolute',
                    zIndex: '9999',
                    background: 'linear-gradient(135deg, #F8FAFC, #EEF2FF)',
                    border: '2px solid #6D28D9',
                    borderRadius: '12px',
                    boxShadow: '0 6px 16px rgba(109,40,217,0.25)',
                    padding: '12px 16px',
                    fontSize: '14px',
                    color: '#1E1B4B',
                    maxWidth: '260px',
                    pointerEvents: 'auto',
                    transition: 'opacity 0.3s ease, transform 0.3s ease',
                    opacity: '0',
                    transform: 'translateY(-6px)',
                    backdropFilter: 'blur(8px)'
                });

                // Contenido dinámico
                if (citas.length === 0) {
                    popup.innerHTML = `
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="bi bi-emoji-frown text-rose-600"></i>
                        <span>No tienes citas el <strong>${fechaISO}</strong>.</span>
                    </div>`;
                } else {
                    let citasHTML = "";
                    citas.forEach(ev => {
                        const e = ev.extendedProps || {};
                        const hora = ev.start?.slice(11, 16) || '--:--';
                        const tipo = e.tipoCita === 'virtual' ? '💻 Virtual' : '🏥 Presencial';
                        citasHTML += `
                        <div class="text-sm text-gray-700 mt-1">
                            • ${hora} — ${e.nombre || 'Sin nombre'}
                            <span class="italic text-gray-500">(${tipo})</span>
                        </div>`;
                    });

                    popup.innerHTML = `
                    <div class="flex flex-col text-gray-700">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="bi bi-check-circle-fill text-emerald-600"></i>
                            <strong>${citas.length}</strong> cita${citas.length > 1 ? 's' : ''} el ${fechaISO}
                        </div>
                        ${citasHTML}
                        <a href="#"
                           class="inline-block mt-3 self-center px-3 py-1.5 bg-gradient-to-r from-indigo-500 to-violet-500 text-white rounded-full text-xs font-medium shadow-md hover:scale-[1.05] hover:shadow-lg transition">
                           Ver detalles
                        </a>
                    </div>`;
                }

                // Posicionar sobre la celda
                const rect = info.dayEl.getBoundingClientRect();
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                const scrollLeft = window.scrollX || document.documentElement.scrollLeft;
                popup.style.top = `${rect.top + scrollTop + 45}px`;
                popup.style.left = `${rect.left + scrollLeft + rect.width / 2 - 110}px`;

                document.body.appendChild(popup);

                // Mostrar popup
                setTimeout(() => {
                    popup.style.opacity = '1';
                    popup.style.transform = 'translateY(0)';
                }, 50);

                // Cerrar si hace clic fuera
                document.addEventListener('click', function cerrar(e) {
                    if (!popup.contains(e.target)) {
                        popup.remove();
                        document.removeEventListener('click', cerrar);
                    }
                });

                // Desaparece automáticamente
                setTimeout(() => popup.remove(), 4000);
            }
        })();

        // === 🧭 Filtros client-side para tabla ===
        (function() {
            const btns = document.querySelectorAll('[data-filter]');
            const rows = document.querySelectorAll('#tbody-citas tr[data-tipo]');

            btns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const tipo = btn.getAttribute('data-filter');
                    rows.forEach(tr => {
                        tr.style.display = (tipo === 'all' || tr.getAttribute('data-tipo') ===
                            tipo) ? '' : 'none';
                    });
                    btns.forEach(b => b.classList.remove('ring-2', 'ring-violet-300', 'bg-violet-50'));
                    btn.classList.add('ring-2', 'ring-violet-300', 'bg-violet-50');
                });
            });
        })();

        // === ⚙️ Hooks de acciones ===
        (function() {
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-action]');
                if (!btn) return;
                const action = btn.getAttribute('data-action');
                const id = btn.getAttribute('data-id');
                if (!action || !id) return;
                console.log('Acción:', action, 'Cita:', id);
                // Aquí conectarás rutas reales: editar/reprogramar/cancelar
            });
        })();
    </script>



    <style>
        /* FullCalendar custom styles */
        .calendar-compact-theme .fc-toolbar-title {
            font-size: 0.95rem;
            color: #4F46E5;
            font-weight: 700;
        }

        .calendar-compact-theme .fc-button {
            background-color: #E0E7FF;
            color: #4338CA;
            border: none;
            border-radius: 0.6rem;
            padding: 0.25rem 0.6rem;
            font-size: 0.75rem;
        }

        .calendar-compact-theme .fc-button:hover {
            background-color: #C7D2FE;
        }

        .calendar-compact-theme .fc-daygrid-day {
            transition: background-color 0.2s ease;
        }

        .calendar-compact-theme .fc-daygrid-day:hover {
            background-color: #EEF2FF;
            cursor: pointer;
        }

        .calendar-compact-theme .fc-event {
            border-radius: 0.75rem;
            border: none !important;
            padding: 4px 6px;
            font-size: 0.7rem;
            background-color: #6D28D9 !important;
            color: white !important;
            box-shadow: 0 2px 6px rgba(109, 40, 217, 0.25);
        }

        .calendar-compact-theme .fc-day-today {
            background-color: #F5F3FF !important;
            border: 2px dashed #7C3AED;
        }

        /* Popup del calendario */
        .popup-cita {
            font-family: 'Inter', sans-serif;
            animation: fadeIn 0.25s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Popup del calendario médico */
        .popup-cita {
            animation: fadeSlideUp 0.3s ease forwards;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(-6px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
