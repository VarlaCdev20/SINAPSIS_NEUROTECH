<div class="p-6 bg-gradient-to-br from-gray-50 via-violet-50 to-indigo-50 min-h-[90vh]">

    {{-- 🧭 ENCABEZADO --}}
    <div class="relative mb-8 rounded-3xl bg-gradient-to-r from-violet-700 via-indigo-700 to-purple-700 text-white shadow-xl p-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h2 class="text-3xl md:text-4xl font-extrabold leading-tight">
                    Agenda Administrativa
                </h2>
                <p class="text-white/90 mt-1">Supervisión global — médicos, pacientes y solicitantes</p>

                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div class="bg-white/15 px-4 py-2 rounded-xl backdrop-blur shadow-inner">
                        <span class="text-white/80">Citas en rango</span>
                        <div class="text-2xl font-extrabold">{{ $total }}</div>
                    </div>
                    <div class="bg-white/15 px-4 py-2 rounded-xl backdrop-blur shadow-inner">
                        <span class="text-white/80">Prom. atención</span>
                        <div class="text-2xl font-extrabold text-emerald-200">{{ $promedioAtencion }}%</div>
                    </div>
                    <div class="bg-white/15 px-4 py-2 rounded-xl backdrop-blur shadow-inner">
                        <span class="text-white/80">Cancelaciones</span>
                        <div class="text-2xl font-extrabold text-rose-200">{{ $tasaCancel }}%</div>
                    </div>
                    <div class="bg-white/15 px-4 py-2 rounded-xl backdrop-blur shadow-inner">
                        <span class="text-white/80">Semana</span>
                        <div class="text-xs">{{ \Carbon\Carbon::parse($fechaInicio)->translatedFormat('d M') }} — {{ \Carbon\Carbon::parse($fechaFin)->translatedFormat('d M') }}</div>
                    </div>
                </div>
            </div>

            {{-- 🔍 Buscador y filtros --}}
            <div class="w-full md:w-auto space-y-3">
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.400ms="buscar"
                           placeholder="Buscar por médico, paciente, solicitante, motivo o código"
                           class="w-full md:w-[28rem] rounded-2xl border border-white/20 bg-white/10 text-white placeholder-white/70 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                    <i class="bi bi-search absolute right-3 top-2.5 text-white/70"></i>
                </div>

                <div class="flex flex-wrap gap-3">
                    <input type="date" wire:model.live="fechaInicio"
                           class="rounded-xl border border-white/20 bg-white/10 text-white px-3 py-2 placeholder-white/70">
                    <input type="date" wire:model.live="fechaFin"
                           class="rounded-xl border border-white/20 bg-white/10 text-white px-3 py-2">

                    <select wire:model.live="filtroMedico"
                            class="rounded-xl border border-white/20 bg-white/10 text-white/90 px-3 py-2">
                        <option value="">Todos los médicos</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m['cod'] }}">{{ $m['nombre'] }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="filtroEstado"
                            class="rounded-xl border border-white/20 bg-white/10 text-white/90 px-3 py-2">
                        <option value="">Estado</option>
                        <option value="programada">Programada</option>
                        <option value="atendida">Atendida</option>
                        <option value="cancelada">Cancelada</option>
                    </select>

                    <select wire:model.live="filtroTipo"
                            class="rounded-xl border border-white/20 bg-white/10 text-white/90 px-3 py-2">
                        <option value="">Tipo</option>
                        <option value="presencial">Presencial</option>
                        <option value="virtual">Virtual</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- 📈 Mini tendencia (últimos 7 días) --}}
    <div class="bg-white rounded-2xl shadow border border-violet-100 p-5 mb-8">
        <h3 class="text-sm font-bold text-indigo-700 mb-3 flex items-center gap-2">
            <i class="bi bi-activity"></i> Tendencia de citas (7 días)
        </h3>
        <canvas id="spark-citas" class="w-full h-24"></canvas>
    </div>

    {{-- 🗓️ Calendario semanal compacto --}}
    <div class="mb-10">
        <h3 class="text-lg font-bold text-indigo-700 mb-4 flex items-center gap-2">
            <i class="bi bi-calendar-week"></i> Semana en vista
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-4">
            @foreach ($this->semana as $fecha)
                @php
                    $citasDia = $citas->filter(fn($c) => \Carbon\Carbon::parse($c->fec_cit)->isSameDay($fecha));
                    $cnt = $citasDia->count();
                    $atd = $citasDia->filter(fn($c)=>strtolower($c->est_cit ?? '')==='atendida')->count();
                    $can = $citasDia->filter(fn($c)=>strtolower($c->est_cit ?? '')==='cancelada')->count();
                @endphp

                <div class="bg-white rounded-2xl p-4 border border-violet-100 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-semibold text-slate-800">{{ $fecha->translatedFormat('D d') }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-bold">{{ $cnt }} citas</span>
                    </div>

                    @forelse ($citasDia->take(4) as $c)
                        @php
                            $hora = \Carbon\Carbon::parse($c->fec_cit)->format('H:i');
                            $estado = strtolower($c->est_cit ?? 'programada');
                            $badge = match($estado){
                                'atendida' => 'bg-emerald-100 text-emerald-700',
                                'cancelada'=> 'bg-rose-100 text-rose-700',
                                default     => 'bg-blue-100 text-blue-700',
                            };
                            $esPaciente = (bool)($c->cod_pac ?? $c->COD_PAC);

                            $nombre = $esPaciente
                                ? ($c->paciente->usuario->name ?? 'Paciente')
                                : (trim(($c->solicitante->nom_sol ?? '').' '.($c->solicitante->ap_pat_sol ?? ' ').' '.($c->solicitante->ap_mat_sol ?? '')) ?: 'Solicitante');

                            $medico = $c->medico->usuario->name ?? '—';
                        @endphp
                        <button
                            wire:click="openDetalle('{{ $c->cod_cit ?? $c->COD_CIT }}')"
                            class="w-full text-left text-sm bg-slate-50 hover:bg-indigo-50 border border-slate-200 rounded-lg px-3 py-2 mb-2 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-800 truncate">{{ $nombre }}</span>
                                <span class="text-xs">{{ $hora }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span class="truncate">Dr(a). {{ $medico }}</span>
                                <span class="ml-2 px-2 py-0.5 rounded-full {{ $badge }}">{{ ucfirst($estado) }}</span>
                            </div>
                        </button>
                    @empty
                        <p class="text-xs text-slate-400 italic">Sin citas</p>
                    @endforelse

                    @if ($citasDia->count() > 4)
                        <p class="text-[11px] text-slate-500 mt-1">+ {{ $citasDia->count() - 4 }} más…</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- 🪟 Modal Detalle --}}
    <div x-data="{ open: @entangle('showModal') }" x-show="open" x-transition.opacity
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div @click.away="open=false" class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-xl relative">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-bold text-indigo-700"><i class="bi bi-clipboard2-pulse"></i> Detalle de cita</h3>
                <button @click="open=false" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>

            @if ($detalle)
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500">Código</p>
                        <p class="font-semibold">{{ $detalle['cod_cit'] }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Fecha / Hora</p>
                        <p class="font-semibold">{{ $detalle['fecha'] }} — {{ $detalle['hora'] }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Médico</p>
                        <p class="font-semibold">Dr(a). {{ $detalle['medico'] }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">{{ $detalle['esPaciente'] ? 'Paciente' : 'Solicitante' }}</p>
                        <p class="font-semibold">{{ $detalle['persona'] }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Estado</p>
                        <p class="font-semibold">{{ $detalle['estado'] }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Tipo</p>
                        <p class="font-semibold">{{ $detalle['tipo'] }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-slate-500">Motivo</p>
                        <p class="font-semibold">{{ $detalle['motivo'] }}</p>
                    </div>
                </div>
            @else
                <p class="text-slate-500 text-sm">Cargando…</p>
            @endif

            <div class="mt-5 flex justify-end gap-3">
                <button @click="open=false" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Cerrar</button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("livewire:load", () => {
    const ctx = document.getElementById("spark-citas");
    if (!ctx) return;

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 120);
    gradient.addColorStop(0, 'rgba(99,102,241,0.45)'); // indigo-500
    gradient.addColorStop(1, 'rgba(99,102,241,0.05)');

    const chart = new Chart(ctx, {
        type: "line",
        data: {
            labels: @json($this->tendenciaLabels),
            datasets: [{
                label: "Citas",
                data: @json($this->tendenciaDatos),
                borderColor: "#8B5CF6",          // violet-500
                backgroundColor: gradient,
                tension: 0.35,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }},
            scales: {
                x: { display: true, grid: { display: false }},
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.05)'} }
            }
        }
    });

    // Cuando Livewire refresque (por filtros), actualizamos dataset
    Livewire.hook('morph.updated', () => {
        chart.data.labels = @this.get('tendenciaLabels');
        chart.data.datasets[0].data = @this.get('tendenciaDatos');
        chart.update();
    });
});
</script>
@endpush
