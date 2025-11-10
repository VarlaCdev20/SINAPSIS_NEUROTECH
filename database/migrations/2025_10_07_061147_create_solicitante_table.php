<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitantes', function (Blueprint $table) {
            $table->string('cod_sol', 10)->primary(); // Ej: SOL01
            $table->string('nom_sol', 100);
            $table->string('ap_pat_sol', 100)->nullable();
            $table->string('ap_mat_sol', 100)->nullable();
            $table->date('fec_nac_sol')->nullable();
            $table->string('est_sol', 20)->nullable();
            $table->string('email_sol', 150)->unique()->nullable();
            $table->string('cel_sol', 20)->nullable();
            $table->string('dir_sol', 100)->nullable();
            $table->string('ci_sol', 20)->unique()->nullable();
            $table->text('des_sol')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitantes');
    }
};
