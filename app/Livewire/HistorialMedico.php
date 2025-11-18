<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Historial;
use App\Models\Paciente;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

class HistorialMedico extends Component
{
    use WithPagination;

    // ============================================================
    // 🎛️ Filtros y vista
    // ============================================================

    public $buscar = '';
    public $vista = 'cards';
    public $n_registros = 10;

    // ============================================================
    // 🧩 Modales
    // ============================================================

    public $modalCrear = false;
    public $modalDetalle = false;

    // ============================================================
    // 🔍 Buscar paciente
    // ============================================================

    public $buscarPaciente = '';
    public $pacientes = [];
    public $pacienteSeleccionadoId = null;
    public $pacienteSeleccionadoNombre = null;
    public $mensajePaciente = null;

    // Campos del historial
    public $ANT_PER_HIS;
    public $ANT_FAM_HIS;
    public $ALE_HIS;
    public $TRA_PRE_HIS;
    public $OBS_GEN_HIS;
    public $HAB_ALI_HIS;
    public $PES_HIS;
    public $ALT_HIS;
    public $TIP_SAN_HIS;

    // Modal detalle
    public $historialDetalle;

    // ============================================================
    // 🧪 Validaciones
    // ============================================================

    protected $rules = [
        'pacienteSeleccionadoId' => 'required|unique:historial,cod_pac',
        'PES_HIS' => 'nullable|numeric',
        'ALT_HIS' => 'nullable|numeric',
        'TIP_SAN_HIS' => 'nullable|string|max:5',
    ];

    protected $messages = [
        'pacienteSeleccionadoId.required' => 'Debe seleccionar un paciente.',
        'pacienteSeleccionadoId.unique'   => 'Este paciente ya tiene un historial registrado.',
    ];

    // ============================================================
    // 🔁 Eventos Livewire
    // ============================================================

    public function updatingBuscar()
    {
        $this->resetPage();
    }
    public function updatingNRegistros()
    {
        $this->resetPage();
    }

    public function cambiarVista($vista)
    {
        $this->vista = $vista;
    }

    // ============================================================
    // ➕ ABRIR / CERRAR MODAL CREAR
    // ============================================================

