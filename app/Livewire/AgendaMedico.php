<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\{Cita, Paciente, Solicitante};
use Carbon\Carbon;
use Illuminate\Support\Str;

class AgendaMedico extends Component
{
    public $medico;

    // 📊 Datos principales
    public Collection $citas;
    public int $total = 0;
    public int $programadas = 0;
    public int $asistidas = 0;
    public int $canceladas = 0;
    public array $meses = [];
    public array $conteos = [];
    public $proxCita = null;
    public $tiempoRestante = null;
    public $pacientesAtendidosMes = 0;

    // 🔍 Buscador
    public string $buscar = '';

    // 📅 Semana actual
    public Collection $semana;

    public function mount(
        $medico,
        $citas,
        $total,
        $programadas,
        $asistidas,
        $canceladas,
        $meses,
        $conteos,
        $proxCita = null,
        $tiempoRestante = null,
        $pacientesAtendidosMes = 0
    ) {
        $this->medico = $medico;
        $this->citas = collect($citas);
        $this->total = $total;
        $this->programadas = $programadas;
        $this->asistidas = $asistidas;
        $this->canceladas = $canceladas;
        $this->meses = $meses;
        $this->conteos = $conteos;
        $this->proxCita = $proxCita;
        $this->tiempoRestante = $tiempoRestante;
        $this->pacientesAtendidosMes = $pacientesAtendidosMes;

        // 📆 Semana actual (lunes a domingo)
        $this->semana = collect();
        for ($i = 0; $i < 7; $i++) {
            $this->semana->push(Carbon::now()->startOfWeek()->addDays($i));
        }
    }

    protected function buscarPacientesOSolicitantes($texto)
    {
        // 🩺 Pacientes del médico
        $pacientes = \App\Models\Paciente::with(['usuario', 'citas' => fn($q) => $q->orderBy('fec_cit', 'asc')])
            ->whereHas(
                'citas',
                fn($q) =>
                $q->where('cod_med', $this->medico->cod_med)
                    ->orWhere('COD_MED', $this->medico->cod_med)
            )
            ->whereHas(
                'usuario',
                fn($u) =>
                $u->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($texto) . '%'])
            )
            ->get()
            ->map(function ($p) {
                $prox = $p->citas->where('fec_cit', '>=', now())->sortBy('fec_cit')->first();

                return [
                    'nombre' => $p->usuario->name,
                    'proxima_cita' => $prox ? \Carbon\Carbon::parse($prox->fec_cit) : null,
                    'estado' => $prox->est_cit ?? 'Programada',
                    'tipo' => 'Paciente',
                ];
            })
            ->values(); // 🔹 Reinicia índices numéricos

        // 👤 Solicitantes del médico
        $solicitantes = \App\Models\Solicitante::with(['citas' => fn($q) => $q->orderBy('fec_cit', 'asc')])
            ->whereHas(
                'citas',
                fn($q) =>
                $q->where('cod_med', $this->medico->cod_med)
                    ->orWhere('COD_MED', $this->medico->cod_med)
            )
            ->where(function ($q) use ($texto) {
                $q->whereRaw('LOWER(nom_sol) LIKE ?', ['%' . strtolower($texto) . '%'])
                    ->orWhereRaw('LOWER(ap_pat_sol) LIKE ?', ['%' . strtolower($texto) . '%'])
                    ->orWhereRaw('LOWER(ap_mat_sol) LIKE ?', ['%' . strtolower($texto) . '%']);
            })
            ->get()
            ->map(function ($s) {
                $prox = $s->citas->where('fec_cit', '>=', now())->sortBy('fec_cit')->first();

                return [
                    'nombre' => "{$s->nom_sol} {$s->ap_pat_sol} {$s->ap_mat_sol}",
                    'proxima_cita' => $prox ? \Carbon\Carbon::parse($prox->fec_cit) : null,
                    'estado' => $prox->est_cit ?? 'Programada',
                    'tipo' => 'Solicitante',
                ];
            })
            ->values(); // 🔹 También reinicia índices

        // ✅ Merge como colección genérica normal
        return collect($pacientes->all())
            ->merge($solicitantes->all())
            ->sortBy(fn($r) => $r['proxima_cita'] ?? now())
            ->values();
    }



    public function render()
    {
        $resultados = collect();

        if (strlen($this->buscar) >= 2) {
            $resultados = $this->buscarPacientesOSolicitantes($this->buscar);
        }

        return view('livewire.agenda-medico', [
            'resultados' => $resultados,
        ]);
    }
}
