<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Collection;

class AgendaAdmin extends Component
{
    public $administrador;
    public Collection $citas;
    public int $total = 0;
    public int $programadas = 0;
    public int $asistidas = 0;
    public int $canceladas = 0;
    public array $meses = [];
    public array $conteos = []; 
    public $proxCita = null;
    public $tiempoRestante = null;

    public function mount(
        $administrador,
        $citas,
        $total,
        $programadas,
        $asistidas,
        $canceladas,
        $meses,
        $conteos,
        $proxCita = null,
        $tiempoRestante = null
    ) {
        $this->administrador = $administrador;
        $this->citas = collect($citas);
        $this->total = $total;
        $this->programadas = $programadas;
        $this->asistidas = $asistidas;
        $this->canceladas = $canceladas;
        $this->meses = $meses;
        $this->conteos = $conteos;
        $this->proxCita = $proxCita;
        $this->tiempoRestante = $tiempoRestante;
    }

    public function render()
    {
        return view('livewire.agenda-admin');
    }
}
