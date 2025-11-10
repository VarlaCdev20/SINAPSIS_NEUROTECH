<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MisPacientes extends Component
{
    use WithPagination;

    public $search = '';
    public $n_registros = 10;
    public $haySinMedico = false;

    /** 🧠 Al iniciar el componente */
    public function mount()
    {
        $this->verificarPacientesSinMedico();
    }

    /** 🩺 Cambiar estado de un paciente */
    public function estado($codigo)
    {
        $user = User::where('cod_usu', $codigo)->first();

        if (!$user) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Paciente no encontrado',
                'text' => 'No se encontró el registro especificado.',
            ]);
            return;
        }

        if (Auth::user()->cod_usu === $user->cod_usu) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Acción no permitida',
                'text' => 'No puedes desactivar tu propia cuenta.',
            ]);
            return;
        }

        $user->estado = $user->estado ? 0 : 1;
        $user->save();

        Bitacora::create([
            'cod_usu' => Auth::user()->cod_usu,
            'acc_bit' => "Cambió el estado del paciente {$user->name} {$user->paterno} a " . ($user->estado ? 'Activo' : 'Inactivo'),
            'fec_hor_bit' => Carbon::now(),
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Estado actualizado',
            'text' => "El paciente {$user->name} fue " . ($user->estado ? 'activado' : 'desactivado') . ".",
        ]);
    }

    /** 🔍 Verifica si hay pacientes sin médico */
    public function verificarPacientesSinMedico()
    {
        $this->haySinMedico = Paciente::whereNull('cod_med')->exists();
    }

    /** ⚙️ Muestra confirmación con la lista de pacientes sin médico */
    public function confirmarAsignacion()
    {
        $sinMedico = Paciente::whereNull('cod_med')
            ->join('users', 'users.cod_usu', '=', 'pacientes.cod_usu')
            ->select('users.name', 'users.paterno', 'users.materno', 'users.carnet')
            ->get();

        if ($sinMedico->isEmpty()) {
            $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'No hay pacientes pendientes',
                'text' => 'Todos los pacientes ya tienen un médico asignado.',
            ]);
            return;
        }

        $nombres = $sinMedico->map(fn($p) => "{$p->name} {$p->paterno} {$p->materno} - CI: {$p->carnet}")->toArray();

        $this->dispatch('confirmarAsignacion', ['pacientes' => $nombres]);
    }

    /** ✅ Asigna pacientes sin médico al médico logueado */
    #[\Livewire\Attributes\On('asignarPacientesSinMedico')]
    public function asignarPacientesSinMedico()
    {
        $usuario = auth()->user();

        if (!$usuario->hasRole('Medico')) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Acceso denegado',
                'text' => 'Solo los médicos pueden reclamar pacientes.',
            ]);
            return;
        }

        $medico = Medico::where('cod_usu', $usuario->cod_usu)->first();

        if (!$medico) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error de relación',
                'text' => 'No se encontró el código interno del médico actual.',
            ]);
            return;
        }

        $sinMedico = Paciente::whereNull('cod_med')->get();

        if ($sinMedico->isEmpty()) {
            $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'Sin pacientes pendientes',
                'text' => 'Todos los pacientes ya tienen un médico asignado.',
            ]);
            $this->haySinMedico = false;
            return;
        }

        $cantidad = 0;
        foreach ($sinMedico as $paciente) {
            $paciente->update(['cod_med' => $medico->cod_med]);
            $cantidad++;
        }

        Bitacora::create([
            'cod_usu' => $usuario->cod_usu,
            'acc_bit' => "Asignó {$cantidad} paciente(s) sin médico a su lista personal ({$medico->cod_med}).",
            'fec_hor_bit' => Carbon::now(),
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Pacientes asignados',
            'text' => "Se asignaron {$cantidad} pacientes correctamente a tu lista.",
        ]);

        $this->verificarPacientesSinMedico();
        $this->dispatch('$refresh');
    }

    /** 📋 Render principal */
    public function render()
    {
        $medico = Medico::where('cod_usu', Auth::user()->cod_usu)->first();

        if (!$medico) {
            abort(403, 'El usuario actual no tiene un registro de médico asociado.');
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
            ->orderBy('users.id', 'desc')
            ->paginate($this->n_registros);

        $this->verificarPacientesSinMedico();

        return view('livewire.mis-pacientes', compact('pacientes'));
    }
}
