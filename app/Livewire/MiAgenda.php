<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Collection;

class MiAgenda extends Component
{
    public $paciente;
    public Collection $citas;
    public int $total = 0;
    public int $programadas = 0;
    public int $asistidas = 0;
    public int $canceladas = 0;
    public array $meses = [];
    public array $conteos = [];

    // 💜 Variables que llegan desde el controlador
    public $proxCita = null;
    public $tiempoRestante = null;

    /**
     * Recibe todos los datos listos desde el controlador AgendaController@index
     */
    public function mount(
        $paciente,
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
        $this->paciente       = $paciente;
        $this->citas          = collect($citas);
        $this->total          = (int) $total;
        $this->programadas    = (int) $programadas;
        $this->asistidas      = (int) $asistidas;
        $this->canceladas     = (int) $canceladas;
        $this->meses          = $meses;
        $this->conteos        = $conteos;
        $this->proxCita       = $proxCita;
        $this->tiempoRestante = $tiempoRestante;
    }

    /**
     * La vista del componente
     */
    public function render()
    {
        return view('livewire.mi-agenda');
    }
}
