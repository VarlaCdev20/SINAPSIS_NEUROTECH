<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial', function (Blueprint $table) {
            $table->string('cod_his', 10)->primary(); // Ej: HIS01
            $table->string('cod_pac', 10);
            $table->date('fec_cre_his')->nullable();
            $table->text('ant_per_his')->nullable();
            $table->text('ant_fam_his')->nullable();
            $table->text('ale_his')->nullable();
            $table->text('tra_pre_his')->nullable();
            $table->text('obs_gen_his')->nullable();
            $table->text('hab_ali_his')->nullable();
            $table->decimal('pes_his', 5, 2)->nullable();
            $table->decimal('alt_his', 5, 2)->nullable();
            $table->string('tip_san_his', 10)->nullable();

            $table->foreign('cod_pac')->references('cod_pac')->on('pacientes');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial');
    }
};