    public function abrirModalCrear()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->modalCrear = true;
    }

    public function cerrarModalCrear()
    {
        $this->modalCrear = false;

        $this->reset([
            'buscarPaciente',
            'pacientes',
            'pacienteSeleccionadoId',
            'pacienteSeleccionadoNombre',
            'mensajePaciente',
            'ANT_PER_HIS',
            'ANT_FAM_HIS',
            'ALE_HIS',
            'TRA_PRE_HIS',
            'OBS_GEN_HIS',
            'HAB_ALI_HIS',
            'PES_HIS',
            'ALT_HIS',
            'TIP_SAN_HIS',
        ]);
    }

    // ============================================================
    // 🔎 Buscar paciente — SOLO ELOQUENT ❤️
    // ============================================================

    public function updatedBuscarPaciente()
    {
        $this->mensajePaciente = null;
        $texto = trim($this->buscarPaciente);

        if ($texto === '') {
            $this->pacientes = [];
            return;
        }

        $this->pacientes = Paciente::with('usuario')
            ->whereHas('usuario', function ($q) use ($texto) {
                $q->where('name', 'LIKE', "%$texto%")
                    ->orWhere('paterno', 'LIKE', "%$texto%")
                    ->orWhere('materno', 'LIKE', "%$texto%")
                    ->orWhere('carnet', 'LIKE', "%$texto%");
            })
            ->take(8)
            ->get();
    }

    public function seleccionarPaciente($codPac)
    {
        $pac = Paciente::with('usuario')->find($codPac);

        if (!$pac) {
            $this->mensajePaciente = 'Paciente no encontrado.';
            return;
        }

        if (Historial::where('cod_pac', $codPac)->exists()) {
            $this->pacienteSeleccionadoId = null;
            $this->mensajePaciente = 'Este paciente ya tiene un historial registrado.';
            return;
        }

        $u = $pac->usuario;

        $this->pacienteSeleccionadoId = $codPac;
        $this->pacienteSeleccionadoNombre = "{$u->name} {$u->paterno} {$u->materno}";

        $this->pacientes = [];
        $this->buscarPaciente = $this->pacienteSeleccionadoNombre;
    }

    // ============================================================
    // 💾 GUARDAR HISTORIAL
    // ============================================================

    public function guardarHistorial()
    {
        $this->validate();

        $hist = Historial::create([
            'cod_pac'       => $this->pacienteSeleccionadoId,
            'fec_cre_his'   => now(),
            'ant_per_his'   => $this->ANT_PER_HIS,
            'ant_fam_his'   => $this->ANT_FAM_HIS,
            'ale_his'       => $this->ALE_HIS,
            'tra_pre_his'   => $this->TRA_PRE_HIS,
            'obs_gen_his'   => $this->OBS_GEN_HIS,
            'hab_ali_his'   => $this->HAB_ALI_HIS,
            'pes_his'       => $this->PES_HIS,
            'alt_his'       => $this->ALT_HIS,
            'tip_san_his'   => $this->TIP_SAN_HIS,
        ]);

        $pac = Paciente::with('usuario')->find($hist->cod_pac);
        $u = $pac->usuario;
        $med = Auth::user();

        Bitacora::create([
            'cod_usu' => $med->cod_usu,
            'acc_bit' => "El médico {$med->name} {$med->paterno} {$med->materno} registró el historial {$hist->cod_his} para el paciente {$u->name} {$u->paterno} {$u->materno}.",
            'fec_hor_bit' => now(),
        ]);

        $this->dispatch('historial-creado');

        $this->cerrarModalCrear();
    }

    // ============================================================
    // 👁 Modal detalle
    // ============================================================

    public function abrirModalDetalle($id)
    {
        $this->historialDetalle = Historial::with(['paciente.usuario'])->find($id);
        $this->modalDetalle = true;
    }

    public function cerrarModalDetalle()
    {
        $this->modalDetalle = false;
        $this->historialDetalle = null;
    }

    // ============================================================
    // 📈 Gráfico
    // ============================================================

    public function getDataGraficoProperty()
    {
        $data = Historial::selectRaw("
            MONTH(fec_cre_his) as mes,
            COUNT(*) as total
        ")
            ->whereYear('fec_cre_his', now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return [
            'meses'   => $data->pluck('mes'),
            'totales' => $data->pluck('total'),
        ];
    }

    // ============================================================
    // 🎨 RENDER — CARGA SIEMPRE RELACIONES
    // ============================================================

    public function render()
    {
        $texto = trim($this->buscar);

        $query = Historial::with('paciente.usuario');

        if ($texto !== '') {
            $query->where('cod_his', 'LIKE', "%$texto%")
                ->orWhereHas('paciente.usuario', function ($q) use ($texto) {
                    $q->where('name', 'LIKE', "%$texto%")
                        ->orWhere('paterno', 'LIKE', "%$texto%")
                        ->orWhere('materno', 'LIKE', "%$texto%")
                        ->orWhere('carnet', 'LIKE', "%$texto%");
                });
        }

        // 📊 MÉTRICAS REALISTAS
        $totalHistoriales = Historial::count();

        $historialesEsteMes = Historial::whereMonth('fec_cre_his', now()->month)
            ->whereYear('fec_cre_his', now()->year)
            ->count();

        $totalPacientes = \App\Models\Paciente::count();

        $pacientesSinHistorial = \App\Models\Paciente::whereDoesntHave('historial')->count();

        // 📈 DATOS DEL GRÁFICO
        $dataGrafico = $this->dataGrafico;

        return view('livewire.historial-medico', [
            'historiales' => $query
                ->orderBy('fec_cre_his', 'desc')
                ->paginate($this->n_registros),

            'grafico'               => $dataGrafico,
            'totalHistoriales'      => $totalHistoriales,
            'historialesEsteMes'    => $historialesEsteMes,
            'totalPacientes'        => $totalPacientes,
            'pacientesSinHistorial' => $pacientesSinHistorial,
        ]);
    }
}
