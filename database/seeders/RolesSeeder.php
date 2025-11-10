<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ Crear roles solo si no existen
        $role1 = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $role2 = Role::firstOrCreate(['name' => 'Medico', 'guard_name' => 'web']);
        $role3 = Role::firstOrCreate(['name' => 'Paciente', 'guard_name' => 'web']);

        // ✅ Lista de permisos con sus roles asociados
        $permisos = [
            'Panel' => [$role1, $role2, $role3],
            'Gestion_usuarios' => [$role1],
            'Pacientes' => [$role1],
            'Mis_Pacientes' => [$role2],
            'Agenda' => [$role2],
            'Reseñas' => [$role1],
            'Ajustes' => [$role1, $role2, $role3],
            'Solicitudes' => [$role2],
            'Calendario_Medico' => [$role2],
            'Calendario_Paciente' => [$role3],
            'Episodios' => [$role2],
            'Mis_Episodios' => [$role3],
            'Reportes_Administrativos' => [$role1],
            'Reportes_Medicos' => [$role2],
            'Historial_Clinico' => [$role2],
            'Mi_Historial_Clinico' => [$role3],
        ];

        // ✅ Crear permisos si no existen y asignarlos a los roles correspondientes
        foreach ($permisos as $nombre => $roles) {
            $permiso = Permission::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);
            $permiso->syncRoles($roles);
        }
    }
}
