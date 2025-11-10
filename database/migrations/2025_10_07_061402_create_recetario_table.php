
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recetario', function (Blueprint $table) {
            $table->string('cod_rec', 10)->primary(); // Ej: REC01
            $table->string('cod_pac', 10);
            $table->string('cod_med', 10);
            $table->string('cod_epi', 10);
            $table->string('tit_rec', 100)->nullable();
            $table->text('des_rec')->nullable();
            $table->string('dia_rec', 150)->nullable();
            $table->date('fec_emi_rec')->nullable();

            $table->foreign('cod_pac')->references('cod_pac')->on('pacientes');
            $table->foreign('cod_med')->references('cod_med')->on('medico');
            $table->foreign('cod_epi')->references('cod_epi')->on('episodio');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recetario');
    }
};
