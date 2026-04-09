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
    ];
}
