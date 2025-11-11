<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Solicitante;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Carbon\Carbon;

class Solicitudes extends Component
{
    public $buscar = '';
    public $n_registros = '';

    // 🔄 Escucha el refresh global sin recrear componentes hijos
    protected $listeners = ['refreshComponent' => '$refresh'];

    /**
     * 🧠 Render principal del componente: filtra y muestra solicitantes
     */
    public function render()
    {
        $query = Solicitante::query();

        // 🔎 Filtro de búsqueda flexible
        if (!empty($this->buscar)) {
            $busqueda = '%' . trim($this->buscar) . '%';
            $query->where(function ($q) use ($busqueda) {
                $q->where('nom_sol', 'ILIKE', $busqueda)
                    ->orWhere('ap_pat_sol', 'ILIKE', $busqueda)
                    ->orWhere('ap_mat_sol', 'ILIKE', $busqueda)
                    ->orWhere('email_sol', 'ILIKE', $busqueda)
                    ->orWhere('cod_sol', 'ILIKE', $busqueda);
            });
        }

        // 🔢 Límite de registros mostrado
        if (!empty($this->n_registros) && is_numeric($this->n_registros)) {
            $solicitantes = $query->orderBy('cod_sol', 'desc')
                ->take($this->n_registros)
                ->get();
        } else {
            $solicitantes = $query->orderBy('cod_sol', 'desc')->get();
        }

        return view('livewire.solicitudes', [
            'solicitantes' => $solicitantes
        ]);
    }

    /**
     * 🟢 Dispara alerta de confirmación (Aprobación)
     */
    public function aprobar($cod_sol)
    {
        if (empty($cod_sol)) return;
        $this->dispatch('confirmarAprobacion', $cod_sol);
    }

    /**
     * 🔴 Dispara alerta de confirmación (Rechazo)
     */
    public function rechazar($cod_sol)
    {
        if (empty($cod_sol)) return;
        $this->dispatch('confirmarRechazo', $cod_sol);
    }

    /**
     * 🟢 Acción de aprobación confirmada (evento global)
     */
    #[On('confirmadoAprobacion')]
    public function confirmadoAprobacion($codSol)
    {
        if (!$codSol) return;

        $sol = Solicitante::where('cod_sol', $codSol)->first();
        if (!$sol) return;

        $sol->update(['est_sol' => 'aprobado']);

        // 🧠 Registrar en bitácora
        $this->registrarBitacora("Aprobó la solicitud de {$sol->getNombreCompletoAttribute()}");

        // 🟢 Alerta visual
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Solicitud aprobada',
            'text'  => "El solicitante {$sol->getNombreCompletoAttribute()} fue aprobado correctamente.",
            'refresh' => true,
        ]);

        logger()->info("✅ Evento confirmadoAprobacion recibido", ['codSol' => $codSol]);
    }

    /**
     * 🔴 Acción de rechazo confirmada (evento global)
     */
    #[On('confirmadoRechazo')]
    public function confirmadoRechazo($codSol)
    {
        if (!$codSol) return;

        $sol = Solicitante::where('cod_sol', $codSol)->first();
        if (!$sol) return;

        $sol->update(['est_sol' => 'rechazado']);

        // 🧠 Registrar en bitácora
        $this->registrarBitacora("Rechazó la solicitud de {$sol->getNombreCompletoAttribute()}");

        // 🔴 Alerta visual
        $this->dispatch('swal', [
            'icon' => 'info',
            'title' => 'Solicitud rechazada',
            'text'  => "El solicitante {$sol->getNombreCompletoAttribute()} fue rechazado.",
            'refresh' => true,
        ]);

        logger()->info("✅ Evento confirmadoRechazo recibido", ['codSol' => $codSol]);
    }

    /**
     * 📋 Registrar en bitácora quién realiza la acción
     */
    protected function registrarBitacora($accion)
    {
        try {
            $user = Auth::user();
            if (!$user) return;

            Bitacora::create([
                'cod_usu'     => $user->cod_usu ?? null,
                'acc_bit'     => $accion,
                'fec_hor_bit' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            logger()->error('Error al registrar en bitácora', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
