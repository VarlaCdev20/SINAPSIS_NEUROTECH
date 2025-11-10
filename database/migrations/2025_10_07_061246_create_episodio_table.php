<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodio', function (Blueprint $table) {
            $table->string('cod_epi', 10)->primary(); // Ej: EPI01
            $table->string('cod_pac', 10);
            $table->string('cod_med', 10);
            $table->date('fec_ini_epi')->nullable();
            $table->date('fec_fin_epi')->nullable();
            $table->string('dur_epi', 20)->nullable();
            $table->string('int_epi', 20)->nullable();
            $table->text('des_epi')->nullable();
            $table->string('est_emo_epi', 50)->nullable();
            $table->string('int_med_epi', 100)->nullable();
            $table->boolean('hosp_epi')->default(false);
            $table->text('obs_cli_epi')->nullable();

            $table->foreign('cod_pac')->references('cod_pac')->on('pacientes');
            $table->foreign('cod_med')->references('cod_med')->on('medico');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodio');
    }
};
