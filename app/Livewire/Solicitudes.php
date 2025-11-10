<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Solicitante;

class Solicitudes extends Component
{
    public $buscar = '';

    protected $listeners = ['refreshComponent' => '$refresh'];

    // Abrir confirmación de aprobación
    public function aprobar($cod_sol)
    {
        $this->dispatch('confirmarAprobacion', $cod_sol);
    }

    #[\Livewire\Attributes\On('confirmadoAprobacion')]
    public function confirmadoAprobacion($codSol)
    {
        Solicitante::where('cod_sol', $codSol)->update([
            'est_sol' => 'aprobado'
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Solicitud aprobada',
            'text' => 'El solicitante fue aprobado correctamente.',
            'refresh' => true,
        ]);

        $this->dispatch('refreshComponent');
    }

    // Rechazar solicitud
    public function rechazar($cod_sol)
    {
        $this->dispatch('confirmarRechazo', $cod_sol);
    }

    #[\Livewire\Attributes\On('confirmadoRechazo')]
    public function confirmadoRechazo($codSol)
    {
        Solicitante::where('cod_sol', $codSol)->update([
            'est_sol' => 'rechazado'
        ]);

        $this->dispatch('swal', [
            'icon' => 'info',
            'title' => 'Solicitud rechazada',
            'text' => 'El solicitante fue marcado como rechazado.',
            'refresh' => true,
        ]);

        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        $solicitantes = Solicitante::query()
            ->when($this->buscar, function ($q) {
                $q->where('nom_sol', 'like', "%{$this->buscar}%")
                    ->orWhere('ap_pat_sol', 'like', "%{$this->buscar}%")
                    ->orWhere('ap_mat_sol', 'like', "%{$this->buscar}%")
                    ->orWhere('email_sol', 'like', "%{$this->buscar}%")
                    ->orWhere('cod_sol', 'like', "%{$this->buscar}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.solicitudes', compact('solicitantes'));
    }
}
