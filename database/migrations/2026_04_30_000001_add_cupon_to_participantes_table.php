<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->string('cupon_codigo')->nullable()->after('imagen_ruta');
            $table->decimal('cupon_monto', 10, 2)->nullable()->after('cupon_codigo');
            $table->string('cupon_monto_texto')->nullable()->after('cupon_monto');
        });
    }

    public function down(): void
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->dropColumn(['cupon_codigo', 'cupon_monto', 'cupon_monto_texto']);
        });
    }
};
