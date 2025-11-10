<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $user=User::create([
            'cod_usu' => 'US-001',
            'name' => 'Victor',
            'paterno' => 'Asturizaga',
            'materno' => 'Plata',
            'celular' => '123456789',
            'direccion' => 'Calle Falsa 123',
            'fecha_nacimiento' => '1990-01-01',
            'email' => 'asturizagavictor@gmail.com',
            'carnet' => '1234567',
            'password' => Hash::make('victor123'),
            'estado' => true,
            'ocupacion' => 'Médico',
            'ciudad_id'=>1
        ]);
         // Asignar el rol "admin" (o cualquier rol que tengas definido)
         $user->assignRole('Administrador'); // Asegúrate de que el rol "admin" esté creado previamente
    }
}
