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

    protected function casts(): array
    {
        return [
            'valor_total' => 'decimal:2', 'valor_abono' => 'decimal:2', 'saldo_pendiente' => 'decimal:2',
            'fecha_entrega_est' => 'date', 'fecha_salida_real' => 'datetime',
            'fecha_devolucion_limite' => 'date', 'fecha_devolucion_real' => 'datetime',
        ];
    }

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
