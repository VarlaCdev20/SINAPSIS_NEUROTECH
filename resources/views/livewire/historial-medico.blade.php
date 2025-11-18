<div class="p-6 bg-gray-50 min-h-screen">

    {{-- 🔵 ENCABEZADO PRINCIPAL --}}
    <div class="flex flex-wrap items-start justify-between mb-6 gap-4">

        {{-- 🧠 Títulos y descripción --}}
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="bi bi-clipboard2-pulse text-purple-600 text-3xl drop-shadow-sm"></i>
                <span>Historiales Médicos</span>
            </h1>
            <p class="text-sm text-gray-500">
                Administración, registro y seguimiento clínico de los pacientes.
            </p>
        </div>

        {{-- 📈 Gráfico pequeño + resumen --}}
        <div class="w-full sm:w-72 lg:w-80 bg-white border border-gray-200 rounded-2xl shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-semibold text-gray-600 flex items-center gap-1">
                    <i class="bi bi-bar-chart-line text-purple-600"></i>
                    Historiales registrados ({{ date('Y') }})
                </h3>
            </div>

            <div class="flex items-center gap-3 mb-2">
                <div class="flex-1">
                    <canvas id="graficoHistoriales" class="w-full h-24" wire:ignore></canvas>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-[11px] mt-1">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-500 shadow-sm"></span>
                    <span class="text-gray-500">Total historiales:</span>
                    <span class="font-semibold text-gray-800">{{ $totalHistoriales ?? '0' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-fuchsia-500 shadow-sm"></span>
                    <span class="text-gray-500">Este mes:</span>
                    <span class="font-semibold text-gray-800">{{ $historialesEsteMes ?? '0' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-sm"></span>
                    <span class="text-gray-500">Pacientes:</span>
                    <span class="font-semibold text-gray-800">{{ $totalPacientes ?? '0' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shadow-sm"></span>
                    <span class="text-gray-500">Sin historial:</span>
                    <span class="font-semibold text-gray-800">{{ $pacientesSinHistorial ?? '0' }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ⭐ MINI-RESUMEN DE ESTADO ABAJO DEL HEADER --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
        <div class="bg-white border border-purple-100 rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 flex items-center justify-center rounded-full bg-purple-50">
                <i class="bi bi-clipboard-pulse text-purple-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Historiales activos</p>
                <p class="text-base font-semibold text-gray-800">
                    {{ $totalHistoriales ?? '0' }}
                </p>
            </div>
        </div>
        <div class="bg-white border border-fuchsia-100 rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 flex items-center justify-center rounded-full bg-fuchsia-50">
                <i class="bi bi-calendar2-week text-fuchsia-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Registrados este mes</p>
                <p class="text-base font-semibold text-gray-800">
                    {{ $historialesEsteMes ?? '0' }}
                </p>
            </div>
        </div>
        <div class="bg-white border border-emerald-100 rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 flex items-center justify-center rounded-full bg-emerald-50">
                <i class="bi bi-people text-emerald-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Pacientes sin historial</p>
                <p class="text-base font-semibold text-gray-800">
                    {{ $pacientesSinHistorial ?? '0' }}
                </p>
            </div>
        </div>
    </div>

    {{-- 🔍 SEGUNDA FILA: Buscador + Filtros + Vista + Botón --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">

        {{-- Búsqueda --}}
        <div class="flex-1 min-w-[240px]">
            <label class="block text-gray-700 text-sm font-medium mb-1">Buscar historial</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-purple-400">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" wire:model.live="buscar"
                    placeholder="Buscar por paciente, CI, código de historial..."
                    class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
            </div>
        </div>

        {{-- Selector de registros --}}
        <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Mostrar</label>
            <select wire:model="n_registros"
                class="border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-purple-500 focus:border-purple-500 shadow-sm bg-white">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
            </select>
        </div>

        {{-- Cambiar vista --}}
        <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Vista</label>
            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-full px-2 py-1 shadow-sm">
                <button type="button" wire:click="cambiarVista('cards')"
                    class="p-2 rounded-full {{ $vista === 'cards' ? 'bg-purple-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>

                <button type="button" wire:click="cambiarVista('tabla')"
                    class="p-2 rounded-full {{ $vista === 'tabla' ? 'bg-purple-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="bi bi-table"></i>
                </button>
            </div>
        </div>

        {{-- Botón para nuevo historial --}}
        <div>
            <label class="block text-sm invisible">.</label>
            <button type="button" wire:click="abrirModalCrear"
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow flex items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i>
                Nuevo historial
            </button>
        </div>

    </div>

    {{-- 🧾 LISTADO DE HISTORIALES --}}
    @php use Illuminate\Support\Str; @endphp

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4">

        {{-- Si no hay --}}
        @if ($historiales->count() === 0)
            <div class="py-10 text-center text-gray-500">
                <i class="bi bi-clipboard2-x text-4xl text-gray-300 mb-2"></i>
                <p>No se encontraron historiales.</p>
            </div>
        @else
            {{-- Vista Cards --}}
            @if ($vista === 'cards')

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

                    @foreach ($historiales as $his)
                        @php
                            $pac = $his->paciente;
                            $u = $pac->usuario ?? null;
                        @endphp

                        <div
                            class="p-4 border border-purple-100 rounded-2xl bg-gradient-to-b from-purple-50/60 via-white to-white shadow-sm hover:shadow-lg transition transform hover:-translate-y-[2px]">

                            {{-- Encabezado --}}
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-xs font-semibold text-purple-600 tracking-wide">
                                        cod: {{ $his->cod_his }}
                                    </p>
                                    <h2 class="font-semibold text-gray-800">
                                        @if ($u)
                                            {{ $u->name }} {{ $u->paterno }} {{ $u->materno }}
                                        @else
                                            Paciente {{ $his->cod_pac }}
                                        @endif
                                    </h2>
                                    <p class="text-xs text-gray-500">
                                        CI:
                                        @if ($u)
                                            {{ $u->carnet }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>

                                <div class="text-right space-y-1">
                                    <span
                                        class="px-2 py-0.5 text-[11px] rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                                        {{ $his->tip_san_his ?? 'Sin tipo' }}
                                    </span>
                                    <p class="text-[11px] text-gray-400">
                                        {{ \Carbon\Carbon::parse($his->fec_cre_his)->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Datos rápidos --}}
                            <div class="flex flex-wrap gap-2 text-xs text-gray-600 mb-3">
                                <span class="px-2 py-0.5 rounded-full border bg-white/80">
                                    Peso: <strong>{{ $his->pes_his ?? '—' }} kg</strong>
                                </span>
                                <span class="px-2 py-0.5 rounded-full border bg-white/80">
                                    Altura: <strong>{{ $his->alt_his ?? '—' }} m</strong>
                                </span>
                            </div>

                            {{-- Observaciones --}}
                            <p class="text-xs text-gray-500 mb-3 line-clamp-2">
                                {{ $his->obs_gen_his ?? 'Sin observaciones registradas.' }}
                            </p>

                            {{-- Chips de info extra --}}
                            <div class="flex flex-wrap gap-2 text-[11px] text-gray-500 mb-3">

                                @if ($his->ant_per_his)
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-100">
                                        Antecedentes personales
                                    </span>
                                @endif

                                {{-- Alergias mejoradas --}}
                                @if ($his->ale_his)
                                    <span title="{{ $his->ale_his }}"
                                        class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 cursor-help">
                                        Alergias: {{ Str::limit($his->ale_his, 25) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Acciones --}}
                            <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                                <button wire:click="abrirModalDetalle('{{ $his->cod_his }}')"
                                    class="text-xs text-purple-600 hover:text-purple-800 flex items-center gap-1">
                                    <i class="bi bi-eye-fill"></i> Ver detalle
                                </button>

                                <a href="#"
                                    class="text-xs text-gray-600 hover:text-gray-800 flex items-center gap-1">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                {{-- Vista Tabla --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 text-xs uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Código</th>
                                <th class="px-3 py-2 text-left">Paciente</th>
                                <th class="px-3 py-2 text-left">CI</th>
                                <th class="px-3 py-2 text-left">Fecha</th>
                                <th class="px-3 py-2 text-left">Tipo</th>
                                <th class="px-3 py-2 text-left">Peso</th>
                                <th class="px-3 py-2 text-left">Altura</th>

                                {{-- NUEVA COLUMNA: Alergias --}}
                                <th class="px-3 py-2 text-left">Alergias</th>

                                <th class="px-3 py-2 text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">

                            @foreach ($historiales as $his)
                                @php
                                    $pac = $his->paciente;
                                    $u = $pac?->usuario;
                                @endphp

                                <tr class="hover:bg-purple-50/40 transition-colors">

                                    <td class="px-3 py-2 font-semibold text-gray-800">
                                        {{ $his->cod_his }}
                                    </td>

                                    <td class="px-3 py-2 font-medium text-gray-800">
                                        @if ($u)
                                            {{ $u->name }} {{ $u->paterno }} {{ $u->materno }}
                                        @else
                                            Paciente {{ $his->cod_pac }}
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 text-gray-600">
                                        {{ $u?->carnet ?? '—' }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600 text-xs">
                                        {{ \Carbon\Carbon::parse($his->fec_cre_his)->format('d/m/Y') }}
                                    </td>

                                    <td class="px-3 py-0.5">
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs bg-purple-50 text-purple-700 border border-purple-200">
                                            {{ $his->tip_san_his ?? 'Sin tipo' }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-2 text-gray-700">
                                        {{ $his->pes_his ?? '—' }} kg
                                    </td>

                                    <td class="px-3 py-2 text-gray-700">
                                        {{ $his->alt_his ?? '—' }} m
                                    </td>

                                    {{-- CELDA DE ALERGIAS --}}
                                    <td class="px-3 py-2 text-gray-700">
                                        @if ($his->ale_his)
                                            <span title="{{ $his->ale_his }}"
                                                class="px-2 py-0.5 rounded-full text-xs bg-rose-50 text-rose-700 border border-rose-200 cursor-help">
                                                {{ Str::limit($his->ale_his, 20) }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        <button wire:click="abrirModalDetalle('{{ $his->cod_his }}')"
                                            class="p-2 bg-purple-50 text-purple-600 rounded-full hover:bg-purple-100">
                                            <i class="bi bi-eye-fill text-sm"></i>
                                        </button>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>

            @endif

            {{-- Paginación --}}
            <div class="mt-4">{{ $historiales->links() }}</div>

        @endif

    </div>


    {{-- 🟦 MODAL CREAR --}}
    @if ($modalCrear)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div
                class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden animate-fadeIn max-h-[85vh] flex flex-col">

                {{-- HEADER --}}
                <div
                    class="px-6 py-4 border-b bg-gradient-to-r from-purple-600 to-fuchsia-500 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class="bi bi-clipboard-plus"></i>
                            Nuevo historial médico
                        </h2>
                        <p class="text-[11px] text-purple-100">Registrar información clínica inicial.</p>
                    </div>
                    <button wire:click="cerrarModalCrear" class="text-purple-100 hover:text-white transition">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>

                {{-- CONTENIDO (SCROLL) --}}
                <div class="px-6 py-4 overflow-y-auto space-y-5 bg-gray-50 flex-1 custom-scroll">

                    {{-- BUSCAR PACIENTE --}}
                    <div class="bg-white rounded-xl border border-purple-100 p-4 shadow-sm">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Paciente <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-purple-400">
                                <i class="bi bi-person-search"></i>
                            </span>
                            <input type="text" wire:model.live="buscarPaciente"
                                placeholder="Buscar por nombre, apellido o CI..."
                                class="w-full border rounded-lg px-9 py-2 shadow-sm focus:ring-purple-500 bg-white">
                        </div>

                        {{-- Lista de resultados --}}
                        @if (!empty($pacientes))
                            <ul
                                class="mt-2 bg-white border border-gray-200 rounded-lg shadow max-h-40 overflow-y-auto custom-scroll">
                                @foreach ($pacientes as $p)
                                    @php $uPac = $p->usuario; @endphp
                                    <li wire:click="seleccionarPaciente('{{ $p->cod_pac }}')"
                                        class="px-3 py-2 hover:bg-purple-50 cursor-pointer flex justify-between">
                                        <div>
                                            <strong class="text-sm text-gray-800">
                                                {{ $uPac->name }} {{ $uPac->paterno }} {{ $uPac->materno }}
                                            </strong>
                                            <p class="text-xs text-gray-500">
                                                CI: {{ $uPac->carnet }} · Código: {{ $p->cod_pac }}
                                            </p>
                                        </div>
                                        <i class="bi bi-plus-circle text-purple-500 text-lg"></i>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        {{-- Seleccionado --}}
                        @if ($pacienteSeleccionadoNombre)
                            <p class="mt-2 text-sm text-emerald-600 flex items-center gap-1">
                                <i class="bi bi-check-circle-fill"></i>
                                Seleccionado:
                                <strong>{{ $pacienteSeleccionadoNombre }}</strong>
                            </p>
                        @endif

                        {{-- Errores --}}
                        @if ($mensajePaciente)
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                {{ $mensajePaciente }}
                            </p>
                        @endif

                        @error('pacienteSeleccionadoId')
                            <p class="text-xs text-red-600 mt-1 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- CAMPOS CLÍNICOS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Antecedentes personales --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-3 shadow-sm">
                            <label class="block text-sm font-semibold text-gray-700">Antecedentes personales</label>
                            <textarea wire:model.defer="ANT_PER_HIS"
                                class="w-full border rounded-lg px-3 py-2 h-28 text-sm focus:ring-purple-500"></textarea>
                        </div>

                        {{-- Antecedentes familiares --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-3 shadow-sm">
                            <label class="block text-sm font-semibold text-gray-700">Antecedentes familiares</label>
                            <textarea wire:model.defer="ANT_FAM_HIS"
                                class="w-full border rounded-lg px-3 py-2 h-28 text-sm focus:ring-purple-500"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Alergias --}}
                        <div class="bg-white rounded-xl border p-3 shadow-sm">
                            <label class="block text-sm font-semibold text-gray-700">Alergias</label>
                            <textarea wire:model.defer="ALE_HIS" class="w-full border rounded-lg px-3 py-2 h-24 text-sm focus:ring-purple-500"></textarea>
                        </div>

                        {{-- Tratamientos previos --}}
                        <div class="bg-white rounded-xl border p-3 shadow-sm">
                            <label class="block text-sm font-semibold text-gray-700">Tratamientos previos</label>
                            <textarea wire:model.defer="TRA_PRE_HIS"
                                class="w-full border rounded-lg px-3 py-2 h-24 text-sm focus:ring-purple-500"></textarea>
                        </div>
                    </div>

                    {{-- Hábitos alimenticios --}}
                    <div class="bg-white rounded-xl border p-3 shadow-sm">
                        <label class="block text-sm font-semibold text-gray-700">Hábitos alimenticios</label>
                        <textarea wire:model.defer="HAB_ALI_HIS"
                            class="w-full border rounded-lg px-3 py-2 h-24 text-sm focus:ring-purple-500"></textarea>
                    </div>

                    {{-- Observaciones --}}
                    <div class="bg-white rounded-xl border p-3 shadow-sm">
                        <label class="block text-sm font-semibold text-gray-700">Observaciones generales</label>
                        <textarea wire:model.defer="OBS_GEN_HIS"
                            class="w-full border rounded-lg px-3 py-2 h-24 text-sm focus:ring-purple-500"></textarea>
                    </div>

                    {{-- Peso / Altura / Tipo de sangre --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Peso --}}
                        <div class="bg-white rounded-xl border p-3 shadow-sm">
                            <label class="block text-sm font-semibold text-gray-700">Peso (kg)</label>
                            <input type="number" step="0.01" wire:model.defer="PES_HIS"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-purple-500">
                        </div>

                        {{-- Altura --}}
                        <div class="bg-white rounded-xl border p-3 shadow-sm">
                            <label class="block text-sm font-semibold text-gray-700">Altura (m)</label>
                            <input type="number" step="0.01" wire:model.defer="ALT_HIS"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-purple-500">
                        </div>

                        {{-- Tipo de sangre --}}
                        <div class="bg-white rounded-xl border p-3 shadow-sm">
                            <label class="block text-sm font-semibold text-gray-700">Tipo de sangre</label>

                            <select wire:model.defer="TIP_SAN_HIS"
                                class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:ring-purple-500">

                                <option value="">Seleccione…</option>

                                <optgroup label="Rh Positivo">
                                    <option value="O+">O+</option>
                                    <option value="A+">A+</option>
                                    <option value="B+">B+</option>
                                    <option value="AB+">AB+</option>
                                    <option value="ORH+">ORH+</option>
                                </optgroup>

                                <optgroup label="Rh Negativo">
                                    <option value="O-">O-</option>
                                    <option value="A-">A-</option>
                                    <option value="B-">B-</option>
                                    <option value="AB-">AB-</option>
                                    <option value="ORH-">ORH-</option>
                                </optgroup>

                            </select>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="px-6 py-3 border-t bg-white flex justify-end gap-3">

                    {{-- Botón limpiar --}}
                    <button
                        wire:click="
                    $set('ANT_PER_HIS','');
                    $set('ANT_FAM_HIS','');
                    $set('ALE_HIS','');
                    $set('TRA_PRE_HIS','');
                    $set('OBS_GEN_HIS','');
                    $set('HAB_ALI_HIS','');
                    $set('PES_HIS','');
                    $set('ALT_HIS','');
                    $set('TIP_SAN_HIS','');
                "
                        class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-100">
                        Limpiar
                    </button>

                    {{-- Botón cancelar --}}
                    <button wire:click="cerrarModalCrear"
                        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                        Cancelar
                    </button>

                    {{-- Guardar --}}
                    <button wire:click="guardarHistorial"
                        class="px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 shadow">
                        Guardar historial
                    </button>

                </div>
            </div>
        </div>
    @endif

    <style>
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #c084fc;
            border-radius: 10px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #f3e8ff;
        }
    </style>



    {{-- 🟦 MODAL DETALLE --}}
    @if ($modalDetalle && $historialDetalle)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-40">
            <div class="bg-white w-full max-w-3xl max-h-[90vh] rounded-2xl shadow-2xl overflow-hidden animate-fadeIn">
                {{-- Header --}}
                <div
                    class="px-6 py-3 border-b flex items-center justify-between bg-gradient-to-r from-purple-600 to-indigo-500">
                    <div>
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class="bi bi-clipboard2-heart"></i>
                            Detalle del historial
                        </h2>
                        <p class="text-xs text-purple-100">
                            Código:
                            <strong>{{ $historialDetalle->COD_HIS }}</strong>
                            · Fecha:
                            {{ \Carbon\Carbon::parse($historialDetalle->fec_cre_his)->format('d/m/Y') }}
                        </p>
                    </div>

                    <button wire:click="cerrarModalDetalle" class="text-purple-100 hover:text-white transition">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>

                {{-- Contenido --}}
                <div class="px-6 py-4 overflow-y-auto space-y-4 text-sm bg-gray-50">

                    @php
                        $pacDet = $historialDetalle->paciente ?? null;
                        $uDet = $pacDet?->usuario ?? null;
                    @endphp

                    {{-- 📌 Datos del paciente --}}
                    <div class="bg-white border border-purple-100 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                        <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center shadow-inner">
                            <i class="bi bi-person-heart text-purple-600 text-xl"></i>
                        </div>

                        <div class="space-y-0.5">
                            <h3 class="text-xs font-semibold text-purple-700 uppercase">Paciente</h3>

                            <p class="font-semibold text-gray-800">
                                @if ($uDet)
                                    {{ $uDet->name }} {{ $uDet->paterno }} {{ $uDet->materno }}
                                @else
                                    Paciente {{ $historialDetalle->cod_pac }}
                                @endif
                            </p>

                            <p class="text-xs text-gray-600">
                                CI:
                                {{ $uDet?->carnet ?? '—' }}
                                @if ($pacDet)
                                    · Código: {{ $pacDet->cod_pac }}
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- 🧪 Bloque de datos clínicos --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Antecedentes personales --}}
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Antecedentes personales</h3>
                            <p class="p-2 border rounded-lg bg-gray-50 min-h-[60px] shadow-sm">
                                {{ $historialDetalle->ant_per_his ?? 'Sin datos.' }}
                            </p>
                        </div>

                        {{-- Antecedentes familiares --}}
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Antecedentes familiares</h3>
                            <p class="p-2 border rounded-lg bg-gray-50 min-h-[60px] shadow-sm">
                                {{ $historialDetalle->ant_fam_his ?? 'Sin datos.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Alergias --}}
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Alergias</h3>
                            <p class="p-2 border rounded-lg bg-rose-50 text-rose-700 min-h-[60px] shadow-sm">
                                {{ $historialDetalle->ale_his ?? 'Sin alergias registradas.' }}
                            </p>
                        </div>

                        {{-- Tratamientos previos --}}
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Tratamientos previos</h3>
                            <p class="p-2 border rounded-lg bg-gray-50 min-h-[60px] shadow-sm">
                                {{ $historialDetalle->tra_pre_his ?? 'Sin datos.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Hábitos alimenticios --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Hábitos alimenticios</h3>
                        <p class="p-2 border rounded-lg bg-gray-50 min-h-[60px] shadow-sm">
                            {{ $historialDetalle->hab_ali_his ?? 'Sin datos.' }}
                        </p>
                    </div>

                    {{-- Observaciones --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Observaciones generales</h3>
                        <p class="p-2 border rounded-lg bg-gray-50 min-h-[60px] shadow-sm">
                            {{ $historialDetalle->obs_gen_his ?? 'Sin datos.' }}
                        </p>
                    </div>

                    {{-- Peso / Altura / Tipo de sangre --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Peso --}}
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Peso</h3>
                            <p class="p-2 border rounded-lg bg-gray-50 min-h-[40px] shadow-sm">
                                {{ $historialDetalle->PES_HIS ? $historialDetalle->pes_his . ' kg' : 'Sin datos.' }}
                            </p>
                        </div>

                        {{-- Altura --}}
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Altura</h3>
                            <p class="p-2 border rounded-lg bg-gray-50 min-h-[40px] shadow-sm">
                                {{ $historialDetalle->ALT_HIS ? $historialDetalle->alt_his . ' m' : 'Sin datos.' }}
                            </p>
                        </div>

                        {{-- Tipo sangre --}}
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Tipo de sangre</h3>
                            <p class="p-2 border rounded-lg bg-gray-50 min-h-[40px] shadow-sm">
                                {{ $historialDetalle->tip_san_his ?? 'Sin datos.' }}
                            </p>
                        </div>

                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-3 border-t bg-white text-right">
                    <button wire:click="cerrarModalDetalle"
                        class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-100 transition">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    @endif


    <style>
        /* Line clamp para textos */
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener("livewire:load", () => {

                let graficoHistorial = null; // Variable global para evitar duplicados

                function renderizarGrafico() {
                    const ctxEl = document.getElementById("graficoHistoriales");

                    if (!ctxEl) return;

                    // Destruir gráfico existente si ya había uno
                    if (graficoHistorial !== null) {
                        graficoHistorial.destroy();
                        graficoHistorial = null;
                    }

                    const meses = @json($grafico['meses'] ?? []);
                    const totales = @json($grafico['totales'] ?? []);

                    if (meses.length === 0 || totales.length === 0) {
                        console.warn("⚠️ No hay datos para el gráfico.");
                        return;
                    }

                    const nombresMeses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov',
                        'Dic'
                    ];
                    const labels = meses.map(m => nombresMeses[m - 1]);

                    // 🎨 Crear gradiente suave
                    const ctx = ctxEl.getContext("2d");
                    const gradient = ctx.createLinearGradient(0, 0, 0, 120);
                    gradient.addColorStop(0, "rgba(168, 85, 247, 0.35)"); // purple-500
                    gradient.addColorStop(1, "rgba(168, 85, 247, 0.05)");

                    graficoHistorial = new Chart(ctxEl, {
                        type: "line",
                        data: {
                            labels: labels,
                            datasets: [{
                                label: "Historiales registrados",
                                data: totales,
                                borderWidth: 2,
                                borderColor: "#a855f7", // purple-500
                                backgroundColor: gradient,
                                pointRadius: 4,
                                pointBackgroundColor: "#7c3aed",
                                tension: 0.35,
                                fill: true
                            }]
                        },
                        options: {
                            animation: {
                                duration: 900,
                                easing: "easeOutQuart"
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: "#4c1d95",
                                    titleColor: "#fff",
                                    bodyColor: "#fff",
                                    borderWidth: 0,
                                    padding: 10,
                                    displayColors: false
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: "#6b7280",
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0,
                                        color: "#6b7280",
                                        font: {
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        color: "rgba(107, 114, 128, 0.15)"
                                    }
                                }
                            }
                        }
                    });
                }

                // 🌟 Render inicial
                renderizarGrafico();

                // 🔁 Re-render automático cuando Livewire actualice el gráfico
                Livewire.on("actualizar-grafico", () => {
                    renderizarGrafico();
                });

                // 🔔 SweetAlert para historial creado
                Livewire.on("historial-creado", () => {
                    Swal.fire({
                        icon: "success",
                        title: "Historial creado",
                        text: "El registro se guardó correctamente.",
                        confirmButtonColor: "#a855f7",
                        confirmButtonText: "Aceptar"
                    });

                    // Actualiza el gráfico después de crear un historial
                    setTimeout(() => Livewire.dispatch("actualizar-grafico"), 300);
                });

            });
        </script>
    @endpush
</div>
