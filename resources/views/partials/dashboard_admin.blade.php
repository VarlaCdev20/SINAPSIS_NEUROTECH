{{-- =============================== --}}
{{-- DASHBOARD DEL ADMINISTRADOR     --}}
{{-- =============================== --}}

@php
    use Carbon\Carbon;
    $hora = Carbon::now()->format('H');
    $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
@endphp

<section class="p-8 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl shadow-lg space-y-6">
    {{-- ======================== --}}
    {{-- ENCABEZADO CON SALUDO   --}}
    {{-- ======================== --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl  text-blue-900">
                {{ $saludo }}, {{ Auth::user()->name ?? 'Administrador' }} 👋
            </h2>
        </div>
        <div class="bg-white shadow-md px-4 py-2 rounded-xl border border-gray-100 text-gray-700 text-sm">
            <i class="bi bi-calendar-week text-blue-600 mr-1"></i>
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- ======================== --}}
    {{-- TARJETAS RESUMEN GLOBAL --}}
    {{-- ======================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-6">
        {{-- Usuarios activos --}}
        <div class="bg-white rounded-2xl p-3 shadow-sm border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <h4 class="text-gray-500 text-sm uppercase">Usuarios activos</h4>
                <i class="bi bi-person-check text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-3xl font-bold text-green-700 mt-2">{{ $usuariosActivos ?? 0 }}</h3>
        </div>

        {{-- Usuarios desactivados --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-red-500">
            <div class="flex justify-between items-center">
                <h4 class="text-gray-500 text-sm uppercase">Usuarios desactivados</h4>
                <i class="bi bi-person-x text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-3xl font-bold text-red-700 mt-2">{{ $usuariosDesactivados ?? 0 }}</h3>
        </div>

        {{-- Solicitantes --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <h4 class="text-gray-500 text-sm uppercase">Solicitantes</h4>
                <i class="bi bi-hourglass-split text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-3xl font-bold text-blue-700 mt-2">{{ $totalSolicitantes ?? 0 }}</h3>
        </div>

        {{-- Médicos activos --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <h4 class="text-gray-500 text-sm uppercase">Médicos activos</h4>
                <i class="bi bi-clipboard2-pulse text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-3xl font-bold text-purple-700 mt-2">{{ $totalMedicos ?? 0 }}</h3>
        </div>

        {{-- Pacientes registrados --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-teal-500">
            <div class="flex justify-between items-center">
                <h4 class="text-gray-500 text-sm uppercase">Pacientes registrados</h4>
                <i class="bi bi-person-hearts text-teal-600 text-2xl"></i>
            </div>
            <h3 class="text-3xl font-bold text-teal-700 mt-2">{{ $totalPacientes ?? 0 }}</h3>
        </div>

        {{-- Citas totales --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-yellow-500">
            <div class="flex justify-between items-center">
                <h4 class="text-gray-500 text-sm uppercase">Citas totales</h4>
                <i class="bi bi-calendar2-check text-yellow-500 text-2xl"></i>
            </div>
            <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $totalCitas ?? 0 }}</h3>
        </div>
    </div>

    {{-- ======================== --}}
    {{-- GRÁFICOS PRINCIPALES    --}}
    {{-- ======================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Gráfico: Distribución de usuarios --}}
        <div class="bg-white rounded-2xl shadow-md p-3 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-1">
                <i class="bi bi-pie-chart-fill text-blue-300"></i>
                Distribución de Usuarios
            </h3>
            <canvas id="userStatusChart" height="140"></canvas>
        </div>

        {{-- Gráfico: Rendimiento médico --}}
        <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-bar-chart-line-fill text-purple-600"></i>
                Top 5 Médicos con más citas
            </h3>
            <canvas id="medicoRankingChart" height="140"></canvas>
        </div>
    </div>

    {{-- ======================== --}}
    {{-- ACTIVIDAD RECIENTE (Bitácora) --}}
    {{-- ======================== --}}
    <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="bi bi-clock-history text-blue-600"></i>
            Actividad reciente del sistema
        </h3>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-2 text-left">Fecha</th>
                    <th class="px-4 py-2 text-left">Usuario</th>
                    <th class="px-4 py-2 text-left">Acción</th>
                    <th class="px-4 py-2 text-left">Descripción</th>
                </tr>
            </thead>

            @php
                // Función auxiliar para determinar color, ícono y texto según la acción
                function estiloAccion($texto)
                {
                    $texto = strtolower($texto);

                    if (str_contains($texto, 'registró')) {
                        return ['color' => 'green', 'icon' => 'bi-plus-circle-fill', 'label' => 'Registro'];
                    } elseif (str_contains($texto, 'actualizó')) {
                        return ['color' => 'blue', 'icon' => 'bi-pencil-fill', 'label' => 'Actualización'];
                    } elseif (str_contains($texto, 'sincronizó')) {
                        return ['color' => 'indigo', 'icon' => 'bi-arrow-repeat', 'label' => 'Sincronización'];
                    } elseif (str_contains($texto, 'verificó')) {
                        return ['color' => 'teal', 'icon' => 'bi-check-circle-fill', 'label' => 'Verificación'];
                    } elseif (str_contains($texto, 'eliminó')) {
                        return ['color' => 'orange', 'icon' => 'bi-trash-fill', 'label' => 'Eliminación'];
                    } elseif (str_contains($texto, 'error')) {
                        return ['color' => 'red', 'icon' => 'bi-exclamation-triangle-fill', 'label' => 'Error'];
                    } else {
                        return ['color' => 'gray', 'icon' => 'bi-info-circle-fill', 'label' => 'Acción'];
                    }
                }
            @endphp

            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($bitacora as $evento)
                    @php
                        $estilo = estiloAccion($evento->acc_bit);
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($evento->fec_hor_bit)->format('d/m/Y H:i') }}
                        </td>

                        <td class="px-4 py-2 font-medium text-gray-800">
                            {{ $evento->usuario_nombre }}
                        </td>

                        <td class="px-4 py-2 flex items-center gap-2 text-{{ $estilo['color'] }}-600 font-semibold">
                            <i class="bi {{ $estilo['icon'] }}"></i>
                            {{ $estilo['label'] }}
                        </td>

                        <td class="px-4 py-2 text-gray-700">
                            {{ $evento->acc_bit }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-400 italic">
                            <i class="bi bi-inbox"></i> Aún no hay registros en la bitácora.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    {{-- ======================== --}}
    {{-- SOLICITUDES PENDIENTES --}}
    {{-- ======================== --}}
    <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="bi bi-envelope-open text-blue-600"></i>
            Solicitudes de registro pendientes
        </h3>

        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-2 text-left">Código</th>
                    <th class="px-4 py-2 text-left">Nombre Completo</th>
                    <th class="px-4 py-2 text-left">Correo</th>
                    <th class="px-4 py-2 text-left">Descripción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($solicitantesPendientes ?? [] as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $s->cod_sol }}</td>
                        <td class="px-4 py-2">
                            {{ $s->nom_sol }}
                            {{ $s->ap_pat_sol }}
                            {{ $s->ap_mat_sol }}
                        </td>
                        <td class="px-4 py-2">{{ $s->email_sol }}</td>
                        <td class="px-4 py-2">
                            {{ $s->des_sol ?? 'Sin descripción' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-400">
                            Sin solicitudes pendientes
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</section>

{{-- =============================== --}}
{{-- SCRIPTS DE GRÁFICOS Chart.js    --}}
{{-- =============================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Distribución de usuarios
        const ctx1 = document.getElementById('userStatusChart');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'pie',
                data: {
                    labels: ['Activos', 'Solicitantes', 'Desactivados'],
                    datasets: [{
                        data: [{{ $usuariosActivos ?? 0 }}, {{ $totalSolicitantes ?? 0 }},
                            {{ $usuariosDesactivados ?? 0 }}
                        ],
                        backgroundColor: ['#16a34a', '#2563eb', '#dc2626'],
                        hoverOffset: 8,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '1%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Ranking de médicos (dinámico desde DB)
        const ctx2 = document.getElementById('medicoRankingChart');

        @if (!empty($labelsMedicos) && !empty($valoresCitas))
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsMedicos) !!},
                    datasets: [{
                        label: 'Citas atendidas',
                        data: {!! json_encode($valoresCitas) !!},
                        backgroundColor: '#7e22ce',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#4c1d95',
                            titleColor: '#fff',
                            bodyColor: '#e0e7ff'
                        }
                    }
                }
            });
        @else
            if (ctx2) {
                ctx2.parentNode.innerHTML += `
            <p class="text-center text-gray-400 italic mt-4">
                No hay médicos ni citas registradas aún.
            </p>`;
            }
        @endif

    });
</script>
