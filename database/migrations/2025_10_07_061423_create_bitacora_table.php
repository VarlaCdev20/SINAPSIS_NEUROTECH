<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->string('cod_bit', 10)->primary(); // Ej: BIT01
            $table->string('cod_usu', 10);
            $table->string('acc_bit', 200);
            $table->dateTime('fec_hor_bit');

            $table->foreign('cod_usu')->references('cod_usu')->on('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};
