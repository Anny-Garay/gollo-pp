<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Pinky5, Pinky10, Pinky15, Pinky20, Pinky24
            $table->string('nombre');
            $table->decimal('min_desviacion', 5, 2)->default(0);
            $table->decimal('max_desviacion', 5, 2)->nullable();
            $table->string('rango_texto'); // '4-8%', '9-10%', etc.
            $table->decimal('monto_descuento', 10, 2);
            $table->string('monto_texto'); // '5 mil colones', '10 mil colones', etc.
            $table->unsignedInteger('stock_total')->default(0);
            $table->unsignedInteger('stock_disponible')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar los 5 cupones según la tabla oficial
        DB::table('cupones')->insert([
            [
                'codigo'           => 'Pinky5',
                'nombre'           => 'Cupón Pinky 5K',
                'min_desviacion'   => 4.00,
                'max_desviacion'   => 8.99,
                'rango_texto'      => '4-8%',
                'monto_descuento'  => 5000.00,
                'monto_texto'      => '5 mil colones',
                'stock_total'      => 57,
                'stock_disponible' => 57,
                'activo'           => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'codigo'           => 'Pinky10',
                'nombre'           => 'Cupón Pinky 10K',
                'min_desviacion'   => 9.00,
                'max_desviacion'   => 10.99,
                'rango_texto'      => '9-10%',
                'monto_descuento'  => 10000.00,
                'monto_texto'      => '10 mil colones',
                'stock_total'      => 50,
                'stock_disponible' => 50,
                'activo'           => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'codigo'           => 'Pinky15',
                'nombre'           => 'Cupón Pinky 15K',
                'min_desviacion'   => 11.00,
                'max_desviacion'   => 12.99,
                'rango_texto'      => '11 a 12%',
                'monto_descuento'  => 15000.00,
                'monto_texto'      => '15 mil colones',
                'stock_total'      => 15,
                'stock_disponible' => 15,
                'activo'           => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'codigo'           => 'Pinky20',
                'nombre'           => 'Cupón Pinky 20K',
                'min_desviacion'   => 13.00,
                'max_desviacion'   => 14.99,
                'rango_texto'      => '13-14%',
                'monto_descuento'  => 20000.00,
                'monto_texto'      => '20 mil colones',
                'stock_total'      => 14,
                'stock_disponible' => 14,
                'activo'           => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'codigo'           => 'Pinky24',
                'nombre'           => 'Cupón Pinky 25K',
                'min_desviacion'   => 15.00,
                'max_desviacion'   => null,
                'rango_texto'      => 'Más de 15%',
                'monto_descuento'  => 25000.00,
                'monto_texto'      => '25 mil colones',
                'stock_total'      => 15,
                'stock_disponible' => 15,
                'activo'           => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cupones');
    }
};
