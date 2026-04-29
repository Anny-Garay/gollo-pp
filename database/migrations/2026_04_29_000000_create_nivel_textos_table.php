<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nivel_textos', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nivel')->unique(); // 1–5
            $table->string('titulo');
            $table->longText('contenido'); // HTML del editor WYSIWYG
            $table->timestamps();
        });

        // Datos por defecto para los 5 niveles (0–4, 5–8, 9–12, 13–16, 17–20)
        DB::table('nivel_textos')->insert([
            [
                'nivel'      => 1,
                'titulo'     => 'NIVEL 1 — LEVE',
                'contenido'  => '<p>¡Tu meñique está en perfecto estado! Con un ángulo de 0 a 4°, apenas hay inclinación. Sin embargo, en Gollo siempre encontrarás el modelo ideal para que tu celular sea aún más cómodo.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nivel'      => 2,
                'titulo'     => 'NIVEL 2 — BAJO',
                'contenido'  => '<p>Se detecta una leve inclinación en tu meñique (5–8°). Nada grave por ahora, pero es una señal de que tu celular empieza a pesar. En Gollo tenemos modelos ultraligeros que marcan la diferencia.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nivel'      => 3,
                'titulo'     => 'NIVEL 3 — MODERADO',
                'contenido'  => '<p>Tu meñique muestra una inclinación moderada (9–12°). Tu cel ya te está cobrando la factura. Visita Gollo y descubrí los equipos diseñados para un agarre cómodo y natural.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nivel'      => 4,
                'titulo'     => 'NIVEL 4 — ALTO',
                'contenido'  => '<p>¡Atención! Tu meñique registra una inclinación importante (13–16°). Es hora de tomar acción. En Gollo te esperamos con opciones perfectas para darle un merecido descanso a tu dedo.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nivel'      => 5,
                'titulo'     => 'NIVEL 5 — SEVERO',
                'contenido'  => '<p>🚨 ¡SOS meñique en crisis! Con 17–20° de inclinación, tu dedo ya dio la señal de auxilio. Nuestros cálculos indican que necesitás un cambio urgente. Pasate por Gollo y rescatá a tu meñique hoy mismo.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('nivel_textos');
    }
};
