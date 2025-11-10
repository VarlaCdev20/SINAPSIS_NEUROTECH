<!-- 🌟 MODAL DE REGISTRO DE CITA -->
<div x-data="{ open: @entangle('mostrarModal') }" x-cloak>
    <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm z-50"
        x-transition.opacity.duration.300ms>

        <div x-show="open"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 scale-95 hover:scale-100 border-t-4 border-accent-600"
            x-transition.scale.origin.bottom.duration.300ms>

            <!-- Encabezado -->
            <div class="flex justify-between items-center bg-accent-600 text-white px-6 py-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="bi bi-calendar2-plus-fill text-2xl"></i>
                    Registrar nueva cita
                </h2>
                <button @click="open = false" class="hover:text-gray-200 transition">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            <!-- Contenido -->
            <div class="p-6 space-y-4 bg-gradient-to-b from-white to-accent-50/40">

                <!-- Código Cita -->
                <div>
                    <label class="font-medium text-gray-700 flex items-center gap-2">
                        <i class="bi bi-hash text-accent-600"></i> Código de Cita
                    </label>
                    <input type="text" wire:model="cod_cit" readonly
                        class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-green-50 text-accent-700 font-semibold tracking-wide focus:outline-none cursor-not-allowed">
                </div>

                <!-- Seleccionar Solicitante -->
                <div>
                    <label class="font-medium text-gray-700 flex items-center gap-2">
                        <i class="bi bi-people-fill text-accent-600"></i> Seleccionar Solicitante
                    </label>
                    <select wire:model="cod_sol"
                        class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-300 focus:border-accent-400 transition">
                        <option value="">Seleccione un solicitante...</option>

                        @foreach ($solicitantesAprobados as $sol)
                            <option value="{{ $sol->cod_sol }}">
                                {{ $sol->cod_sol }} - {{ $sol->nom_sol }} {{ $sol->ap_pat_sol }} {{ $sol->ap_mat_sol }}
                            </option>
                        @endforeach

                    </select>

                    @error('cod_sol')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Médico asignado -->
                <div>
                    <label class="font-medium text-gray-700 flex items-center gap-2">
                        <i class="bi bi-person-workspace text-accent-600"></i> Médico Asignado
                    </label>
                    <input type="text" value="{{ Auth::user()->name ?? 'Médico actual' }}" readonly
                        class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-800 focus:outline-none cursor-not-allowed">
                </div>

                <!-- Tipo de cita -->
                <div>
                    <label class="font-medium text-gray-700 flex items-center gap-2">
                        <i class="bi bi-laptop text-accent-600"></i> Tipo de Cita
                    </label>
                    <select wire:model="tip_cit"
                        class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-300 focus:border-accent-400 transition">
                        <option value="">Selecciona...</option>
                        <option value="virtual">Virtual</option>
                        <option value="presencial">Presencial</option>
                    </select>
                </div>

                <!-- Fecha -->
                <div>
                    <label class="font-medium text-gray-700 flex items-center gap-2">
                        <i class="bi bi-clock-history text-accent-600"></i> Fecha y hora
                    </label>
                    <input type="datetime-local" wire:model="fec_cit" min="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-300 focus:border-accent-400 transition">


                </div>

                <!-- Motivo -->
                <div>
                    <label class="font-medium text-gray-700 flex items-center gap-2">
                        <i class="bi bi-chat-left-text text-accent-600"></i> Motivo de la cita
                    </label>
                    <textarea wire:model="mot_cit" rows="2" placeholder="Ej: Consulta inicial, control de tratamiento..."
                        class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-300 focus:border-accent-400 resize-none"></textarea>
                </div>

                <!-- Estado -->
                <div>
                    <label class="font-medium text-gray-700 flex items-center gap-2">
                        <i class="bi bi-clipboard-check text-accent-600"></i> Estado
                    </label>
                    <input type="text" value="Registrado" readonly
                        class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-green-50 text-green-700 font-semibold focus:outline-none cursor-not-allowed">
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-3 px-6 py-4 bg-white border-t border-gray-200">
                <button @click="open = false"
                    class="px-5 py-2.5 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium transition">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>

                <button wire:click="guardarCita"
                    class="px-5 py-2.5 rounded-lg bg-accent-600 hover:bg-accent-700 text-white font-semibold shadow transition flex items-center gap-2">
                    <i class="bi bi-save2-fill"></i> Guardar Cita
                </button>
            </div>

        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('citaGuardada', () => {
            Swal.fire({
                title: 'Cita registrada',
                text: 'La cita se registró exitosamente',
                icon: 'success',
                confirmButtonColor: '#16a34a'
            });
        });

        Livewire.on('errorAlGuardar', data => {
            console.error(data); // <-- para debug
            Swal.fire({
                title: 'Error',
                text: data.message ?? 'Ocurrió un error inesperado',
                icon: 'error',
                confirmButtonColor: '#dc2626'
            });
        });
    });
</script>
<!-- 🌟 FIN MODAL DE REGISTRO DE CITA -->