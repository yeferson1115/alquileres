<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alquiler extends Model
{
    use HasFactory;

    protected $table = 'alquileres';

    protected $fillable = [
        'cliente_id', 'vestido_id', 'valor_total', 'valor_abono', 
        'saldo_pendiente', 'fecha_entrega_est', 'fecha_salida_real', 
        'fecha_devolucion_limite', 'fecha_devolucion_real', 'estado_alquiler'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vestido()
    {
        return $this->belongsTo(Vestido::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
