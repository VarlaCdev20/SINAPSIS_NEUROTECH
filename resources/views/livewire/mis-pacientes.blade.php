<div class="p-6 bg-gray-50 min-h-screen">

    <!-- 🧠 Título y botones -->
    <div class="flex flex-wrap items-center justify-between mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center space-x-3">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center space-x-2">
                <i class="bi bi-people-fill text-accent-600 text-3xl"></i>
                <span>Mis Pacientes Registrados</span>
            </h2>

            <span class="bg-blue-100 text-blue-700 text-sm font-semibold px-3 py-1 rounded-full shadow-sm">
                {{ $pacientes->total() }} paciente{{ $pacientes->total() !== 1 ? 's' : '' }}
            </span>
        </div>

        <div class="flex flex-wrap gap-3">

            @if ($haySinMedico)
                <button wire:click="confirmarAsignacion"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                    <i class="bi bi-link-45deg me-2"></i> Asignar sin médico
                </button>
            @endif

            <a href="{{ route('mis_pacientes.registrar') }}"
                class="inline-flex items-center px-4 py-2 bg-accent-600 text-white rounded-lg shadow hover:bg-accent-700 transition">
                <i class="bi bi-person-plus-fill me-2"></i> Nuevo Paciente
            </a>
        </div>
    </div>

    <!-- 🔍 Filtros -->
    <div class="flex flex-wrap items-center gap-4 mb-6">

        <div>
            <label class="text-gray-700 font-medium">Buscar:</label>
            <input type="search" wire:model.live="search" placeholder="Nombre o carnet..."
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-accent-200">
        </div>

        <div>
            <label class="text-gray-700 font-medium">Ver:</label>
            <select wire:model.live="n_registros"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-accent-200">
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
        <table class="min-w-full text-sm">
            <thead class="bg-accent-600 text-white text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Código</th>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Carnet</th>
                    <th class="px-4 py-3 text-left">Celular</th>
                    <th class="px-4 py-3 text-left">Dirección</th>
                    <th class="px-4 py-3 text-center">Registro</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($pacientes as $pac)
                    <tr class="{{ $pac->estado ? 'hover:bg-accent-50' : 'opacity-60 text-gray-500' }} transition">

                        <td class="px-4 py-3 font-semibold">{{ $pac->cod_pac }}</td>

                        <td class="px-4 py-3">
                            {{ $pac->name }} {{ $pac->paterno }} {{ $pac->materno }}
                        </td>

                        <td class="px-4 py-3">{{ $pac->carnet }}</td>

                        <td class="px-4 py-3">{{ $pac->celular }}</td>

                        <td class="px-4 py-3">{{ $pac->direccion }}</td>

                        <td class="px-4 py-3 text-center">
                            {{ $pac->created_at->format('d/m/Y') }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            @if ($pac->estado)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Activo</span>
                            @else
                                <span class="bg-gray-200 text-gray-600 px-2 py-1 rounded text-xs">Inactivo</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-center flex justify-center gap-3">

                            {{-- 📄 Ver historial (abre modal preview) --}}
                            <button wire:click="verHistorial('{{ $pac->cod_pac }}')" title="Ver historial"
                                class="text-blue-600 hover:text-blue-800 transition">
                                <i class="bi bi-clipboard2-pulse text-lg"></i>
                            </button>

                            {{-- ⚙ Activar / desactivar --}}
                            <button wire:click="estado('{{ $pac->cod_usu }}')"
                                class="{{ $pac->estado ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}"
                                title="{{ $pac->estado ? 'Desactivar' : 'Activar' }}">
                                <i class="bi {{ $pac->estado ? 'bi-person-dash' : 'bi-person-check' }} text-lg"></i>
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

    <!-- 🔄 Paginación -->
    <div class="mt-6 flex justify-center">
        {{ $pacientes->links() }}
    </div>

    {{-- ================================================================================= --}}
    {{--  🟣 MODAL PREVIEW HISTORIAL – FUNCIONAL 100% Y SIN "use" EN BLADE                 --}}
    {{-- ================================================================================= --}}

    @if ($mostrarHistorial && $pacienteSeleccionado)

        @php
            // 1️⃣ Obtener el código correctamente
            $codPac = is_object($pacienteSeleccionado) ? $pacienteSeleccionado->cod_pac ?? null : $pacienteSeleccionado;

            // 2️⃣ Buscar paciente completo
            $pac = \App\Models\Paciente::with('usuario')->where('cod_pac', $codPac)->first();

            $u = optional($pac)->usuario;

            // 3️⃣ Buscar último historial REAL en BD
            $h = \App\Models\Historial::where('cod_pac', $codPac)->orderBy('fec_cre_his', 'desc')->first();
        @endphp

        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden animate-fadeIn">

                {{-- Header --}}
                <div class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-500 flex justify-between items-center">
                    <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="bi bi-clipboard2-pulse"></i> Vista previa del historial
                    </h2>

                    <button wire:click="cerrarHistorial" class="text-purple-100 hover:text-white">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>

                {{-- Contenido --}}
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">

                    {{-- 🧍 Datos del paciente --}}
                    @if ($pac && $u)
                        <div class="bg-white p-4 border rounded-lg shadow-sm">
                            <h3 class="text-xs text-purple-700 font-semibold uppercase mb-1">Paciente</h3>

                            <p class="font-semibold text-gray-800">
                                {{ $u->name }} {{ $u->paterno }} {{ $u->materno }}
                            </p>

                            <p class="text-xs text-gray-600">
                                CI: {{ $u->carnet }} · Código: {{ $pac->cod_pac }}
                            </p>
                        </div>
                    @else
                        <div class="bg-white p-4 border rounded-lg text-center text-gray-500">
                            <i class="bi bi-person-exclamation text-3xl text-gray-400"></i>
                            <p class="mt-2 text-sm">No se pudo cargar la información del paciente.</p>
                        </div>
                    @endif

                    {{-- 📝 Último historial --}}
                    @if ($h)
                        <div class="bg-white p-4 border rounded-lg shadow-sm">

                            <h3 class="text-xs text-gray-600 font-semibold uppercase mb-2">Último registro</h3>

                            <p class="text-xs">
                                Fecha:
                                <strong>{{ \Carbon\Carbon::parse($h->fec_cre_his)->format('d/m/Y') }}</strong>
                            </p>

                            <div class="grid grid-cols-2 gap-2 text-xs my-2">
                                <p><strong>Peso:</strong> {{ $h->pes_his ?? '—' }} kg</p>
                                <p><strong>Altura:</strong> {{ $h->alt_his ?? '—' }} m</p>
                                <p><strong>Sangre:</strong> {{ $h->tip_san_his ?? '—' }}</p>
                                <p><strong>Alergias:</strong> {{ $h->ale_his ?? 'N/A' }}</p>
                            </div>

                            <p class="text-xs">
                                <strong>Observaciones:</strong><br>
                                {{ \Illuminate\Support\Str::limit($h->obs_gen_his ?? '—', 120, '...') }}
                            </p>

                        </div>
                    @else
                        <div class="bg-white p-4 border rounded-lg text-center text-gray-500">
                            <i class="bi bi-clipboard-x text-3xl text-gray-400"></i>
                            <p class="mt-2 text-sm">Este paciente no tiene historial registrado.</p>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="p-4 border-t bg-white flex justify-between">
                    <button wire:click="cerrarHistorial" class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                        Cerrar
                    </button>

                    @if ($codPac)
                        <a href="{{ route('medico.historial', ['paciente' => $codPac]) }}"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow flex items-center gap-2">
                            Ver historial completo
                            <i class="bi bi-arrow-right-circle"></i>
                        </a>
                    @endif
                </div>

            </div>
        </div>

    @endif




</div>


{{-- ================================================================================= --}}
{{-- 🟣 SCRIPTS LIVEWIRE SWEETALERT -- TODO AQUÍ ABAJO                                 --}}
{{-- ================================================================================= --}}
<script>
    document.addEventListener("livewire:initialized", () => {

        Livewire.on('confirmarAsignacion', data => {
            const pacientes = data.pacientes;
            const lista = pacientes.map(p => `<li>${p}</li>`).join('');

            Swal.fire({
                title: "¿Asignar pacientes sin médico?",
                html: `<ul style='text-align:left;'>${lista}</ul>`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Sí, asignar",
                cancelButtonText: "Cancelar",
            }).then(res => {
                if (res.isConfirmed) Livewire.dispatch("asignarPacientesSinMedico");
            });
        });

        Livewire.on('swal', data => {
            Swal.fire({
                icon: data.icon,
                title: data.title,
                text: data.text,
                confirmButtonColor: "#7c3aed"
            });
        });

    });
</script>
