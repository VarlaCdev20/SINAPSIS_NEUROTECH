<div> {{-- ✅ Elemento raíz único del componente --}}

    <div class="p-6 bg-gray-50 min-h-screen">

        <!-- 🧠 Título -->
        <div class="flex flex-wrap items-center justify-between mb-6 space-y-3 sm:space-y-0">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center space-x-2">
                <i class="bi bi-envelope-check-fill text-accent-600 text-3xl"></i>
                <span>Gestión de Solicitudes</span>
            </h2>
        </div>


        <!-- 🔍 Búsqueda, filtros y exportación en un solo div -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">

            <!-- Grupo: Buscar + Número de registros + Asignar cita -->
            <div class="flex flex-wrap items-center gap-4">

                <!-- Buscar -->
                <div class="flex items-center gap-2 bg-white px-3 py-2 border border-gray-300 rounded-lg shadow-sm">
                    <i class="bi bi-search text-accent-600 text-lg"></i>
                    <input type="search" wire:model.live="buscar" placeholder="Nombre, código o correo..."
                        class="focus:outline-none bg-transparent text-gray-700 w-44 sm:w-64">
                </div>

                <!-- Ver registros -->
                <div class="flex items-center space-x-2">
                    <label class="text-gray-700 font-medium">Ver:</label>
                    <select wire:model.live='n_registros'
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-accent-200">
                        <option value="">Todo</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <button wire:click="$dispatchTo('agenda-solicitante', 'abrirAgenda')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-accent-600 text-white font-semibold shadow-md hover:bg-accent-700 focus:ring-2 focus:ring-accent-300 transition">
                    <i class="bi bi-calendar2-plus-fill text-lg"></i>
                    <span class="hidden sm:inline">Asignar Cita</span>
                </button>



            </div>

            <!-- Exportar -->
            <div class="relative inline-block text-left">
                <button id="exportarBtn"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 font-medium hover:bg-accent-100 focus:outline-none focus:ring-2 focus:ring-accent-300 transition-all duration-200">
                    <i class="bi bi-arrow-down-square text-accent-600 text-lg"></i>
                    Exportar
                    <i class="bi bi-chevron-down text-gray-500 text-sm"></i>
                </button>

                <div id="exportarMenu"
                    class="hidden absolute right-0 mt-2 w-40 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                    <ul class="py-1">
                        <li>
                            <a href="/medico/solicitantes/exportar/csv"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-accent-100 hover:text-accent-600 transition-colors duration-200">
                                Exportar CSV
                            </a>
                        </li>
                        <li>
                            <a href="/medico/solicitantes/exportar/pdf"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-accent-100 hover:text-accent-600 transition-colors duration-200">
                                Exportar PDF
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <script>
            const btn = document.getElementById('exportarBtn');
            const menu = document.getElementById('exportarMenu');

            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });

            window.addEventListener('click', (e) => {
                if (!btn.contains(e.target)) menu.classList.add('hidden');
            });
        </script>


        <!-- 📋 TABLAS -->
        @php
            $pendientes = $solicitantes->where('est_sol', 'pendiente');
            $aprobados = $solicitantes->where('est_sol', 'aprobado');
            $rechazados = $solicitantes->where('est_sol', 'rechazado');
        @endphp

        {{-- 🔸 Pendientes --}}
        <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-gray-200 mb-10">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-accent-600 mb-4 flex items-center gap-2">
                    <i class="bi bi-hourglass-split"></i> Pendientes
                </h3>
                <table class="min-w-full border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <thead class="bg-yellow-500 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Código</th>
                            <th class="py-3 px-4 text-left">Nombre Completo</th>
                            <th class="py-3 px-4 text-left">Correo</th>
                            <th class="py-3 px-4 text-left">Descripción</th>
                            <th class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($pendientes as $s)
                            <tr class="hover:bg-yellow-50 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $s->cod_sol }}</td>
                                <td class="px-6 py-3">
                                    {{ trim(($s->nom_sol ?? '') . ' ' . ($s->ap_pat_sol ?? '') . ' ' . ($s->ap_mat_sol ?? '')) }}
                                </td>
                                <td class="px-6 py-3">{{ $s->email_sol ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    {{ \Illuminate\Support\Str::limit($s->des_sol ?? 'Sin descripción', 100) }}
                                </td>
                                <td class="px-6 py-3 text-center flex justify-center space-x-3">
                                    <button wire:click="aprobar('{{ $s->cod_sol }}')"
                                        class="text-green-600 hover:text-green-800 font-semibold inline-flex items-center space-x-1 transition"
                                        title="Aprobar">
                                        <i class="bi bi-check-circle-fill text-lg"></i>
                                        <span class="hidden sm:inline">Aprobar</span>
                                    </button>


                                    <!-- Rechazar -->
                                    <button wire:click="rechazar('{{ $s->cod_sol }}')"
                                        class="text-red-600 hover:text-red-800 font-semibold inline-flex items-center space-x-1 transition"
                                        title="Rechazar">
                                        <i class="bi bi-x-circle-fill text-lg"></i>
                                        <span class="hidden sm:inline">Rechazar</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-gray-500 italic">
                                    <i class="bi bi-exclamation-circle"></i> No hay solicitudes pendientes
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🟢 Aprobados --}}
        <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-gray-200 mb-10">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-green-600 mb-4 flex items-center gap-2">
                    <i class="bi bi-check2-circle"></i> Aprobados
                </h3>
                <table class="min-w-full border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <thead class="bg-green-600 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Código</th>
                            <th class="py-3 px-4 text-left">Nombre Completo</th>
                            <th class="py-3 px-4 text-left">Correo</th>
                            <th class="py-3 px-4 text-left">Descripción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($aprobados as $s)
                            <tr class="hover:bg-green-50 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $s->cod_sol }}</td>
                                <td class="px-6 py-3">{{ $s->nom_sol }} {{ $s->ap_pat_sol }} {{ $s->ap_mat_sol }}
                                </td>
                                <td class="px-6 py-3">{{ $s->email_sol ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    {{ \Illuminate\Support\Str::limit($s->des_sol ?? 'Sin descripción', 100) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-gray-500 italic">
                                    <i class="bi bi-check-circle"></i> No hay solicitudes aprobadas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🔴 Rechazados --}}
        <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-gray-200">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-red-600 mb-4 flex items-center gap-2">
                    <i class="bi bi-x-octagon"></i> Rechazados
                </h3>
                <table class="min-w-full border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <thead class="bg-red-600 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Código</th>
                            <th class="py-3 px-4 text-left">Nombre Completo</th>
                            <th class="py-3 px-4 text-left">Correo</th>
                            <th class="py-3 px-4 text-left">Descripción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($rechazados as $s)
                            <tr class="hover:bg-red-50 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $s->cod_sol }}</td>
                                <td class="px-6 py-3">{{ $s->nom_sol }} {{ $s->ap_pat_sol }} {{ $s->ap_mat_sol }}
                                </td>
                                <td class="px-6 py-3">{{ $s->email_sol ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    {{ \Illuminate\Support\Str::limit($s->des_sol ?? 'Sin descripción', 100) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-gray-500 italic">
                                    <i class="bi bi-x-circle"></i> No hay solicitudes rechazadas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @livewire('agenda-solicitante')


</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:init', () => {
        // ✅ Confirmación de aprobación
        Livewire.on('confirmarAprobacion', codSol => {
            Swal.fire({
                title: '¿Aprobar solicitud?',
                text: 'Se marcará como aprobada de inmediato.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b46c1',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    Livewire.dispatch('confirmadoAprobacion', codSol);
                }
            });
        });

        // ⚠️ Confirmación de rechazo
        Livewire.on('confirmarRechazo', codSol => {
            Swal.fire({
                title: '¿Rechazar solicitud?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e53e3e',
                cancelButtonColor: '#6b46c1',
                confirmButtonText: 'Sí, rechazar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    Livewire.dispatch('confirmadoRechazo', codSol);
                }
            });
        });
    });
</script>
<!-- 🌟 MODAL DE REGISTRO DE CITA -->