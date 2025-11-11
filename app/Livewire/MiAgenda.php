<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Collection;
use Carbon\Carbon;

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

    // 🔮 Nuevas propiedades
    public $proxCita = null;
    public $tiempoRestante = null;

    public function mount($paciente, $citas, $total, $programadas, $asistidas, $canceladas, $meses, $conteos)
    {
        $this->paciente     = $paciente;
        $this->citas        = collect($citas);
        $this->total        = (int) $total;
        $this->programadas  = (int) $programadas;
        $this->asistidas    = (int) $asistidas;
        $this->canceladas   = (int) $canceladas;
        $this->meses        = $meses;
        $this->conteos      = $conteos;

        $this->calcularProximaCita();
    }

    /**
     * Calcula la próxima cita y cuánto tiempo falta.
     */
    public function calcularProximaCita()
    {
        $this->proxCita = $this->citas
            ->filter(fn($c) => Carbon::parse($c->fec_cit)->greaterThanOrEqualTo(now()))
            ->sortBy('fec_cit')
            ->first();

        if (!$this->proxCita) {
            $this->tiempoRestante = null;
            return;
        }

        $proxFecha = Carbon::parse($this->proxCita->fec_cit);
        $diffHoras = now()->diffInHours($proxFecha, false);

        if ($diffHoras <= 0) {
            $this->tiempoRestante = 'menos de una hora ⏳';
        } else {
            $diffDias = intdiv($diffHoras, 24);
            $restoHoras = $diffHoras % 24;

            $partes = [];
            if ($diffDias > 0) {
                $partes[] = "{$diffDias} día" . ($diffDias > 1 ? 's' : '');
            }
            if ($restoHoras > 0) {
                $partes[] = "{$restoHoras} hora" . ($restoHoras > 1 ? 's' : '');
            }

            $this->tiempoRestante = 'Faltan ' . implode(' y ', $partes) . ' ⏳';
        }
    }

    public function render()
    {
        return view('livewire.mi-agenda');
    }
}
