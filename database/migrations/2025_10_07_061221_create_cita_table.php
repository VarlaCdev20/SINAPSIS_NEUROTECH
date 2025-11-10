<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cita', function (Blueprint $table) {
            $table->string('cod_cit', 10)->primary(); // Ej: CIT01
            $table->string('cod_sol', 10);
            $table->string('cod_pac', 10);
            $table->dateTime('fec_cit')->nullable();
            $table->string('cod_med', 10);
            $table->string('tip_cit', 50)->nullable();
            $table->string('mot_cit', 150)->nullable();
            $table->string('est_cit', 20)->nullable();
            $table->string('rep_cit', 100)->nullable();
            $table->dateTime('fec_reg_cit')->nullable();

            $table->foreign('cod_sol')->references('cod_sol')->on('solicitantes');
            $table->foreign('cod_med')->references('cod_med')->on('medico');
            $table->foreign('cod_pac')->references('cod_pac')->on('pacientes');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita');
    }
};
