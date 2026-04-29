<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelTexto extends Model
{
    protected $table = 'nivel_textos';

    protected $fillable = ['nivel', 'titulo', 'contenido'];

    /**
     * Devuelve el número de nivel (1–5) según el ángulo del meñique.
     * Rangos: 0–4, 5–8, 9–12, 13–16, 17–20
     */
    public static function nivelDesdeAngulo(float $angulo): int
    {
        if ($angulo <= 4)  return 1;
        if ($angulo <= 8)  return 2;
        if ($angulo <= 12) return 3;
        if ($angulo <= 16) return 4;
        return 5;
    }
}
