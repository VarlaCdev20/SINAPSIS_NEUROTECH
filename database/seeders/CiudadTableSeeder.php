<?php

namespace Database\Seeders;

use App\Models\Ciudad;
use App\Models\Pais;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CiudadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $ciudadesPorPais = [
            'Bolivia' => ['La Paz', 'Cochabamba', 'Santa Cruz', 'Sucre', 'Tarija'],
            'Argentina' => ['Buenos Aires', 'Córdoba', 'Rosario', 'Mendoza'],
            'Chile' => ['Santiago', 'Valparaíso', 'Concepción', 'Antofagasta'],
            'Perú' => ['Lima', 'Cusco', 'Arequipa', 'Trujillo'],
            'Brasil' => ['Brasilia', 'São Paulo', 'Río de Janeiro', 'Curitiba'],
        ];

        foreach ($ciudadesPorPais as $nombrePais => $ciudades) {
            $pais = Pais::where('nombre', $nombrePais)->first();
            if ($pais) {
                foreach ($ciudades as $nombreCiudad) {
                    Ciudad::create([
                        'nombre' => $nombreCiudad,
                        'pais_id' => $pais->id,
                    ]);
                }
            }
        }
    }
}
