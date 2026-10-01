<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alquileres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('vestido_id')->constrained('vestidos');
            
            $table->decimal('valor_total', 10, 2);
            $table->decimal('valor_abono', 10, 2);
            $table->decimal('saldo_pendiente', 10, 2);
            
            $table->date('fecha_entrega_est');
            $table->dateTime('fecha_salida_real')->nullable();
            $table->date('fecha_devolucion_limite')->nullable();
            $table->dateTime('fecha_devolucion_real')->nullable();
            
            $table->string('estado_alquiler'); // Ej: RESERVADO, ACTIVO, DEVUELTO, CANCELADO
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alquileres');
    }
};
