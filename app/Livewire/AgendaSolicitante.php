<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Solicitante;
use App\Models\Cita;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgendaSolicitante extends Component
{
    public $mostrarModal = false;

    // Campos del formulario
    public $cod_cit;
    public $cod_sol;
    public $tip_cit;
    public $fec_cit;
    public $mot_cit;

    public $solicitantesAprobados = [];

    protected $listeners = [
        'abrirAgenda' => 'abrirModal'
    ];

    /**
     * Cargar solicitantes aprobados al iniciar el componente
     */
    public function mount()
    {
        $this->solicitantesAprobados = Solicitante::where('est_sol', 'aprobado')
            ->orderBy('nom_sol')
            ->get();
    }

    /**
     * Abrir modal y generar código de cita preliminar
     */
    public function abrirModal()
    {
        $this->resetForm();

        // Generación previa de código de cita
        $lastCit = Cita::orderBy('cod_cit', 'desc')->first();
        $num = $lastCit ? intval(substr($lastCit->cod_cit, 3)) + 1 : 1;
        $this->cod_cit = 'CIT' . str_pad($num, 3, '0', STR_PAD_LEFT);

        $this->mostrarModal = true;
    }

    /**
     * Cerrar modal y limpiar campos
     */
    public function resetForm()
    {
        $this->reset([
            'cod_cit',
            'cod_sol',
            'tip_cit',
            'fec_cit',
            'mot_cit'
        ]);
    }

    /**
     * Guardar cita
     */
    public function guardarCita()
    {
        $this->validate([
            'cod_sol' => 'required|exists:solicitantes,cod_sol',
            'tip_cit' => 'required|in:virtual,presencial',
            'fec_cit' => 'required|date|after:now',
            'mot_cit' => 'required|string|min:3',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $medico = $user->medico ?? null;

            if (!$medico) {
                throw new \Exception("No se encontró el médico asociado al usuario.");
            }

            $cita = Cita::create([
                'cod_cit' => $this->cod_cit,
                'cod_sol' => $this->cod_sol,
                'cod_med' => $medico->cod_med,
                'cod_pac' => null, // Se definirá después
                'tip_cit' => $this->tip_cit,
                'mot_cit' => $this->mot_cit,
                'fec_cit' => $this->fec_cit,
                'est_cit' => 'Registrado',
                'fec_reg_cit' => Carbon::now(),
            ]);

            // Registro de bitácora
            Bitacora::create([
                'cod_usu' => $user->cod_usu ?? $user->id,
                'acc_bit' => "Registro de cita {$cita->cod_cit} para solicitante {$this->cod_sol}",
                'fec_hor_bit' => Carbon::now(),
            ]);

            DB::commit();

            $this->mostrarModal = false;
            $this->resetForm();

            $this->dispatch('citaGuardada');

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('errorAlGuardar', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.agenda-solicitante');
    }
}
