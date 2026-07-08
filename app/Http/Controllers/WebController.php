<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebController extends Controller
{
    public function inicio()
    {
        return view('inicio');
    }

    public function serveImagen(string $path)
    {
        abort_unless(\Storage::disk('public')->exists($path), 404);
        return \Storage::disk('public')->response($path);
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

        // Guardar imagen en storage temporal para no re-enviarla en el segundo paso
        $rawB64  = preg_replace('/^data:image\/[a-z]+;base64,/', '', $request->imagen);
        $decoded = base64_decode($rawB64);
        abort_if($decoded === false, 422, 'Imagen inválida.');

        $imagenTemp = 'imagenes/temp/' . \Str::uuid() . '.jpg';
        \Storage::disk('public')->put($imagenTemp, $decoded);

        [$humanaScore, $anguloMenique, $soloMenique, $pinkyPoints] = $this->analizarImagenConIA($request->imagen);

        return response()->json([
            'humana_score'   => $humanaScore,
            'angulo_menique' => $anguloMenique,
            'solo_menique'   => $soloMenique,
            'pinky_points'   => $pinkyPoints,
            'imagen_temp'    => $imagenTemp,
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
        Participante::create([
            'nombre'  => $request->nombre,
            'cedula'  => $request->cedula,
            'celular' => $request->celular,
            'email'   => $request->email,
        ]);

        // Guardar imagen base64 en disco
        $imagenB64 = $request->imagen;
        $rawB64    = preg_replace('/^data:image\/[a-z]+;base64,/', '', $imagenB64);
        $decoded   = base64_decode($rawB64);
        abort_if($decoded === false, 422, 'Imagen inválida.');
        $filename = 'imagenes/' . uniqid('cam_', true) . '.jpg';
        \Storage::disk('public')->put($filename, $decoded);

        $humanaScore   = $request->input('humana_score');
        $anguloMenique = $request->input('angulo_menique');

        Participante::create([
            'nombre'         => $request->nombre,
            'cedula'         => $request->cedula,
            'celular'        => $request->celular,
            'email'          => $request->email,
            'foto'           => $filename,
            'humana_score'   => $humanaScore,
            'angulo_menique' => $anguloMenique,
        ]);

        return redirect()->route('resultado')->with([
            'humana_score'   => $humanaScore,
            'angulo_menique' => $anguloMenique,
        ]);
    }

    /**
     * Llama a OpenAI Vision (gpt-4o) para analizar la imagen y devuelve
     * [humana_score, angulo_menique, solo_menique, pinky_points].
     * Retorna [null, null, null, null] si la llamada falla.
     */
    private function analizarImagenConIA(string $imageDataUrl): array
    {
        $apiKey = config('services.openai.key');
        if (!$apiKey) {
            return [null, null, null, null];
        }

        $dedo_path = public_path('dedo-grafico.png');
        $dedoB64   = file_exists($dedo_path)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($dedo_path))
            : null;

        $prompt = <<<EOT
CONCEPTO: "Phone Pinky" (o iPhone Pinky) es una deformación del dedo meñique causada por sostener frecuentemente un smartphone apoyándolo sobre ese dedo. Se manifiesta como una curvatura o desviación lateral visible del meñique.

Analiza la imagen de la mano y responde ÚNICAMENTE con un objeto JSON válido, sin explicaciones ni markdown.

El JSON debe tener estos campos:
{
  "humana_score": <entero 0-100: qué tan probable es que sea una mano humana real>,
  "solo_menique": <true si solo el meñique está extendido y los demás doblados en puño>,
  "pinky_points": [
    {"x": 0-1000, "y": 0-1000},
    {"x": 0-1000, "y": 0-1000},
    {"x": 0-1000, "y": 0-1000},
    {"x": 0-1000, "y": 0-1000}
  ]
}

Los 4 puntos de pinky_points deben ser:
1. Base del meñique (articulación MCP, donde el dedo se une a la palma)
2. Articulación PIP (el nudo medio del meñique)
3. Articulación DIP (el nudo cerca de la uña)
4. Punta del meñique (extremo de la uña)

IMPORTANTE: Usá coordenadas normalizadas 0-1000 donde (0,0) es esquina superior izquierda y (1000,1000) esquina inferior derecha de la imagen. Identificá con precisión anatómica cada punto.
EOT;

        $content = [];
        if ($dedoB64) {
            $content[] = ['type' => 'image_url', 'image_url' => ['url' => $dedoB64, 'detail' => 'low']];
        }
        $content[] = ['type' => 'image_url', 'image_url' => ['url' => $imageDataUrl, 'detail' => 'high']];
        $content[] = ['type' => 'text', 'text' => $prompt];

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'      => 'gpt-4o',
                    'max_tokens' => 500,
                    'messages'   => [['role' => 'user', 'content' => $content]],
                ]);

            if (!$response->successful()) {
                \Log::warning('OpenAI API error', ['status' => $response->status(), 'body' => $response->body()]);
                return [null, null, null, null];
            }

            $raw  = $response->json('choices.0.message.content', '');
            $raw  = trim(preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($raw)));
            $data = json_decode($raw, true);

            if (!is_array($data)) {
                \Log::warning('OpenAI respuesta no parseable', ['content' => $raw]);
                return [null, null, null, null];
            }

            $humanaScore = isset($data['humana_score']) ? (int) $data['humana_score'] : null;
            $soloMenique = isset($data['solo_menique']) ? (bool) $data['solo_menique'] : null;
            $pinkyPoints = $data['pinky_points'] ?? null;

            $anguloMenique = null;
            if (is_array($pinkyPoints) && count($pinkyPoints) >= 3) {
                $anguloMenique = $this->calcularAnguloPinky($pinkyPoints);
            }

            return [$humanaScore, $anguloMenique, $soloMenique, $pinkyPoints];
        } catch (\Throwable $e) {
            \Log::error('OpenAI Vision exception', ['error' => $e->getMessage()]);
            return [null, null, null, null];
        }
    }

    /**
     * Calcula el ángulo de desviación del meñique a partir de coordenadas normalizadas.
     * Mide la desviación de las falanges PIP y DIP respecto a la línea recta base-punta.
     * Ángulo = desviación máxima entre el segmento base→PIP y base→tip, o PIP→DIP y base→tip.
     */
    private function calcularAnguloPinky(array $points): float
    {
        $p0 = $points[0]; // base MCP
        $p3 = $points[count($points) - 1]; // tip

        $vRefx = (float) $p3['x'] - (float) $p0['x'];
        $vRefy = (float) $p3['y'] - (float) $p0['y'];
        $magRef = sqrt($vRefx * $vRefx + $vRefy * $vRefy);

        if ($magRef < 0.001) return 0;

        $maxAngle = 0.0;

        // Medir desviación de cada punto intermedio respecto a la línea base→tip
        for ($i = 1; $i < count($points) - 1; $i++) {
            $vpx = (float) $points[$i]['x'] - (float) $p0['x'];
            $vpy = (float) $points[$i]['y'] - (float) $p0['y'];

            $dot   = $vpx * $vRefx + $vpy * $vRefy;
            $magP  = sqrt($vpx * $vpx + $vpy * $vpy);
            if ($magP < 0.001) continue;

            $cosA = max(-1, min(1, $dot / ($magP * $magRef)));
            $angle = rad2deg(acos($cosA));

            if ($angle > $maxAngle) $maxAngle = $angle;
        }

        return min(20.0, round($maxAngle, 1));
    }

    public function resultado()
    {
        return view('resultado', [
            'humana_score'   => session('humana_score'),
            'angulo_menique' => session('angulo_menique'),
            'pinky_points'   => session('pinky_points'),
            'imagen_temp'    => session('imagen_temp'),
        ]);
    }

    public function storeResultados(Request $request)
    {
        $request->validate([
            'imagen_temp'    => 'required|string',
            'humana_score'   => 'nullable|integer|min:0|max:100',
            'angulo_menique' => 'nullable|numeric',
            'pinky_points'   => 'nullable|string',
        ]);

        $imagenTemp = $request->imagen_temp;
        // Seguridad: solo rutas bajo imagenes/temp/
        abort_unless(
            str_starts_with($imagenTemp, 'imagenes/temp/') && !str_contains($imagenTemp, '..'),
            422, 'Ruta inválida.'
        );
        abort_unless(\Storage::disk('public')->exists($imagenTemp), 422, 'Imagen no encontrada.');

        $anguloMenique = $request->angulo_menique !== null ? min(20.0, (float) $request->angulo_menique) : null;
        $pinkyPoints   = $request->pinky_points ? json_decode($request->pinky_points, true) : null;

        session([
            'imagen_temp'    => $imagenTemp,
            'humana_score'   => $request->humana_score,
            'angulo_menique' => $anguloMenique,
            'pinky_points'   => $pinkyPoints,
        ]);

        return redirect()->route('resultados');
    }

    public function resultados()
    {
        return view('resultado', [
            'humana_score'   => session('humana_score'),
            'angulo_menique' => session('angulo_menique'),
            'pinky_points'   => session('pinky_points'),
            'imagen_temp'    => session('imagen_temp'),
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

        $imagenTemp = session('imagen_temp');
        $imagenRuta = null;

        if (
            is_string($imagenTemp)
            && str_starts_with($imagenTemp, 'imagenes/temp/')
            && !str_contains($imagenTemp, '..')
            && \Storage::disk('public')->exists($imagenTemp)
        ) {
            $imagenRuta = 'imagenes/' . uniqid('cam_', true) . '.jpg';
            \Storage::disk('public')->move($imagenTemp, $imagenRuta);
        }

        $humanaScore   = $request->input('humana_score')   ?? session('humana_score');
        $anguloMenique = $request->input('angulo_menique') ?? session('angulo_menique');

        Participante::create([
            'nombre'         => $request->nombre,
            'cedula'         => $request->cedula,
            'celular'        => $request->celular,
            'email'          => $request->email,
            'foto'           => $imagenRuta,
            'humana_score'   => $humanaScore,
            'angulo_menique' => $anguloMenique,
        ]);

        session()->forget(['imagen_temp', 'humana_score', 'angulo_menique']);

        $docNumber = 'PP-' . strtoupper(substr(md5($request->cedula . now()->timestamp), 0, 8));

        return redirect()->route('listo')->with([
            'nombre'         => $request->nombre,
            'cedula'         => $request->cedula,
            'angulo_menique' => $anguloMenique,
            'humana_score'   => $humanaScore,
            'doc_number'     => $docNumber,
        ]);
    }

    public function listo()
    {
        if (!session('doc_number')) {
            return redirect()->route('inicio');
        }
        $productos = Producto::where('activo', true)->orderBy('orden')->orderBy('id')->get();
        return view('listo', [
            'nombre'         => session('nombre'),
            'cedula'         => session('cedula'),
            'angulo_menique' => session('angulo_menique'),
            'humana_score'   => session('humana_score'),
            'doc_number'     => session('doc_number'),
            'productos'      => $productos,
        ]);
    }
}
