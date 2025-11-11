<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Solicitante;
use Livewire\Attributes\On;

class Solicitudes extends Component
{
    public $buscar = '';
    public $n_registros = '';

    public function aprobar($cod_sol)
    {
        $this->dispatch('confirmarAprobacion', $cod_sol);
    }

    #[On('confirmadoAprobacion')]
    public function confirmadoAprobacion($codSol)
    {
        $solicitante = Solicitante::where('cod_sol', $codSol)->first();

        if ($solicitante) {
            $solicitante->update(['est_sol' => 'aprobado']);
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Solicitud aprobada',
            'text' => 'El solicitante fue aprobado correctamente.',
            'refresh' => true,
        ]);
    }

    public function rechazar($cod_sol)
    {
        $this->dispatch('confirmarRechazo', $cod_sol);
    }

    #[On('confirmadoRechazo')]
    public function confirmadoRechazo($codSol)
    {
        $solicitante = Solicitante::where('cod_sol', $codSol)->first();

        if ($solicitante) {
            $solicitante->update(['est_sol' => 'rechazado']);
        }

        $this->dispatch('swal', [
            'icon' => 'info',
            'title' => 'Solicitud rechazada',
            'text' => 'El solicitante fue marcado como rechazado.',
            'refresh' => true,
        ]);
    }

    public function abrirAgenda($cod_sol)
    {
        // Dispara evento al componente agenda-solicitante
        $this->dispatch('abrirAgenda', $cod_sol);
    }

    public function render()
    {
        $solicitantes = Solicitante::query()
            ->when($this->buscar, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nom_sol', 'like', "%{$this->buscar}%")
                        ->orWhere('ap_pat_sol', 'like', "%{$this->buscar}%")
                        ->orWhere('cod_sol', 'like', "%{$this->buscar}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->when($this->n_registros, fn($q) => $q->limit($this->n_registros))
            ->get();

        return view('livewire.solicitudes', compact('solicitantes'));
    }
}
