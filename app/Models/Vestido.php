<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vestido extends Model
{
    use HasFactory;

    public const DISPONIBLE = 'DISPONIBLE';
    public const RESERVADO = 'RESERVADO';
    public const EN_ALQUILER = 'EN_ALQUILER';
    public const RETRASADO = 'RETRASADO';
    public const EN_LAVANDERIA = 'EN_LAVANDERIA';
    public const EN_PLANCHADO = 'EN_PLANCHADO';

    public const ESTADOS = [self::DISPONIBLE, self::RESERVADO, self::EN_ALQUILER, self::RETRASADO, self::EN_LAVANDERIA, self::EN_PLANCHADO];
    protected $table = 'vestidos';

    protected $fillable = ['codigo', 'descripcion', 'talla', 'color', 'estado'];

    public function puedeCambiarA(string $nuevoEstado): bool
    {
        return in_array($nuevoEstado, match ($this->estado) {
            self::EN_LAVANDERIA => [self::EN_PLANCHADO],
            self::EN_PLANCHADO => [self::DISPONIBLE],
            default => [],
        }, true);
    }

    public function alquileres()
    {
        return $this->hasMany(Alquiler::class);
    }
}
