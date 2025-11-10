<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Solicitante;

class RegistrarPaciente extends Component
{
    public $buscar = '';
    public $solicitantes = [];
    public $seleccionado = null;

    // Campos que se autocompletarán
    public $name, $paterno, $materno, $carnet, $celular, $direccion, $fecha_nacimiento, $email;

    // 🔍 Buscar solicitantes aprobados
    public function updatedBuscar()
    {
        $this->solicitantes = Solicitante::where('est_sol', 'aprobado')
            ->where(function ($q) {
                $q->where('nom_sol', 'like', "%{$this->buscar}%")
                    ->orWhere('ap_pat_sol', 'like', "%{$this->buscar}%")
                    ->orWhere('ap_mat_sol', 'like', "%{$this->buscar}%")
                    ->orWhere('ci_sol', 'like', "%{$this->buscar}%")
                    ->orWhere('email_sol', 'like', "%{$this->buscar}%");
            })
            ->take(5)
            ->get();
    }

    // ✅ Al seleccionar un solicitante
    public function seleccionar($cod_sol)
    {
        $s = Solicitante::where('cod_sol', $cod_sol)->first();

        if ($s) {
            $this->seleccionado = $s->cod_sol;
            $this->name = $s->nom_sol;
            $this->paterno = $s->ap_pat_sol;
            $this->materno = $s->ap_mat_sol;
            $this->carnet = $s->ci_sol;
            $this->celular = $s->cel_sol;
            $this->direccion = $s->dir_sol;
            $this->fecha_nacimiento = $s->fec_nac_sol ? date('Y-m-d', strtotime($s->fec_nac_sol)) : null;
            $this->email = $s->email_sol;

            // ✅ CORRECTO: enviar argumentos nombrados
            $this->dispatch(
                'rellenarCampos',
                name: $this->name,
                paterno: $this->paterno,
                materno: $this->materno,
                carnet: $this->carnet,
                celular: $this->celular,
                direccion: $this->direccion,
                fecha_nacimiento: $this->fecha_nacimiento,
                email: $this->email
            );
        }
    }

    public function render()
    {
        return view('livewire.registrar-paciente');
    }
}
