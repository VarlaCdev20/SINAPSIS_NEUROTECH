<div class="p-6 bg-gray-50 min-h-screen">
    <!-- 🧠 Título -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center space-x-2">
            <i class="bi bi-heart-pulse text-blue-600"></i>
            <span>Lista de Pacientes</span>
        </h2>
    </div>

    <!-- 🔍 Búsqueda y filtros -->
    <div class="flex items-center space-x-4 mb-4">
        <div class="flex items-center space-x-2">
            <label for="buscar" class="text-sm text-gray-700">Buscar:</label>
            <input id="buscar" type="search" placeholder="Nombre, carnet, correo..."
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

    <!-- 🩺 Tabla -->
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
                        <th class="py-2 px-4 text-left">Código</th>
                        <th class="py-2 px-4 text-left">Nombre Completo</th>
                        <th class="py-2 px-4 text-left">Carnet</th>
                        <th class="py-2 px-4 text-left">Edad</th>
                        <th class="py-2 px-4 text-left">Correo</th>
                        <th class="py-2 px-4 text-left">Celular</th>
                        <th class="py-2 px-4 text-left">Dirección</th>
                        <th class="py-2 px-4 text-left">Médico Asignado</th>
                        <th class="py-2 px-4 text-center">Estado</th>
                        <th class="py-2 px-4 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pacientes as $paciente)
                        @php
                            $u = $paciente->usuario; // Relación con User
                            $m = $paciente->medico ?? null; // Relación con Médico
                        @endphp
                        <tr class="{{ $u && $u->estado === 0 ? 'opacity-60 text-gray-500' : 'hover:bg-blue-50' }}">
                            <td class="px-6 py-3 font-medium">{{ $paciente->cod_pac }}</td>
                            <td class="px-6 py-3">{{ $u ? "{$u->name} {$u->paterno} {$u->materno}" : '—' }}</td>
                            <td class="px-6 py-3">{{ $u->carnet ?? '—' }}</td>
                            <td class="px-6 py-3">
                                @if ($u && $u->fecha_nacimiento)
                                    {{ \Carbon\Carbon::parse($u->fecha_nacimiento)->age }} años
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-3">{{ $u->email ?? '—' }}</td>
                            <td class="px-6 py-3">{{ $u->celular ?? '—' }}</td>
                            <td class="px-6 py-3">{{ $u->direccion ?? '—' }}</td>
                            <td class="px-6 py-3">
                                @if ($m)
                                    <span class="text-gray-800 font-medium">{{ $m->usuario->name }}
                                        {{ $m->usuario->paterno }}</span>
                                @else
                                    <span class="text-gray-400 italic">No asignado</span>
                                @endif
                            </td>

                            <td class="px-6 py-3 text-center">
                                @if ($u && $u->estado === 1)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-md text-sm">Activo</span>
                                @else
                                    <span class="bg-gray-200 text-gray-600 px-2 py-1 rounded-md text-sm">Inactivo</span>
                                @endif
                            </td>

                            <td class="px-6 py-3 text-center flex justify-center space-x-2">
                                <!-- 👁️ Ver -->
                                <a href="#" class="text-blue-600 hover:text-blue-800 transition" title="Ver">
                                    <i class="bi bi-eye text-lg"></i>
                                </a>

                                <!-- ✏️ Editar (ENCRIPTADO) -->
                                @if ($u)
                                    <a href="{{ route('pacientes.editar', encrypt($u->cod_usu)) }}"
                                        class="text-yellow-500 hover:text-yellow-600 transition" title="Editar">
                                        <i class="bi bi-pencil-square text-lg"></i>
                                    </a>
                                @endif

                                <!-- 🔄 Cambiar estado -->
                                @if ($u)
                                    <button wire:click="estado('{{ $u->cod_usu }}')"
                                        class="transition {{ $u->estado === 1 ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}"
                                        title="{{ $u->estado === 1 ? 'Desactivar' : 'Activar' }}">
                                        <i
                                            class="bi {{ $u->estado === 1 ? 'bi-person-dash' : 'bi-person-check' }} text-lg"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-6 text-gray-500 italic">
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
