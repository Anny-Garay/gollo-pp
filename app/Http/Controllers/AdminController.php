<?php

namespace App\Http\Controllers;

use App\Models\NivelTexto;
use App\Models\Participante;
use App\Models\Producto;
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

    public function productos()
    {
        $productos = Producto::orderBy('orden')->orderBy('id')->get();
        return view('admin.productos', compact('productos'));
    }

    public function productosStore(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'precio'       => 'required|numeric|min:0',
            'link_externo' => 'required|url|max:500',
            'foto'         => 'nullable|image|max:5120',
            'orden'        => 'nullable|integer|min:0',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('productos', 'public');
        }

        Producto::create([
            'nombre'       => $request->nombre,
            'precio'       => $request->precio,
            'link_externo' => $request->link_externo,
            'foto'         => $fotoPath,
            'orden'        => $request->input('orden', 0),
            'activo'       => $request->boolean('activo', true),
        ]);

        return redirect()->route('admin.productos')->with('success', 'Producto agregado.');
    }

    public function productosEdit(Producto $producto)
    {
        $productos = Producto::orderBy('orden')->orderBy('id')->get();
        return view('admin.productos', compact('productos', 'producto'));
    }

    public function productosUpdate(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'precio'       => 'required|numeric|min:0',
            'link_externo' => 'required|url|max:500',
            'foto'         => 'nullable|image|max:5120',
            'orden'        => 'nullable|integer|min:0',
        ]);

        $data = [
            'nombre'       => $request->nombre,
            'precio'       => $request->precio,
            'link_externo' => $request->link_externo,
            'orden'        => $request->input('orden', 0),
            'activo'       => $request->boolean('activo'),
        ];

        if ($request->hasFile('foto')) {
            if ($producto->foto) {
                \Storage::disk('public')->delete($producto->foto);
            }
            $data['foto'] = $request->file('foto')->store('productos', 'public');
        }

        $producto->update($data);

        return redirect()->route('admin.productos')->with('success', 'Producto actualizado.');
    }

    public function productosDestroy(Producto $producto)
    {
        if ($producto->foto) {
            \Storage::disk('public')->delete($producto->foto);
        }
        $producto->delete();
        return back()->with('success', 'Producto eliminado.');
    }
}
