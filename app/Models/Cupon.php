<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupones';

    protected $fillable = [
        'codigo',
        'nombre',
        'min_desviacion',
        'max_desviacion',
        'rango_texto',
        'monto_descuento',
        'monto_texto',
        'stock_total',
        'stock_disponible',
        'activo',
    ];

    protected $casts = [
        'min_desviacion'   => 'float',
        'max_desviacion'   => 'float',
        'monto_descuento'  => 'float',
        'stock_total'      => 'integer',
        'stock_disponible' => 'integer',
        'activo'           => 'boolean',
    ];

    /**
     * Encuentra el cupón correspondiente a un ángulo / porcentaje de desviación dado.
     */
    public static function obtenerPorDesviacion(?float $angulo): ?self
    {
        if ($angulo === null) {
            return null;
        }

        // Si es mayor o igual a 15%
        if ($angulo >= 15.0) {
            return self::where('activo', true)
                ->where('min_desviacion', '>=', 15.0)
                ->first()
                ?? self::where('codigo', 'Pinky24')->first();
        }

        return self::where('activo', true)
            ->where('min_desviacion', '<=', $angulo)
            ->where(function ($query) use ($angulo) {
                $query->whereNull('max_desviacion')
                      ->orWhere('max_desviacion', '>=', $angulo);
            })
            ->orderBy('min_desviacion', 'desc')
            ->first();
    }

    /**
     * Cantidad de participantes que tienen este cupón asignado.
     */
    public function getEntregadosAttribute(): int
    {
        return Participante::where('cupon_codigo', $this->codigo)->count();
    }

    /**
     * Monto formateado en colones con símbolo (ej. ₡15.000).
     */
    public function getMontoColonesAttribute(): string
    {
        return '₡' . number_format($this->monto_descuento, 0, ',', '.');
    }
}
