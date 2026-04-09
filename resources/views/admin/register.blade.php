<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Login</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #0b3a6d;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 40px 36px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 8px 32px rgba(0,0,0,.25);
        }
        h2 { text-align: center; color: #0b3a6d; margin-bottom: 28px; font-size: 1.3rem; }
        label { display: block; font-size: 0.88rem; color: #444; margin-bottom: 4px; }
        input[type=text], input[type=email], input[type=password] {
            width: 100%; padding: 10px 12px; border: 1px solid #ccc;
            border-radius: 6px; font-size: 0.95rem; margin-bottom: 16px;
        }
        input[type=text]:focus, input[type=email]:focus, input[type=password]:focus {
            outline: none; border-color: #2a8fd8;
        }
        .remember { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; margin-bottom: 20px; }
        button[type=submit] {
            width: 100%; padding: 11px; background: #0b3a6d; color: #fff;
            border: none; border-radius: 6px; font-size: 1rem; cursor: pointer;
            font-weight: bold; letter-spacing: 0.5px;
        }
        button[type=submit]:hover { background: #2a8fd8; }
        .error-msg { background: #f8d7da; color: #721c24; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 0.88rem; }
    </style>
</head>
<body>
    <div class="card">
        <h2>⚙️ Administración</h2>
    @if ($errors->any())
        <div class="error-list">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="login" method="POST" action="{{ route('admin.register.post') }}">
        @csrf
        <div class="nombre">
            <label for="name">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
        </div>
        <div class="nombre">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="nombre">
            <label for="password">Contraseña <small style="color:#999">(mínimo 8 caracteres)</small></label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="nombre">
            <label for="password_confirmation">Repetir contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        <div class="btn-row">
            <button type="submit">Crear usuario</button>
            <a href="{{ route('admin.login') }}" class="btn-back">← Volver al login</a>
        </div>
    </form>
</div>
    </div>
</body>
</html>