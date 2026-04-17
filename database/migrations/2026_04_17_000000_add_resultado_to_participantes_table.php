<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->integer('humana_score')->nullable()->after('foto');
            $table->decimal('angulo_menique', 6, 2)->nullable()->after('humana_score');
            $table->string('imagen_ruta')->nullable()->after('angulo_menique');
        });
    }

    public function down(): void
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->dropColumn(['humana_score', 'angulo_menique', 'imagen_ruta']);
        });
    }
};
