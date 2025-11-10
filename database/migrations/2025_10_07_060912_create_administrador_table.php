<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrador', function (Blueprint $table) {
            $table->string('cod_adm', 10)->primary(); // Ej: ADM01
            $table->string('cod_usu', 10);
            $table->foreign('cod_usu')->references('cod_usu')->on('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrador');
    }
};
