<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medico', function (Blueprint $table) {
            $table->string('cod_med', 10)->primary(); // Ej: MED01
            $table->string('cod_usu', 10);
            $table->foreign('cod_usu')->references('cod_usu')->on('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico');
    }
};
