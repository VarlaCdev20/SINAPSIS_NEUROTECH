<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Título -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center space-x-2">
            <i class="bi bi-person-badge text-blue-600"></i>
            <span>Lista de Roles del Sistema</span>
        </h2>

        <a href=""
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
            <i class="bi bi-plus-circle me-2"></i> Nuevo Rol
        </a>
    </div>

    <!-- Búsqueda y filtro -->
    <div class="flex items-center space-x-4 mb-4">
        <div class="flex items-center space-x-2">
            <label for="buscar" class="text-sm text-gray-700">Buscar:</label>
            <input id="buscar" type="search" placeholder="Nombre del rol..."
                class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                wire:model.live="search">
        </div>
        <div class="flex items-center space-x-2">
            <label for="n_registros" class="text-sm text-gray-700">Ver:</label>
            <select id="n_registros"
                class="border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                wire:model.live="n_registros">
                <option value="">Todo</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-gray-200">
        <div class="p-6">
            @if (session('message'))
                <div class="mb-4 bg-blue-100 text-blue-700 px-4 py-2 rounded-md">
                    {{ session('message') }}
                </div>
            @endif

            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="py-2 px-4 text-left">Nombre del Rol</th>
                        <th class="py-2 px-4 text-left">Permisos Asignados</th>
                        <th class="py-2 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $rol)
                        <tr class="hover:bg-blue-50">
                            <td class="px-6 py-3 font-medium">{{ $rol->name }}</td>
                            <td class="px-6 py-3">
                                @if ($rol->permissions->count() > 0)
                                    @foreach ($rol->permissions as $permiso)
                                        <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">
                                            {{ $permiso->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-gray-400 italic">Sin permisos</span>
                                @endif
                            </td>

                            <td class="px-6 py-3 text-center flex justify-center space-x-2">
                                <a href="" class="text-yellow-500 hover:text-yellow-600">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-6 text-gray-500 italic">
                                <i class="bi bi-exclamation-circle"></i> No hay roles registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-6 flex justify-center">
        {{ $roles->links() }}
    </div>
</div>
