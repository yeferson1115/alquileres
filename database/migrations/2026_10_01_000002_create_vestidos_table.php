<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vestidos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('descripcion');
            $table->string('talla');
            $table->string('color');
            $table->enum('estado', ['DISPONIBLE', 'RESERVADO', 'EN_ALQUILER', 'RETRASADO', 'EN_LAVANDERIA', 'EN_PLANCHADO'])->default('DISPONIBLE');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vestidos');
    }
};
