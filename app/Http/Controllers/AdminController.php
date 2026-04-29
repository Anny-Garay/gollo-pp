<?php

namespace App\Http\Controllers;

use App\Models\NivelTexto;
use App\Models\Participante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.participantes');
        }
        return view('admin.login');
    }

    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.participantes');
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function participantes(Request $request)
    {
        $query = Participante::query();

        if ($search = $request->input('buscar')) {
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $participantes = $query->latest()->paginate(20)->withQueryString();

        return view('admin.participantes', compact('participantes'));
    }

    public function registerForm()
    {
        return view('admin.register');
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => 'required|min:8|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.login')->with('success', 'Usuario creado. Podés iniciar sesión.');
    }

    public function destroyParticipante(Participante $participante)
    {
        if ($participante->foto) {
            \Storage::disk('public')->delete($participante->foto);
        }
        $participante->delete();
        return back()->with('success', 'Participante eliminado.');
    }

    public function niveles()
    {
        $niveles = NivelTexto::orderBy('nivel')->get()->keyBy('nivel');
        return view('admin.niveles', compact('niveles'));
    }

    public function nivelesUpdate(Request $request, int $nivel)
    {
        abort_unless($nivel >= 1 && $nivel <= 5, 404);

        $request->validate([
            'titulo'    => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);

        NivelTexto::updateOrCreate(
            ['nivel' => $nivel],
            [
                'titulo'    => $request->titulo,
                'contenido' => $request->contenido,
            ]
        );

        return back()->with('success', "Nivel {$nivel} guardado correctamente.");
    }
}
