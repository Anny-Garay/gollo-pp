<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use App\Models\Imagen;
use Illuminate\Http\Request;

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

    public function storeImagen(Request $request)
    {
        $request->validate([
            'imagen' => 'required',
            'tipo'   => 'required|in:camara,upload',
        ]);

        $participanteId = session('participante_id');
        abort_unless($participanteId, 403, 'Sesión no válida.');

        $tipo = $request->tipo;

        // Base64 (captura de cámara)
        if ($tipo === 'camara') {
            $data = $request->imagen;
            // quitar prefijo data:image/...;base64,
            $data = preg_replace('/^data:image\/[a-z]+;base64,/', '', $data);
            $decoded = base64_decode($data);
            abort_if($decoded === false, 422, 'Imagen inválida.');
            $filename = 'imagenes/' . uniqid('cam_', true) . '.jpg';
            \Storage::disk('public')->put($filename, $decoded);
            $ruta = $filename;
        } else {
            // Upload normal
            $request->validate(['imagen' => 'file|image|max:10240']);
            $ruta = $request->file('imagen')->store('imagenes', 'public');
        }

        Imagen::create([
            'participante_id' => $participanteId,
            'ruta'            => $ruta,
            'tipo'            => $tipo,
        ]);

        return redirect()->route('resultado');
    }

    public function resultado()
    {
        return view('resultado');
    }
}
