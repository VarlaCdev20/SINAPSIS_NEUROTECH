<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On; // 👈 importante para escuchar eventos desde JS
use Illuminate\Support\Facades\Auth;

class ListaUsuarios extends Component
{
    use WithPagination;

    public $search;
    public $n_registros = 10;

    /**
     * Escucha el evento 'estado' emitido desde el frontend (SweetAlert confirmación)
     */
    #[On('estado')]
    public function estado($codigo)
    {
        $user = User::where('cod_usu', $codigo)->first();

        if (!$user) {
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Usuario no encontrado',
                'text'  => 'No se encontró el usuario especificado.'
            ]);
            return;
        }

        // 🚫 Evitar que el usuario actual se desactive a sí mismo
        if (Auth::user() && Auth::user()->cod_usu === $user->cod_usu) {
            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'Acción no permitida',
                'text'  => 'No puedes desactivar tu propia cuenta.'
            ]);
            return;
        }

        // ✅ Alternar estado (1 -> 0 / 0 -> 1)
        $user->estado = $user->estado == 1 ? 0 : 1;
        $user->save();

        // ✅ Notificación de éxito
        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Estado actualizado',
            'text'  => "El usuario {$user->name} ahora está " . ($user->estado ? 'Activo' : 'Inactivo') . '.'
        ]);
    }

    /**
     * Renderiza los usuarios con búsqueda y paginación.
     */
    public function render()
    {
        $users = User::with('roles')
            ->whereRaw('LOWER(users.carnet) LIKE ?', ['%' . strtolower($this->search) . '%'])
            ->orderBy('id', 'desc')
            ->paginate($this->n_registros);

        return view('livewire.lista-usuarios', compact('users'));
    }
}
