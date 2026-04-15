<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use App\Models\Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebController extends Controller
{
    public function inicio()
    {
        return view('inicio');
    }

    public function login()
    {
        return view('login');
    }

    public function storeParticipante(Request $request)
    {
        $request->validate([
            'nombre'  => 'required|string|max:255',
            'cedula'  => 'required|string|max:50',
            'celular' => 'required|string|max:30',
            'email'   => 'required|email|max:255',
            'foto'    => 'nullable|image|max:5120',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fotos', 'public');
        }

        $data = [
            'nombre'  => $request->nombre,
            'celular' => $request->celular,
            'email'   => $request->email,
        ];

        if ($fotoPath) {
            $data['foto'] = $fotoPath;
        }

        $participante = Participante::updateOrCreate(
            ['cedula' => $request->cedula],
            $data
        );

        session(['participante_id' => $participante->id]);

        return redirect()->route('carga');
    }

    public function carga()
    {
        return view('carga');
    }

    public function analizarImagen(Request $request)
    {
        $request->validate(['imagen' => 'required|string']);
        [$humanaScore, $anguloMenique] = $this->analizarImagenConIA($request->imagen);
        return response()->json([
            'humana_score'   => $humanaScore,
            'angulo_menique' => $anguloMenique,
        ]);
    }

    public function storeImagen(Request $request)
    {
        $request->validate([
            'nombre'         => 'required|string|max:255',
            'cedula'         => 'required|string|max:50',
            'celular'        => 'required|string|max:30',
            'email'          => 'required|email|max:255',
            'imagen'         => 'required|string',
            'tipo'           => 'required|in:camara',
            'humana_score'   => 'nullable|integer|min:0|max:100',
            'angulo_menique' => 'nullable|numeric',
        ]);

        // Guardar participante
        $participante = Participante::updateOrCreate(
            ['cedula' => $request->cedula],
            [
                'nombre'  => $request->nombre,
                'celular' => $request->celular,
                'email'   => $request->email,
            ]
        );

        // Guardar imagen base64 en disco
        $imagenB64 = $request->imagen;
        $rawB64    = preg_replace('/^data:image\/[a-z]+;base64,/', '', $imagenB64);
        $decoded   = base64_decode($rawB64);
        abort_if($decoded === false, 422, 'Imagen inválida.');
        $filename = 'imagenes/' . uniqid('cam_', true) . '.jpg';
        \Storage::disk('public')->put($filename, $decoded);

        $humanaScore   = $request->input('humana_score');
        $anguloMenique = $request->input('angulo_menique');

        Imagen::create([
            'participante_id' => $participante->id,
            'ruta'            => $filename,
            'tipo'            => 'camara',
            'humana_score'    => $humanaScore,
            'angulo_menique'  => $anguloMenique,
        ]);

        return redirect()->route('resultado')->with([
            'humana_score'   => $humanaScore,
            'angulo_menique' => $anguloMenique,
        ]);
    }

    /**
     * Llama a OpenAI Vision con la imagen base64 y devuelve [humana_score, angulo_menique].
     * Retorna [null, null] si la llamada falla.
     */
    private function analizarImagenConIA(string $imageDataUrl): array
    {
        $apiKey = config('services.openai.key');
        if (!$apiKey) {
            return [null, null];
        }

        $prompt = <<<EOT
Analiza esta imagen y responde ÚNICAMENTE con un objeto JSON válido, sin explicaciones ni markdown.
El JSON debe tener exactamente estos dos campos:
{
  "humana_score": <entero del 0 al 100 indicando qué tan probable es que sea una mano humana real>,
  "angulo_menique": <número decimal con el ángulo de inclinación del dedo meñique en grados respecto a la vertical, o 0 si no se puede medir. Trata de medirlo aunque el meñique esté parcialmente oculto, y si no se ve para nada, pon 0.>
}
EOT;

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'      => 'gpt-4o',
                    'max_tokens' => 100,
                    'messages'   => [
                        [
                            'role'    => 'user',
                            'content' => [
                                [
                                    'type'      => 'image_url',
                                    'image_url' => ['url' => $imageDataUrl, 'detail' => 'low'],
                                ],
                                [
                                    'type' => 'text',
                                    'text' => $prompt,
                                ],
                            ],
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                \Log::warning('OpenAI API error', ['status' => $response->status(), 'body' => $response->body()]);
                return [null, null];
            }

            $content = $response->json('choices.0.message.content', '');
            // Limpiar posible markdown ```json ... ```
            $content = trim(preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($content)));
            $data    = json_decode($content, true);

            if (!is_array($data)) {
                \Log::warning('OpenAI respuesta no parseable', ['content' => $content]);
                return [null, null];
            }

            $humanaScore   = isset($data['humana_score'])   ? (int)   $data['humana_score']   : null;
            $anguloMenique = isset($data['angulo_menique']) ? (float) $data['angulo_menique'] : null;

            return [$humanaScore, $anguloMenique];
        } catch (\Throwable $e) {
            \Log::error('OpenAI Vision exception', ['error' => $e->getMessage()]);
            return [null, null];
        }
    }

    public function resultado()
    {
        return view('resultado', [
            'humana_score'   => session('humana_score'),
            'angulo_menique' => session('angulo_menique'),
            'imagen_ruta'    => session('imagen_ruta'),
        ]);
    }

    public function storeResultados(Request $request)
    {
        $request->validate([
            'imagen'         => 'required|string',
            'humana_score'   => 'nullable|integer|min:0|max:100',
            'angulo_menique' => 'nullable|numeric',
        ]);

        $imagenB64 = $request->imagen;
        $rawB64    = preg_replace('/^data:image\/[a-z]+;base64,/', '', $imagenB64);
        $decoded   = base64_decode($rawB64);
        abort_if($decoded === false, 422, 'Imagen inválida.');

        $filename = 'imagenes/' . uniqid('cam_', true) . '.jpg';
        \Storage::disk('public')->put($filename, $decoded);

        session([
            'imagen_ruta'    => $filename,
            'humana_score'   => $request->humana_score,
            'angulo_menique' => $request->angulo_menique,
        ]);

        return redirect()->route('resultados');
    }

    public function resultados()
    {
        return view('resultado', [
            'humana_score'   => session('humana_score'),
            'angulo_menique' => session('angulo_menique'),
            'imagen_ruta'    => session('imagen_ruta'),
        ]);
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre'  => 'required|string|max:255',
            'cedula'  => 'required|string|max:50',
            'celular' => 'required|string|max:30',
            'email'   => 'required|email|max:255',
        ]);

        $participante = Participante::updateOrCreate(
            ['cedula' => $request->cedula],
            [
                'nombre'  => $request->nombre,
                'celular' => $request->celular,
                'email'   => $request->email,
            ]
        );

        $imagenRuta    = session('imagen_ruta');
        $humanaScore   = $request->input('humana_score')   ?? session('humana_score');
        $anguloMenique = $request->input('angulo_menique') ?? session('angulo_menique');

        if ($imagenRuta) {
            Imagen::create([
                'participante_id' => $participante->id,
                'ruta'            => $imagenRuta,
                'tipo'            => 'camara',
                'humana_score'    => $humanaScore,
                'angulo_menique'  => $anguloMenique,
            ]);
            session()->forget(['imagen_ruta', 'humana_score', 'angulo_menique']);
        }

        return redirect()->route('inicio')->with('registro_ok', true);
    }
}
