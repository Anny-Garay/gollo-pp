<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imagenes', function (Blueprint $table) {
            $table->unsignedTinyInteger('humana_score')->nullable()->after('tipo');
            $table->decimal('angulo_menique', 5, 2)->nullable()->after('humana_score');
        });
    }

    public function down(): void
    {
        Schema::table('imagenes', function (Blueprint $table) {
            $table->dropColumn(['humana_score', 'angulo_menique']);
        });
    }
};
