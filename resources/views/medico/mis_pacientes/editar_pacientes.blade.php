<x-menu>
    <div class="p-8 bg-gray-50 min-h-screen text-gray-800">

        <!-- ⚠️ Alertas -->
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <strong>⚠️ Ocurrieron algunos errores:</strong>
                <ul class="list-disc list-inside mt-2 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg animate-fade-in-down">
                <strong>✅ {{ session('success') }}</strong>
            </div>
        @endif

        <!-- 🧠 Encabezado -->
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                <i class="bi bi-person-heart text-blue-600 text-3xl"></i>
                <span>Editar Paciente</span>
            </h2>
            <a href="{{ route('mis_pacientes.listar') }}"
                class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 transition">
                <i class="bi bi-arrow-left-circle me-1"></i> Volver a la lista
            </a>
        </div>

        <!-- 🩺 Formulario -->
        <form method="POST" action="{{ route('pacientes.actualizar', encrypt($user->cod_usu)) }}"
              class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200 space-y-6 animate-fade-in">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <!-- Apellido Paterno -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Apellido Paterno</label>
                    <input type="text" name="paterno" value="{{ old('paterno', $user->paterno) }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <!-- Apellido Materno -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Apellido Materno</label>
                    <input type="text" name="materno" value="{{ old('materno', $user->materno) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <!-- Carnet -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Carnet</label>
                    <input type="text" name="carnet" value="{{ old('carnet', $user->carnet) }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <!-- Celular -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Celular</label>
                    <input type="text" name="celular" value="{{ old('celular', $user->celular) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-1">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $user->direccion) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <!-- Fecha Nacimiento -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $user->fecha_nacimiento) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <!-- Correo -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <!-- 🔐 Contraseña (opcional) -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Nueva Contraseña</label>
                    <input type="password" name="password" placeholder="Solo si desea cambiarla"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                    <small class="text-gray-500 text-xs">Dejar vacío si no desea cambiarla.</small>
                </div>

                <!-- Ocupación -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Ocupación</label>
                    <input type="text" name="ocupacion" value="{{ old('ocupacion', $user->ocupacion) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Estado</label>
                    <select name="estado"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 shadow-sm">
                        <option value="1" {{ $user->estado == 1 ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ $user->estado == 0 ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <!-- Médico Asignado (solo lectura) -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Médico Asignado</label>
                    <input type="text" readonly
                        value="{{ $paciente->medico->usuario->name ?? 'No asignado' }} {{ $paciente->medico->usuario->paterno ?? '' }}"
                        class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-600 shadow-inner cursor-not-allowed">
                </div>
            </div>

            <!-- 🌟 Botón Guardar -->
            <div class="mt-8 text-center">
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105 flex items-center justify-center mx-auto">
                    <i class="bi bi-save2 me-2"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</x-menu>
