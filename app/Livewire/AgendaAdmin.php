<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\{Cita, Medico};

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
    // 🔎 Filtros y búsqueda
    public string $buscar = '';
    public ?string $filtroMedico = null;     // COD_MED
    public ?string $filtroEstado = null;     // programada | atendida | cancelada
    public ?string $filtroTipo = null;       // presencial | virtual


    // 🗓️ Rango visible (semana actual por defecto)
    public string $fechaInicio;
    public string $fechaFin;

    // 📋 Datos auxiliares
    public Collection $medicos;              // Para el select de médico
    public array $tendenciaLabels = [];
    public array $tendenciaDatos = [];

    // 🪟 Modal de detalle
    public bool $showModal = false;
    public ?array $detalle = null; // {cod_cit, fecha, hora, estado, tipo, medico, paciente/solicitante, motivo}

    public function mount()
    {
        // Semana actual (Lunes - Domingo)
        $inicio = Carbon::now()->startOfWeek();
        $fin    = Carbon::now()->endOfWeek();

        $this->fechaInicio = $inicio->toDateString();
        $this->fechaFin    = $fin->toDateString();

        $this->medicos = Medico::query()
            ->with('usuario')
            ->orderBy('cod_med')
            ->get()
            ->map(function ($m) {
                return [
                    'cod' => $m->cod_med ?? $m->COD_MED,
                    'nombre' => $m->usuario->name ?? ('Médico '.$m->cod_med),
                ];
            });

        $this->buildTendencia();
    }

    public function updated($prop)
    {
        // Si cambian filtros o búsqueda, actualizamos el sparkline
        if (in_array($prop, ['buscar','filtroMedico','filtroEstado','filtroTipo','fechaInicio','fechaFin'])) {
            $this->buildTendencia();
        }
    }

    public function getSemanaProperty(): Collection
    {
        $ini = Carbon::parse($this->fechaInicio)->startOfDay();
        $fin = Carbon::parse($this->fechaFin)->endOfDay();

        $dias = collect();
        $cursor = $ini->copy();
        while ($cursor->lte($fin)) {
            $dias->push($cursor->copy());
            $cursor->addDay();
        }
        return $dias;
    }

    public function getCitasFiltradasProperty(): Collection
    {
        $ini = Carbon::parse($this->fechaInicio)->startOfDay();
        $fin = Carbon::parse($this->fechaFin)->endOfDay();

        $q = Cita::query()
            ->with([
                'medico.usuario',
                'paciente.usuario',
                'solicitante',
            ])
            ->whereBetween('fec_cit', [$ini, $fin]);

        if ($this->filtroMedico) {
            $cod = $this->filtroMedico;
            $q->where(function($w) use ($cod) {
                $w->where('cod_med', $cod)->orWhere('COD_MED', $cod);
            });
        }

        if ($this->filtroEstado) {
            $estado = strtolower($this->filtroEstado);
            $q->where(function($w) use ($estado) {
                $w->whereRaw('LOWER(est_cit) = ?', [$estado]);
            });
        }

        if ($this->filtroTipo) {
            $tipo = strtolower($this->filtroTipo);
            $q->where(function($w) use ($tipo) {
                $w->whereRaw('LOWER(tip_cit) = ?', [$tipo]);
            });
        }

        if (strlen($this->buscar) >= 2) {
            $term = mb_strtolower(trim($this->buscar));
            $like = "%{$term}%";

            $q->where(function($w) use ($like) {
                // Médico
                $w->orWhereHas('medico.usuario', function($q2) use ($like) {
                    $q2->whereRaw('LOWER(name) LIKE ?', [$like]);
                });
                // Paciente
                $w->orWhereHas('paciente.usuario', function($q3) use ($like) {
                    $q3->whereRaw('LOWER(name) LIKE ?', [$like]);
                });
                // Solicitante
                $w->orWhereHas('solicitante', function($q4) use ($like) {
                    $q4->whereRaw('LOWER(nom_sol) LIKE ?', [$like])
                       ->orWhereRaw('LOWER(ap_pat_sol) LIKE ?', [$like])
                       ->orWhereRaw('LOWER(ap_mat_sol) LIKE ?', [$like]);
                });
                // Motivo
                $w->orWhereRaw('LOWER(mot_cit) LIKE ?', [$like]);
                // Códigos
                $w->orWhereRaw('LOWER(cod_cit) LIKE ?', [$like]);
            });
        }

        return $q->orderBy('fec_cit', 'asc')->get();
    }

    public function openDetalle(string $codCit): void
    {
        $c = Cita::with(['medico.usuario','paciente.usuario','solicitante'])
            ->where('cod_cit', $codCit)->orWhere('COD_CIT', $codCit)->first();

        if (!$c) return;

        $pacienteNombre = $c->paciente?->usuario?->name;
        $soli = $c->solicitante;
        $solNombre = $soli ? trim(($soli->nom_sol ?? '').' '.($soli->ap_pat_sol ?? ' ').' '.($soli->ap_mat_sol ?? '')) : null;

        $this->detalle = [
            'cod_cit' => $c->cod_cit ?? $c->COD_CIT,
            'fecha'   => Carbon::parse($c->fec_cit)->translatedFormat('l d \d\e F Y'),
            'hora'    => Carbon::parse($c->fec_cit)->format('H:i'),
            'estado'  => ucfirst(mb_strtolower($c->est_cit ?? 'Programada')),
            'tipo'    => ucfirst(mb_strtolower($c->tip_cit ?? 'Presencial')),
            'motivo'  => $c->mot_cit ?? 'Consulta',
            'medico'  => $c->medico?->usuario?->name ?? '—',
            'persona' => $pacienteNombre ?: ($solNombre ?: '—'),
            'esPaciente' => (bool)($c->cod_pac ?? $c->COD_PAC),
        ];

        $this->showModal = true;
    }

    public function closeDetalle(): void
    {
        $this->showModal = false;
        $this->detalle = null;
    }

    protected function buildTendencia(): void
    {
        // Últimos 7 días desde hoy
        $labels = [];
        $datos  = [];

        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::now()->subDays($i);
            $labels[] = $dia->isoFormat('dd D');

            $q = Cita::query()->whereBetween('fec_cit', [$dia->copy()->startOfDay(), $dia->copy()->endOfDay()]);

            if ($this->filtroMedico) {
                $cod = $this->filtroMedico;
                $q->where(function($w) use ($cod) {
                    $w->where('cod_med', $cod)->orWhere('COD_MED', $cod);
                });
            }
            if ($this->filtroEstado) {
                $estado = strtolower($this->filtroEstado);
                $q->whereRaw('LOWER(est_cit) = ?', [$estado]);
            }
            if ($this->filtroTipo) {
                $tipo = strtolower($this->filtroTipo);
                $q->whereRaw('LOWER(tip_cit) = ?', [$tipo]);
            }
            if (strlen($this->buscar) >= 2) {
                $term = mb_strtolower(trim($this->buscar));
                $like = "%{$term}%";
                $q->where(function($w) use ($like) {
                    $w->orWhereRaw('LOWER(mot_cit) LIKE ?', [$like])
                      ->orWhereRaw('LOWER(cod_cit) LIKE ?', [$like])
                      ->orWhereHas('medico.usuario', fn($s)=>$s->whereRaw('LOWER(name) LIKE ?', [$like]))
                      ->orWhereHas('paciente.usuario', fn($s)=>$s->whereRaw('LOWER(name) LIKE ?', [$like]))
                      ->orWhereHas('solicitante', function($s) use ($like){
                          $s->whereRaw('LOWER(nom_sol) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(ap_pat_sol) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(ap_mat_sol) LIKE ?', [$like]);
                      });
                });
            }

            $datos[] = $q->count();
        }

        $this->tendenciaLabels = $labels;
        $this->tendenciaDatos  = $datos;
    }

    public function render()
    {
        $citas = $this->citasFiltradas;

        // Resumen simple para “promedio de atención” y “cancelaciones”
        $total = $citas->count();
        $atendidas = $citas->where(fn($c)=>strtolower($c->est_cit ?? '')==='atendida')->count();
        $canceladas = $citas->where(fn($c)=>strtolower($c->est_cit ?? '')==='cancelada')->count();
        $promedioAtencion = $total ? round(($atendidas/$total)*100) : 0;
        $tasaCancel = $total ? round(($canceladas/$total)*100) : 0;

        return view('livewire.agenda-admin', [
            'citas' => $citas,
            'total' => $total,
            'promedioAtencion' => $promedioAtencion,
            'tasaCancel' => $tasaCancel,
        ]);
    }
}
