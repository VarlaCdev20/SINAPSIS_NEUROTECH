<x-menu>
    <div class="p-8 bg-gray-50 min-h-screen text-gray-800">

        <!-- ✅ Título -->
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center space-x-2 mb-3 md:mb-0">
                <i class="bi bi-person-plus-fill text-blue-600 text-3xl"></i>
                <span>Registrar Paciente</span>
            </h2>
            <a href="{{ route('mis_pacientes.listar') }}"
                class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 transition">
                <i class="bi bi-arrow-left-circle me-1"></i> Volver a la lista
            </a>
        </div>

        <!-- 🔍 Buscador de solicitantes aprobados (Livewire) -->
        @livewire('registrar-paciente')

        <!-- ✅ Alertas -->
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <strong class="font-bold">⚠️ Ocurrieron algunos errores:</strong>
                <ul class="list-disc list-inside mt-2 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
                <strong class="font-bold">✅ {{ session('success') }}</strong>
            </div>
        @endif

        <!-- 🧾 FORMULARIO PACIENTE -->
        <form method="POST" action="{{ route('users.store') }}"
            class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej. Carla"
                        class="campo-formulario">
                    @error('name') <p class="mensaje-error">{{ $message }}</p> @enderror
                </div>

                <!-- Apellido Paterno -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Apellido Paterno</label>
                    <input type="text" name="paterno" value="{{ old('paterno') }}" placeholder="Ej. Encinas"
                        class="campo-formulario">
                    @error('paterno') <p class="mensaje-error">{{ $message }}</p> @enderror
                </div>

                <!-- Apellido Materno -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Apellido Materno</label>
                    <input type="text" name="materno" value="{{ old('materno') }}" placeholder="Ej. Cano"
                        class="campo-formulario">
                    @error('materno') <p class="mensaje-error">{{ $message }}</p> @enderror
                </div>

                <!-- Carnet -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Carnet de Identidad</label>
                    <input type="text" name="carnet" value="{{ old('carnet') }}" placeholder="Ej. 1234567"
                        class="campo-formulario">
                    @error('carnet') <p class="mensaje-error">{{ $message }}</p> @enderror
                </div>

                <!-- Celular -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Celular</label>
                    <input type="text" name="celular" value="{{ old('celular') }}" placeholder="Ej. 71234567"
                        class="campo-formulario">
                    @error('celular') <p class="mensaje-error">{{ $message }}</p> @enderror
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-1">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" placeholder="Ej. Av. Busch N°123, La Paz"
                        class="campo-formulario">
                    @error('direccion') <p class="mensaje-error">{{ $message }}</p> @enderror
                </div>

                <!-- Fecha Nacimiento -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                        class="campo-formulario">
                    @error('fecha_nacimiento') <p class="mensaje-error">{{ $message }}</p> @enderror
                </div>

                <!-- Correo -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com"
                        class="campo-formulario">
                    @error('email') <p class="mensaje-error">{{ $message }}</p> @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Contraseña</label>
                    <input type="password" name="password" placeholder="********"
                        class="campo-formulario">
                    @error('password') <p class="mensaje-error">{{ $message }}</p> @enderror
                </div>

                <!-- Ocupación -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Ocupación</label>
                    <input type="text" name="ocupacion" value="{{ old('ocupacion') }}" placeholder="Ej. Estudiante"
                        class="campo-formulario">
                    @error('ocupacion') <p class="mensaje-error">{{ $message }}</p> @enderror
                </div>

                <!-- País y Ciudad -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:col-span-2">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">País</label>
                        <select id="paisSelect" name="pais_id" class="campo-formulario">
                            <option value="">Seleccione un país</option>
                            @foreach ($pais as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Ciudad</label>
                        <select id="ciudadSelect" name="ciudad_id" class="campo-formulario">
                            <option value="">Seleccione una ciudad</option>
                        </select>
                        <small id="ciudadStatus" class="text-xs text-gray-500 mt-1 block"></small>
                    </div>
                </div>

                <!-- Rol oculto (Paciente fijo) -->
                <input type="hidden" name="rol" value="Paciente">
            </div>

            <!-- Botón Guardar -->
            <div class="mt-8 text-center">
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105 flex items-center justify-center mx-auto">
                    <i class="bi bi-save2 me-2"></i> Guardar Paciente
                </button>
            </div>
        </form>
    </div>
</x-menu>

<!-- 🌍 SCRIPT: Cargar ciudades -->
<script>
    const paisSelect = document.getElementById('paisSelect');
    const ciudadSelect = document.getElementById('ciudadSelect');
    const ciudadStatus = document.getElementById('ciudadStatus');

    paisSelect.addEventListener('change', async () => {
        const paisId = paisSelect.value;
        ciudadSelect.innerHTML = '<option value="">Cargando ciudades...</option>';
        ciudadStatus.textContent = 'Consultando ciudades...';
        ciudadStatus.className = 'text-xs text-blue-600 mt-1 block animate-pulse';

        if (!paisId) {
            ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
            ciudadStatus.textContent = '';
            return;
        }

        try {
            const response = await fetch(`/ciudades/${paisId}`);
            if (!response.ok) throw new Error('Error al obtener ciudades');
            const data = await response.json();

            if (data.length === 0) {
                ciudadSelect.innerHTML = '<option value="">No hay ciudades registradas</option>';
                ciudadStatus.textContent = 'Sin resultados';
                ciudadStatus.className = 'text-xs text-red-600 mt-1 block';
                return;
            }

            ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
            data.forEach(ciudad => {
                const option = document.createElement('option');
                option.value = ciudad.id;
                option.textContent = ciudad.nombre;
                ciudadSelect.appendChild(option);
            });

            ciudadStatus.textContent = `Se encontraron ${data.length} ciudades`;
            ciudadStatus.className = 'text-xs text-green-600 mt-1 block';
        } catch (error) {
            ciudadSelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
            ciudadStatus.textContent = 'Error de conexión';
            ciudadStatus.className = 'text-xs text-red-600 mt-1 block';
        }
    });

    // 🎨 Animación cuando se autocompletan campos desde Livewire
    document.addEventListener('rellenarCampos', e => {
        const data = e.detail;
        for (const campo in data) {
            const input = document.querySelector(`[name="${campo}"]`);
            if (input) {
                input.value = data[campo] ?? '';
                input.classList.add('bg-green-50', 'transition');
                setTimeout(() => input.classList.remove('bg-green-50'), 600);
            }
        }
    });
</script>

<!-- 💅 Clases auxiliares para limpieza -->
<style>
    .campo-formulario {
        width: 100%;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        padding: 0.5rem 0.75rem;
        color: #374151;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .campo-formulario:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 2px #bfdbfe;
    }
    .mensaje-error {
        color: #dc2626;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
</style>
