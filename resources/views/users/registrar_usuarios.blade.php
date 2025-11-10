<x-menu>
    <div class="p-8 bg-gray-50 min-h-screen text-gray-800">

        <!-- Alertas globales -->
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

        <!-- Encabezado -->
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center space-x-2 mb-3 md:mb-0">
                <i class="bi bi-person-plus-fill text-blue-600 text-3xl"></i>
                <span>Registrar Usuario</span>
            </h2>
            <a href="{{ route('users.index') }}"
                class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 transition">
                <i class="bi bi-arrow-left-circle me-1"></i> Volver a la lista
            </a>
        </div>

        <!-- Formulario -->
        <form method="POST" action="{{ route('users.store') }}"
            class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej. Carlos"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 placeholder-gray-400 @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Apellido Paterno -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Apellido Paterno</label>
                    <input type="text" name="paterno" value="{{ old('paterno') }}" placeholder="Ej. Méndez"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 placeholder-gray-400 @error('paterno') border-red-500 @enderror">
                    @error('paterno')
                    <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Apellido Materno -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Apellido Materno</label>
                    <input type="text" name="materno" value="{{ old('materno') }}" placeholder="Ej. Brun"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 placeholder-gray-400 @error('materno') border-red-500 @enderror">
                    @error('materno')
                    <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Carnet -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Carnet</label>
                    <input type="text" name="carnet" value="{{ old('carnet') }}" placeholder="Ej. 1234564"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 placeholder-gray-400 @error('celular') border-red-500 @enderror">
                    @error('carnet')
                    <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
                <!-- Celular -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Celular</label>
                    <input type="text" name="celular" value="{{ old('celular') }}" placeholder="Ej. 71234567"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 placeholder-gray-400 @error('celular') border-red-500 @enderror">
                    @error('celular')
                    <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-1">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}"
                        placeholder="Ej. Av. Busch N°123, La Paz"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 placeholder-gray-400 @error('direccion') border-red-500 @enderror">
                    @error('direccion')
                    <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha Nacimiento -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 @error('fecha_nacimiento') border-red-500 @enderror">
                    @error('fecha_nacimiento')
                    <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Correo -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 placeholder-gray-400 @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Contraseña</label>
                    <input type="password" name="password" placeholder="********"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 placeholder-gray-400 @error('password') border-red-500 @enderror">
                    @error('password')
                    <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Ocupación -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Ocupación</label>
                    <input type="text" name="ocupacion" value="{{ old('ocupacion') }}" placeholder="Ej. Médico General"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 placeholder-gray-400 @error('ocupacion') border-red-500 @enderror">
                    @error('ocupacion')
                    <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- roles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:col-span-2">
                    <!-- País -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Rol</label>
                        <select  name="rol"
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 @error('pais_id') border-red-500 @enderror">
                            <option value="">Seleccione un Rol</option>
                            @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ old('rol')==$role->id ? 'selected' : '' }}>
                                {{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('pais_id')
                        <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <!-- País y Ciudad -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:col-span-2">
                        <!-- País -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-1">País</label>
                            <select id="paisSelect" name="pais_id"
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 @error('pais_id') border-red-500 @enderror">
                                <option value="">Seleccione un país</option>
                                @foreach ($pais as $item)
                                <option value="{{ $item->id }}" {{ old('pais_id')==$item->id ? 'selected' : '' }}>
                                    {{ $item->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('pais_id')
                            <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Ciudad -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-1">Ciudad</label>
                            <select id="ciudadSelect" name="ciudad_id"
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700 @error('ciudad_id') border-red-500 @enderror">
                                <option value="">Seleccione una ciudad</option>
                            </select>
                            <small id="ciudadStatus" class="text-xs text-gray-500 mt-1 block"></small>
                            @error('ciudad_id')
                            <p class="text-red-600 text-xs mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Botón -->
                <div class="mt-8 text-center">
                    <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105 flex items-center justify-center mx-auto">
                        <i class="bi bi-save2 me-2"></i> Guardar Usuario
                    </button>
                </div>
        </form>
    </div>
</x-menu>

<!-- SCRIPT -->
<script>
    const paisSelect = document.getElementById('paisSelect');
    const ciudadSelect = document.getElementById('ciudadSelect');
    const ciudadStatus = document.getElementById('ciudadStatus');

    paisSelect.addEventListener('change', async () => {
        const paisId = paisSelect.value;
        ciudadSelect.innerHTML = '<option value="">Cargando ciudades...</option>';
        ciudadStatus.textContent = 'Consultando ciudades disponibles...';
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
                ciudadStatus.textContent = 'Sin resultados para este país';
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
            console.error(error);
            ciudadSelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
            ciudadStatus.textContent = 'Error de conexión con el servidor';
            ciudadStatus.className = 'text-xs text-red-600 mt-1 block';
        }
    });
</script>
