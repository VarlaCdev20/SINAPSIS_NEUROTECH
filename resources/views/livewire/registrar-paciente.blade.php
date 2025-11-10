<div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <label class="block text-gray-700 font-semibold mb-2">Buscar solicitante aprobado</label>
    <input type="text" wire:model.live="buscar" placeholder="Buscar por nombre, CI o correo..."
        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 px-3 py-2 shadow-sm text-gray-700">

    @if(!empty($solicitantes))
        <ul class="mt-2 bg-white border border-gray-200 rounded-lg shadow divide-y divide-gray-100 max-h-48 overflow-y-auto">
            @foreach($solicitantes as $sol)
                <li class="px-3 py-2 hover:bg-blue-50 cursor-pointer"
                    wire:click="seleccionar('{{ $sol->cod_sol }}')">
                    <strong>{{ $sol->nom_sol }} {{ $sol->ap_pat_sol }} {{ $sol->ap_mat_sol }}</strong>
                    <span class="text-sm text-gray-500"> ({{ $sol->ci_sol }}) </span>
                </li>
            @endforeach
        </ul>
    @endif

    @if($seleccionado)
        <p class="mt-3 text-green-700 text-sm">
            ✅ Solicitante <strong>{{ $seleccionado }}</strong> cargado correctamente.
        </p>
    @endif
</div>

<!-- Script que escucha el evento para rellenar el form -->
<script>
    document.addEventListener('rellenarCampos', e => {
        const data = e.detail;

        for (const campo in data) {
            const input = document.querySelector(`[name="${campo}"]`);
            if (input) input.value = data[campo] ?? '';
        }
    });
</script>
