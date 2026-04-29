<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — @yield('title', 'Panel')</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; background: #f0f2f5; color: #222; }
        header {
            background: #0b3a6d;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 28px;
        }
        header h1 { font-size: 1.2rem; letter-spacing: 1px; }
        header a { color: #aed6f1; text-decoration: none; font-size: 0.9rem; }
        header a:hover { color: #fff; }
        .container { max-width: 1100px; margin: 32px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,.08); padding: 28px; }
        .alert-success {
            background: #d4edda; color: #155724; border: 1px solid #c3e6cb;
            border-radius: 6px; padding: 10px 16px; margin-bottom: 16px;
        }
        .alert-error {
            background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;
            border-radius: 6px; padding: 10px 16px; margin-bottom: 16px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <header>
        <h1>⚙️ Panel de Administración</h1>
        <div style="display:flex; gap:20px; align-items:center;">
            <a href="{{ route('admin.participantes') }}">Participantes</a>
            <a href="{{ route('admin.niveles') }}">Textos por Nivel</a>
            <a href="{{ route('admin.productos') }}">Productos</a>
            <a href="{{ route('admin.register') }}">+ Nuevo usuario</a>
            <a href="{{ route('admin.logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
                Cerrar sesión ({{ Auth::user()->name }})
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">@csrf</form>
        </div>
    </header>

    <div class="container">
        @yield('content')
    </div>
</body>
</html>
