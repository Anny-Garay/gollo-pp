<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $fillable = [
        'participante_id',
        'ruta',
        'tipo',
    ];

    public function participante()
    {
        return $this->belongsTo(Participante::class);
    }
}
