<?php

namespace Database\Seeders;

use App\Models\Pais;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaisTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $paises = [
            ['nombre' => 'Bolivia'],
            ['nombre' => 'Argentina'],
            ['nombre' => 'Chile'],
            ['nombre' => 'Perú'],
            ['nombre' => 'Brasil'],
        ];

        foreach ($paises as $pais) {
            Pais::create($pais);
        }
    }
}
