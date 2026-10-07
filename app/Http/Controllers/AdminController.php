<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use App\Models\NivelTexto;
use App\Models\Participante;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

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
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cupon_codigo', 'like', "%{$search}%");
            });
        }

        if ($cuponFilter = $request->input('cupon')) {
            $query->where('cupon_codigo', $cuponFilter);
        }

        $participantes = $query->latest()->paginate(20)->withQueryString();
        $cupones = Cupon::orderBy('min_desviacion')->get();
        $totalEntregados = Participante::whereNotNull('cupon_codigo')->count();
        $totalStock = Cupon::sum('stock_total');
        $totalDisponible = Cupon::sum('stock_disponible');

        return view('admin.participantes', compact('participantes', 'cupones', 'totalEntregados', 'totalStock', 'totalDisponible'));
    }

    public function exportParticipantes(Request $request): StreamedResponse
    {
        $query = Participante::query();

        if ($search = $request->input('buscar')) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cupon_codigo', 'like', "%{$search}%");
            });
        }

        if ($cuponFilter = $request->input('cupon')) {
            $query->where('cupon_codigo', $cuponFilter);
        }

        $participantes = $query->latest('id')->get();
        $filename = 'participantes_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($participantes) {
            $handle = fopen('php://output', 'w');
            
            // BOM UTF-8 para apertura correcta con acentos en Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Encabezados CSV
            fputcsv($handle, [
                'ID',
                'Nombre y Apellido',
                'Cédula / DIMEX',
                'Celular',
                'Email',
                'Desviación (°)',
                'Cupón',
                'Monto Descuento',
                'Foto URL',
                'Fecha de Registro',
            ], ';');

            foreach ($participantes as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->nombre,
                    $p->cedula,
                    $p->celular,
                    $p->email,
                    $p->angulo_menique !== null ? $p->angulo_menique . '°' : '—',
                    $p->cupon_codigo ?: 'Sin cupón',
                    $p->cupon_monto_texto ?: ($p->cupon_monto ? '₡' . number_format($p->cupon_monto, 0, ',', '.') : '—'),
                    $p->foto ? url('img/' . $p->foto) : 'Sin foto',
                    $p->created_at ? $p->created_at->format('d/m/Y H:i:s') : '—',
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportFotosZip(Request $request)
    {
        $participantes = Participante::whereNotNull('foto')
            ->where('foto', '!=', '')
            ->orderBy('id', 'desc')
            ->get();

        if ($participantes->isEmpty()) {
            return back()->with('error', 'No hay participantes con fotos para descargar.');
        }

        $zipFileName = 'fotos_participantes_' . date('Y-m-d_His') . '.zip';
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $zipFilePath = $tempDir . '/' . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'No se pudo crear el archivo ZIP.');
        }

        $usedNames = [];
        $addedCount = 0;

        foreach ($participantes as $p) {
            if (!$p->foto || !Storage::disk('public')->exists($p->foto)) {
                continue;
            }

            $extension = pathinfo($p->foto, PATHINFO_EXTENSION) ?: 'jpg';
            // Sanitizar nombre del participante
            $safeNombre = trim(preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $p->nombre));
            $safeNombre = preg_replace('/\s+/', ' ', $safeNombre) ?: 'participante';

            // Nombre de la foto: "Nombre del participante - ID"
            $fileName = "{$safeNombre} - {$p->id}.{$extension}";

            // Evitar colisiones exactas en zip
            if (isset($usedNames[$fileName])) {
                $usedNames[$fileName]++;
                $fileName = "{$safeNombre} - {$p->id} ({$usedNames[$fileName]}).{$extension}";
            } else {
                $usedNames[$fileName] = 1;
            }

            $fileContents = Storage::disk('public')->get($p->foto);
            $zip->addFromString($fileName, $fileContents);
            $addedCount++;
        }

        $zip->close();

        if ($addedCount === 0) {
            @unlink($zipFilePath);
            return back()->with('error', 'No se encontraron archivos de fotos en el servidor.');
        }

        return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function cupones()
    {
        $cupones = Cupon::orderBy('min_desviacion')->get();
        $totalStock = Cupon::sum('stock_total');
        $totalDisponible = Cupon::sum('stock_disponible');
        $totalEntregados = Participante::whereNotNull('cupon_codigo')->count();

        return view('admin.cupones', compact('cupones', 'totalStock', 'totalDisponible', 'totalEntregados'));
    }

    public function cuponesUpdate(Request $request, Cupon $cupon)
    {
        $request->validate([
            'stock_total'      => 'required|integer|min:0',
            'stock_disponible' => 'required|integer|min:0',
            'activo'           => 'boolean',
        ]);

        $cupon->update([
            'stock_total'      => (int) $request->stock_total,
            'stock_disponible' => (int) $request->stock_disponible,
            'activo'           => $request->boolean('activo'),
        ]);

        return back()->with('success', "Cupón {$cupon->codigo} actualizado correctamente.");
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
