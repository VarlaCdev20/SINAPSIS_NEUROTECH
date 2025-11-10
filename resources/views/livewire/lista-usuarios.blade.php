<div class="p-6 bg-gray-50 min-h-screen">

    <!-- Título y acciones -->
    <div class="flex flex-wrap items-center justify-between mb-6 space-y-3 sm:space-y-0">
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center space-x-2">
            <i class="bi bi-people-fill text-blue-600 text-3xl"></i>
            <span>Lista de Usuarios del Sistema</span>
        </h2>

        <div class="flex items-center space-x-3">
            <!-- Botón registrar -->
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                <i class="bi bi-person-plus-fill me-2"></i> Registrar Usuario
            </a>

            <!-- Botón sincronizar -->
            <form action="{{ route('users.refreshRoles') }}" method="POST" id="refreshForm">
                @csrf
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition font-semibold">
                    <i class="bi bi-arrow-repeat me-2"></i> Actualizar Códigos y Roles
                </button>
            </form>
        </div>
    </div>

    <!-- Barra de búsqueda -->
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <div class="flex items-center space-x-2">
            <label class="text-gray-700 font-medium">Buscar:</label>
            <input type="search" wire:model.live='search' placeholder="Carnet"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-200">
        </div>

        <div class="flex items-center space-x-2">
            <label class="text-gray-700 font-medium">Ver:</label>
            <select wire:model.live='n_registros'
                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-200">
                <option value="">Todo</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-gray-200">
        <div class="p-6">

            <table class="min-w-full border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Nombre</th>
                        <th class="py-3 px-4 text-left">Carnet</th>
                        <th class="py-3 px-4 text-left">Correo</th>
                        <th class="py-3 px-4 text-left">Dirección</th>
                        <th class="py-3 px-4 text-left">Celular</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                        <th class="py-3 px-4 text-center">Rol</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $item)
                        <tr
                            class="{{ $item->estado === 0 ? 'opacity-60 text-gray-500' : 'hover:bg-blue-50 transition-colors' }}">
                            <td class="px-6 py-3 font-medium">{{ $item->name }} {{ $item->paterno }}
                                {{ $item->materno }}</td>
                            <td class="px-6 py-3">{{ $item->carnet }}</td>
                            <td class="px-6 py-3">{{ $item->email }}</td>
                            <td class="px-6 py-3">{{ $item->direccion }}</td>
                            <td class="px-6 py-3">{{ $item->celular }}</td>

                            <td class="px-6 py-3 text-center">
                                @if ($item->estado === 1)
                                    <span
                                        class="bg-green-100 text-green-700 px-2 py-1 rounded-md text-sm font-semibold">
                                        Activo
                                    </span>
                                @else
                                    <span class="bg-gray-200 text-gray-600 px-2 py-1 rounded-md text-sm font-semibold">
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-3 text-center">
                                @foreach ($item->roles as $role)
                                    <span
                                        class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>

                            <td class="px-6 py-3 text-center flex justify-center space-x-2">
                                <a href="{{ route('users.edit', encrypt($item->id)) }}"
                                    class="text-blue-600 hover:text-blue-800 transition"
                                    @if ($item->estado !== 1) onclick="return false;" @endif>
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>

                                <a href="{{ route('users.edit', encrypt($item->id)) }}"
                                    class="text-indigo-600 hover:text-indigo-800 transition"
                                    @if ($item->estado !== 1) onclick="return false;" @endif>
                                    <i class="bi bi-card-checklist text-lg"></i>
                                </a>

                                <button wire:click="estado('{{ $item->cod_usu }}')"
                                    title="{{ auth()->user()->cod_usu === $item->cod_usu ? 'No puedes desactivarte a ti misma' : ($item->estado === 1 ? 'Desactivar' : 'Activar') }}"
                                    class="transition
        {{ $item->estado === 1 ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}
        {{ auth()->user()->cod_usu === $item->cod_usu ? 'opacity-40 cursor-not-allowed pointer-events-none' : '' }}"
                                    @if (auth()->user()->cod_usu === $item->cod_usu) disabled @endif>
                                    <i
                                        class="bi {{ $item->estado === 1 ? 'bi-person-dash' : 'bi-person-check' }} text-lg"></i>
                                </button>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-500 italic">
                                <i class="bi bi-exclamation-circle"></i> No hay usuarios registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-6 flex justify-center">
        {{ $users->links() }}
    </div>
</div>

<!-- SweetAlert2 -->

<script>
    document.getElementById('refreshForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Deseas sincronizar los registros?',
            text: 'Esto creará los códigos faltantes en las tablas correspondientes.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, sincronizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>

@if (session('success'))
    <script>
        Swal.fire({
            title: '¡Proceso completado!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#28a745'
        });
    </script>
@endif

@if (session('info'))
    <script>
        Swal.fire({
            title: 'Sin cambios',
            text: "{{ session('info') }}",
            icon: 'info',
            confirmButtonColor: '#007bff'
        });
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            title: 'Error',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    </script>
@endif
