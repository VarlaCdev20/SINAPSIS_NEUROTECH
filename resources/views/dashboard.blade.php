<x-menu>
    <main class="">
        <div class="">

            {{-- ADMINISTRADOR --}}
            @role('Administrador')
                @include('partials.dashboard_admin')
            @endrole

            {{-- MÉDICO --}}
            @role('Medico')
                @include('partials.dashboard_medico')
            @endrole

            {{-- PACIENTE --}}
            @role('Paciente')
                @include('partials.dashboard_paciente')
            @endrole

            {{-- Fallback opcional si no hay rol --}}
            @if(!auth()->user()->hasAnyRole(['Administrador','Medico','Paciente']))
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-800">Sin rol asignado</h2>
                    <p class="text-gray-600 mt-2">Contacta al administrador para asignarte un rol.</p>
                </div>
            @endif

        </div>
    </main>
</x-menu>
