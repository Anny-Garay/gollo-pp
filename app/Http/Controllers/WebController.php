<?php

namespace App\Http\Controllers;

use App\Models\Participante;
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

    public function resultado()
    {
        return view('resultado');
    }
}
