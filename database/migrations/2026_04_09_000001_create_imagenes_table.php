<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participante_id')->constrained('participantes')->onDelete('cascade');
            $table->string('ruta');
            $table->string('tipo')->default('upload'); // 'camara' | 'upload'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imagenes');
    }
};
