<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vestido extends Model
{
    use HasFactory;
    protected $table = 'vestidos';

    protected $fillable = ['codigo', 'descripcion', 'talla', 'color', 'estado'];

    public function alquileres()
    {
        return $this->hasMany(Alquiler::class);
    }
}
