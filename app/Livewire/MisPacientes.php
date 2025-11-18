<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Bitacora;
use App\Models\Historial;     // ✅ IMPORTACIÓN NECESARIA
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MisPacientes extends Component
{
    use WithPagination;

    // 🔍 BUSQUEDA
    public $search = '';
    public $n_registros = 10;
    public $haySinMedico = false;

    // 🔎 MODAL HISTORIAL
    public $mostrarHistorial = false;
    public $pacienteSeleccionado = null; // aquí irá el historial real o null

    /**********************************************
     * 🔹 Al iniciar
     **********************************************/
    public function mount()
    {
        $this->verificarPacientesSinMedico();
    }

    /**********************************************
     * 🔹 Cambiar estado
     **********************************************/
    public function estado($codigo)
    {
        $user = User::where('cod_usu', $codigo)->first();

        if (!$user) {
            return $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Paciente no encontrado',
                'text' => 'No se encontró el registro especificado.',
            ]);
        }

        if (Auth::user()->cod_usu === $user->cod_usu) {
            return $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Acción no permitida',
                'text' => 'No puedes desactivar tu propia cuenta.',
            ]);
        }

        // Cambiar estado
        $user->estado = $user->estado ? 0 : 1;
        $user->save();

        // Bitácora
        Bitacora::create([
            'cod_usu' => Auth::user()->cod_usu,
            'acc_bit' => "Cambió el estado del paciente {$user->name} {$user->paterno} a " .
                ($user->estado ? 'Activo' : 'Inactivo'),
            'fec_hor_bit' => now(),
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Estado actualizado',
            'text' => "El paciente {$user->name} fue " . ($user->estado ? 'activado' : 'desactivado'),
        ]);
    }

    /**********************************************
     * 🔹 Ver pacientes sin médico
     **********************************************/
    public function verificarPacientesSinMedico()
    {
        $this->haySinMedico = Paciente::whereNull('cod_med')->exists();
    }

    /**********************************************
     * 🔹 Confirmación para asignar
     **********************************************/
    public function confirmarAsignacion()
    {
        $sinMedico = Paciente::whereNull('cod_med')
            ->join('users', 'users.cod_usu', '=', 'pacientes.cod_usu')
            ->select('users.name', 'users.paterno', 'users.materno', 'users.carnet')
            ->get();

        if ($sinMedico->isEmpty()) {
            return $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'No hay pacientes pendientes',
                'text' => 'Todos los pacientes ya tienen un médico asignado.',
            ]);
        }

        $nombres = $sinMedico->map(
            fn($p) => "{$p->name} {$p->paterno} {$p->materno} - CI: {$p->carnet}"
        )->toArray();

        $this->dispatch('confirmarAsignacion', ['pacientes' => $nombres]);
    }

    /**********************************************
     * 🔹 Asignar pacientes sin médico
     **********************************************/
    #[\Livewire\Attributes\On('asignarPacientesSinMedico')]
    public function asignarPacientesSinMedico()
    {
        $usuario = auth()->user();

        if (!$usuario->hasRole('Medico')) {
            return $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Acceso denegado',
                'text' => 'Solo los médicos pueden reclamar pacientes.',
            ]);
        }

        $medico = Medico::where('cod_usu', $usuario->cod_usu)->first();

        if (!$medico) {
            return $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se encontró el registro del médico actual.',
            ]);
        }

        $sinMedico = Paciente::whereNull('cod_med')->get();

        if ($sinMedico->isEmpty()) {
            $this->haySinMedico = false;
            return $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'Sin pacientes pendientes',
                'text' => 'Todos los pacientes ya tienen médico.',
            ]);
        }

        $cantidad = 0;
        foreach ($sinMedico as $pac) {
            $pac->update(['cod_med' => $medico->cod_med]);
            $cantidad++;
        }

        Bitacora::create([
            'cod_usu' => $usuario->cod_usu,
            'acc_bit' => "Asignó {$cantidad} paciente(s) sin médico ({$medico->cod_med}).",
            'fec_hor_bit' => now(),
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Asignación completada',
            'text' => "Se asignaron {$cantidad} pacientes.",
        ]);

        $this->verificarPacientesSinMedico();
        $this->dispatch('$refresh');
    }


    /**********************************************
     * 🔹 VER HISTORIAL (modal pequeño)
     **********************************************/
    public function verHistorial($codPac)
    {
        // 🔥 CARGA EL HISTORIAL REAL DEL PACIENTE
        $historial = Historial::with('paciente.usuario')
            ->where('cod_pac', $codPac)
            ->first();

        if (!$historial) {
            // No tiene historial → mandar mensaje
            $this->pacienteSeleccionado = null;

            return $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'Sin historial',
                'text' => 'Este paciente aún no tiene un historial médico registrado.',
            ]);
        }

        // SI TIENE HISTORIAL → ABRIR MODAL
        $this->pacienteSeleccionado = $historial;
        $this->mostrarHistorial = true;
    }

    public function cerrarHistorial()
    {
        $this->pacienteSeleccionado = null;
        $this->mostrarHistorial = false;
    }


    /**********************************************
     * 🔹 Render
     **********************************************/
    public function render()
    {
        $medico = Medico::where('cod_usu', Auth::user()->cod_usu)->first();

        if (!$medico) {
            abort(403, 'El usuario actual no tiene registro de médico.');
        }

        $pacientes = User::join('pacientes', 'users.cod_usu', '=', 'pacientes.cod_usu')
            ->select('users.*', 'pacientes.cod_pac', 'pacientes.cod_med')
            ->where('pacientes.cod_med', $medico->cod_med)
            ->where(function ($q) {
                $q->where('users.name', 'like', "%{$this->search}%")
                    ->orWhere('users.paterno', 'like', "%{$this->search}%")
                    ->orWhere('users.materno', 'like', "%{$this->search}%")
                    ->orWhere('users.carnet', 'like', "%{$this->search}%");
            })
            ->orderBy('users.created_at', 'desc')
            ->paginate($this->n_registros);

        $this->verificarPacientesSinMedico();

        return view('livewire.mis-pacientes', [
            'pacientes' => $pacientes
        ]);
    }
}
