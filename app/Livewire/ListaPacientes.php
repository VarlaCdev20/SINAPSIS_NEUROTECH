<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Paciente;

class ListaPacientes extends Component
{
    use WithPagination;

    public $search = '';
    public $n_registros = 10;

    /**
     * 🔄 Reinicia la paginación cuando cambia la búsqueda
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * ⚙️ Cambiar estado del usuario (activar/desactivar)
     */
    public function estado($cod_usu)
    {
        $paciente = Paciente::with('usuario')->where('cod_usu', $cod_usu)->first();

        if (!$paciente || !$paciente->usuario) {
            session()->flash('error', 'No se encontró el paciente o su usuario asociado.');
            return;
        }

        $usuario = $paciente->usuario;
        $usuario->estado = $usuario->estado ? 0 : 1;
        $usuario->save();

        session()->flash('message', 'El estado del paciente fue actualizado correctamente.');
    }

    /**
     * 📋 Render principal
     */
    public function render()
    {
        // ✅ Traer pacientes con su usuario relacionado
        $pacientes = Paciente::with('usuario')
            ->whereHas('usuario', function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('paterno', 'like', "%{$this->search}%")
                    ->orWhere('materno', 'like', "%{$this->search}%")
                    ->orWhere('carnet', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->orderBy('cod_pac', 'desc')
            ->paginate($this->n_registros);

        // 🔹 Concatenar nombre completo dentro del componente
        $pacientes->getCollection()->transform(function ($pac) {
            if ($pac->usuario) {
                $pac->nombre_completo = trim(
                    "{$pac->usuario->name} {$pac->usuario->paterno} {$pac->usuario->materno}"
                );
            } else {
                $pac->nombre_completo = '—';
            }
            return $pac;
        });

        return view('livewire.lista-pacientes', compact('pacientes'));
    }
}
