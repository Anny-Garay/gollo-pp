<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="login-body">
    <div class="fondo-blanco">
        <form class="login" method="POST" action="{{ route('login.store') }}" enctype="multipart/form-data">
            @csrf
            <h3 class="titulo-login">Completá tus datos</h3>

            @if ($errors->any())
                <div class="errores">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="nombre">
                <label for="nombre">Nombre y Apellido*</label>
                <br>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
            </div>
            <div class="cedula">
                <label for="cedula">Número de Cédula*</label>
                <br>
                <input type="text" id="cedula" name="cedula" value="{{ old('cedula') }}" required>
            </div>
            <div class="celular">
                <label for="celular">Celular*</label>
                <br>
                <input type="text" id="celular" name="celular" value="{{ old('celular') }}" required>
            </div>
            <div class="email">
                <label for="email">Correo Electrónico*</label>
                <br>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="foto">
                <label for="foto">Subí tu foto*</label>
                <br>
                <input type="file" id="foto" name="foto" accept="image/*" capture="environment" hidden>
                <label for="foto" class="btn-foto">Tomar foto</label>
            </div>
            <div class="login-boton">
                <button type="submit" class="submit-boton">SIGUIENTE 🤙</button>
            </div>
        </form>
    </div>
</body>
</html>
