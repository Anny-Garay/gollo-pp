<?php

namespace Tests\Feature;

use App\Models\Cupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

        $this->post(route('guardar'), [
            'imagen_temp'    => $analisis->json('imagen_temp'),
            'nombre'         => 'Cliente de Prueba',
            'cedula'         => '123456789',
            'celular'        => '88888888',
            'email'          => 'cliente@example.com',
            'angulo_menique' => 2.0,
        ])->assertRedirect();

        $this->assertDatabaseHas('participantes', [
            'cedula'       => '123456789',
            'cupon_codigo' => 'Pinky5',
        ]);
    }

    public function test_el_paso_intermedio_acepta_post_y_muestra_el_cupon(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('imagenes/temp/prueba.jpg', 'contenido');

        $puntos = [
            ['x' => 500, 'y' => 500],
            ['x' => 510, 'y' => 450],
            ['x' => 520, 'y' => 400],
            ['x' => 530, 'y' => 350],
        ];

        $response = $this->post(route('resultados.store'), [
            'imagen_temp'    => 'imagenes/temp/prueba.jpg',
            'humana_score'   => 90,
            'angulo_menique' => 2.0,
            'pinky_points'   => json_encode($puntos),
        ]);

        $response->assertRedirect();

        $this->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee('Pinky5');
    }

    public function test_debug_openai_muestra_la_respuesta_cruda_solo_al_admin(): void
    {
        config(['services.openai.key' => 'sk-test']);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'humana_score' => 95,
                            'solo_menique' => true,
                            'pinky_points' => [
                                ['x' => 500, 'y' => 500],
                                ['x' => 510, 'y' => 450],
                                ['x' => 520, 'y' => 400],
                                ['x' => 530, 'y' => 350],
                            ],
                        ]),
                    ],
                ]],
                'usage' => ['total_tokens' => 120],
            ], 200),
        ]);

        $user = User::create([
            'name'     => 'Admin',
            'email'    => 'admin-debug@example.com',
            'password' => Hash::make('password'),
        ]);

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=');

        $this->actingAs($user)
            ->get(route('admin.debug.openai'))
            ->assertOk()
            ->assertSee('Debug OpenAI Vision');

        $this->actingAs($user)
            ->post(route('admin.debug.openai.analyze'), [
                'imagen' => 'data:image/png;base64,' . base64_encode($png),
            ])
            ->assertOk()
            ->assertSee('pinky_points')
            ->assertSee('95');
    }
}
