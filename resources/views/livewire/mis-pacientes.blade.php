<div class="p-6 bg-gray-50 min-h-screen">

    <!-- 🧠 Título y botones -->
    <div class="flex flex-wrap items-center justify-between mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center space-x-3">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center space-x-2">
                <i class="bi bi-people-fill text-accent-600 text-3xl"></i>
                <span>Mis Pacientes Registrados</span>
            </h2>

            <!-- 👥 Contador de pacientes -->
            <span class="bg-blue-100 text-blue-700 text-sm font-semibold px-3 py-1 rounded-full shadow-sm">
                {{ $pacientes->total() }} paciente{{ $pacientes->total() !== 1 ? 's' : '' }}
            </span>
        </div>

        <div class="flex flex-wrap gap-3">
            <!-- 🩺 Botón visible solo si hay pacientes sin médico -->
            @if ($haySinMedico)
                <button wire:click="confirmarAsignacion"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                    <i class="bi bi-link-45deg me-2"></i> Asignar sin médico
                </button>
            @endif

            <!-- ➕ Botón Nuevo Paciente -->
            <a href="{{ route('mis_pacientes.registrar') }}"
                class="inline-flex items-center px-4 py-2 bg-accent-600 text-white rounded-lg shadow hover:bg-accent-700 transition">
                <i class="bi bi-person-plus-fill me-2"></i> Nuevo Paciente
            </a>
        </div>
    </div>


    <!-- 🔍 Búsqueda -->
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <div class="flex items-center space-x-2">
            <label class="text-gray-700 font-medium">Buscar:</label>
            <input type="search" wire:model.live="search" placeholder="Nombre o carnet..."
                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-accent-200">
        </div>

        <div class="flex items-center space-x-2">
            <label class="text-gray-700 font-medium">Ver:</label>
            <select wire:model.live="n_registros"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-accent-200">
                <option value="">Todo</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <!-- 📋 Tabla -->
    <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-gray-200">
        <div class="p-6">
            <table class="min-w-full border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                <thead class="bg-accent-600 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Código</th>
                        <th class="py-3 px-4 text-left">Nombre Completo</th>
                        <th class="py-3 px-4 text-left">Carnet</th>
                        <th class="py-3 px-4 text-left">Celular</th>
                        <th class="py-3 px-4 text-left">Dirección</th>
                        <th class="py-3 px-4 text-center">Fecha de Registro</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($pacientes as $pac)
                        <tr
                            class="{{ $pac->estado === 0 ? 'opacity-60 text-gray-500' : 'hover:bg-accent-50 transition-colors' }}">
                            <td class="px-6 py-3 font-medium">{{ $pac->cod_pac }}</td>
                            <td class="px-6 py-3">{{ $pac->name }} {{ $pac->paterno }} {{ $pac->materno }}</td>
                            <td class="px-6 py-3">{{ $pac->carnet }}</td>
                            <td class="px-6 py-3">{{ $pac->celular }}</td>
                            <td class="px-6 py-3">{{ $pac->direccion }}</td>
                            <td class="px-6 py-3 text-center">{{ $pac->created_at->format('d/m/Y') }}</td>

                            <td class="px-6 py-3 text-center">
                                @if ($pac->estado)
                                    <span
                                        class="bg-green-100 text-green-700 px-2 py-1 rounded-md text-sm font-semibold">Activo</span>
                                @else
                                    <span
                                        class="bg-gray-200 text-gray-600 px-2 py-1 rounded-md text-sm font-semibold">Inactivo</span>
                                @endif
                            </td>

                            <td class="px-6 py-3 text-center flex justify-center space-x-3">
                                <!-- 📄 Historial -->
                                <a href="#" class="text-blue-600 hover:text-blue-800 transition"
                                    title="Ver historial">
                                    <i class="bi bi-clipboard2-pulse text-lg"></i>
                                </a>

                                <!-- ⚙️ Desactivar / Activar -->
                                <button wire:click="estado('{{ $pac->cod_usu }}')"
                                    title="{{ $pac->estado ? 'Desactivar' : 'Activar' }}"
                                    class="transition {{ $pac->estado ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}">
                                    <i
                                        class="bi {{ $pac->estado ? 'bi-person-dash' : 'bi-person-check' }} text-lg"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-500 italic">
                                <i class="bi bi-exclamation-circle"></i> No hay pacientes registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 🔄 Paginación -->
    <div class="mt-6 flex justify-center">
        {{ $pacientes->links() }}
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {

        // 🧩 Mostrar la lista de pacientes sin médico antes de asignar
        Livewire.on('confirmarAsignacion', (data) => {
            const pacientes = data?.pacientes || data?.[0]?.pacientes || [];
            const lista = pacientes.map(p => `<li>${p}</li>`).join('');
            const cantidad = pacientes.length;

            Swal.fire({
                title: `¿Desea registrar a los siguientes ${cantidad} paciente${cantidad !== 1 ? 's' : ''} como suyos?`,
                html: `<ul style="text-align:left; list-style:disc; margin-left:20px;">${lista}</ul>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, asignar ahora',
                cancelButtonText: 'Cancelar',
            }).then(result => {
                if (result.isConfirmed) {
                    Livewire.dispatch(
                    'asignarPacientesSinMedico'); // ✅ ejecuta el método del componente
                }
            });
        });

        // ⚡ Alertas normales
        Livewire.on('swal', data => {
            Swal.fire({
                icon: data.icon,
                title: data.title,
                text: data.text,
                confirmButtonColor: '#3085d6',
            });
        });
    });
</script>
