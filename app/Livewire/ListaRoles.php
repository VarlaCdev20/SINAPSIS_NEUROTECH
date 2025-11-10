<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class ListaRoles extends Component
{
    use WithPagination;

    public $search = '';
    public $n_registros = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $roles = Role::query()
            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->search) . '%'])
            ->orderBy('id', 'desc')
            ->paginate($this->n_registros);

        return view('livewire.lista-roles', compact('roles'));
    }
}
