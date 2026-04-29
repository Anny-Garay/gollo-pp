<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = ['nombre', 'foto', 'precio', 'link_externo', 'orden', 'activo'];

    protected $casts = ['activo' => 'boolean', 'precio' => 'decimal:2'];
}
