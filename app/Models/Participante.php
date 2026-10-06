<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    protected $fillable = [
        'nombre',
        'cedula',
        'celular',
        'email',
        'foto',
        'humana_score',
        'angulo_menique',
        'imagen_ruta',
        'cupon_codigo',
        'cupon_monto',
        'cupon_monto_texto',
    ];
}
