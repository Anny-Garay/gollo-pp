<?php

namespace Tests\Feature;

use App\Models\Cupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CuponAsignacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_dos_grados_corresponde_a_pinky_5(): void
    {
        $cupon = Cupon::obtenerPorDesviacion(2.0);

        $this->assertNotNull($cupon);
        $this->assertSame('Pinky5', $cupon->codigo);
    }

    public function test_entrega_el_cupon_aunque_el_stock_este_agotado(): void
    {
        Cupon::where('codigo', 'Pinky5')->update(['stock_disponible' => 0]);

        $cupon = Cupon::asignarPorDesviacion(2.0);

        $this->assertNotNull($cupon);
        $this->assertSame('Pinky5', $cupon->codigo);
        $this->assertEquals(0, Cupon::where('codigo', 'Pinky5')->value('stock_disponible'));
    }

    public function test_descuenta_el_stock_cuando_hay_disponible(): void
    {
        $inicial = (int) Cupon::where('codigo', 'Pinky5')->value('stock_disponible');

        $cupon = Cupon::asignarPorDesviacion(2.0);

        $this->assertSame('Pinky5', $cupon->codigo);
        $this->assertEquals(
            $inicial - 1,
            (int) Cupon::where('codigo', 'Pinky5')->value('stock_disponible')
        );
    }

    public function test_sin_tokens_el_flujo_entrega_pinky_5(): void
    {
        Storage::fake('public');
        config(['services.openai.key' => 'sk-test']);
        Http::fake([
            'api.openai.com/*' => Http::response('', 429),
        ]);

        Cupon::where('codigo', 'Pinky5')->update(['stock_disponible' => 0]);

        $analisis = $this->postJson(route('analizar'), [
            'imagen' => 'data:image/jpeg;base64,' . base64_encode('imagen-de-prueba'),
        ]);

        $analisis->assertOk()->assertJson(['angulo_menique' => 2.0]);

        $this->get(route('guardar', [
            'imagen_temp'    => $analisis->json('imagen_temp'),
            'nombre'         => 'Cliente de Prueba',
            'cedula'         => '123456789',
            'celular'        => '88888888',
            'email'          => 'cliente@example.com',
            'angulo_menique' => 2.0,
        ]))->assertRedirect();

        $this->assertDatabaseHas('participantes', [
            'cedula'       => '123456789',
            'cupon_codigo' => 'Pinky5',
        ]);
    }
}
