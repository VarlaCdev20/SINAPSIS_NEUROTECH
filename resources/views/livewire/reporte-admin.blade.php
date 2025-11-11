<div class="p-6 bg-gray-50 min-h-screen" x-data="{ openFilter: false }">
    <!-- 🧠 ENCABEZADO -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('img/welcome/ICON.png') }}" alt="Logo SINAPSIS" class="w-12 h-12 rounded-lg shadow-md">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Reporte de Bitácora</h2>
                <p class="text-sm text-gray-500">Registro histórico de actividades en el sistema</p>
            </div>
        </div>

        <div class="flex items-center mt-4 sm:mt-0 space-x-3">
            <button wire:click="exportarPDF"
                class="flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-purple-700 transition">
                <i class="bi bi-file-earmark-pdf-fill mr-2"></i> Exportar PDF
            </button>

            <button wire:click="exportarExcel"
                class="flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700 transition">
                <i class="bi bi-file-earmark-excel-fill mr-2"></i> Exportar Excel
            </button>
        </div>
    </div>

    <!-- 🎚️ FILTROS -->
    <div class="bg-white rounded-xl shadow p-4 mb-8 border border-gray-100">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <label class="text-sm font-semibold text-gray-600">Desde</label>
                <input type="date" wire:model.live="fechaInicio"
                    class="mt-1 px-3 py-2 rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Hasta</label>
                <input type="date" wire:model.live="fechaFin"
                    class="mt-1 px-3 py-2 rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Rol</label>
                <select wire:model.live="rolUsuario"
                    class="mt-1 px-3 py-2 rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    <option value="todos">Todos</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Medico">Médico</option>
                    <option value="Paciente">Paciente</option>
                </select>
            </div>

            <button wire:click="filtrar"
                class="px-5 py-2 bg-purple-600 text-white font-semibold rounded-lg shadow hover:bg-purple-700 transition">
                <i class="bi bi-search mr-2"></i> Filtrar
            </button>
        </div>
    </div>

    <!-- 📊 GRÁFICO -->
    <div class="bg-white rounded-xl shadow p-5 mb-10 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="bi bi-bar-chart-line-fill text-purple-600 mr-2"></i>
            Actividad por Rol
        </h3>
        <canvas id="graficoRoles" height="100"></canvas>
    </div>

    <!-- 📋 TABLA DE BITÁCORA -->
    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-purple-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Fecha</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Usuario</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Rol</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bitacoras as $b)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($b->fec_hor_bit)->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $b->usuario->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-1 rounded-full text-xs font-semibold
                                @if ($b->usuario->hasRole('Administrador')) bg-purple-100 text-purple-700
                                @elseif($b->usuario->hasRole('Medico')) bg-blue-100 text-blue-700
                                @elseif($b->usuario->hasRole('Paciente')) bg-green-100 text-green-700
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ $b->usuario->getRoleNames()->first() ?? 'Sin rol' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $b->acc_bit }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">No hay registros en este rango.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 🪶 FOOTER -->
    <div class="text-right mt-4 text-gray-500 text-sm">
        Mostrando {{ $bitacoras->count() }} registro(s) — Actualizado:
        {{ now()->format('d/m/Y H:i') }}
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:load', () => {
                const ctx = document.getElementById('graficoRoles').getContext('2d');
                let chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                            label: 'Actividades registradas',
                            data: @json($data),
                            backgroundColor: ['#A78BFA', '#60A5FA', '#34D399'],
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
                                backgroundColor: '#6D28D9',
                                titleColor: '#fff',
                                bodyColor: '#fff'
                            }
                        }
                    }
                });

                Livewire.on('refreshChart', (data) => {
                    chart.data.datasets[0].data = data;
                    chart.update();
                });
            });
        </script>
    @endpush
</div>
