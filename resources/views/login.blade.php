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
        <form class="login">
            @csrf
            <h3 class="titulo-login">Completá tus datos</h3>
            <div class="nombre">
                <label for="nombre">Nombre y Apellido*</label>
                <br>
                <input type="text" id="nombre" required>
            </div>
            <div class="cedula">
                <label for="cedula">Número de Cédula*</label>
                <br>
                <input type="text" id="cedula" required>
            </div>
            <div class="celular">
                <label for="celular">Celular*</label>
                <br>
                <input type="text" id="celular" required>
            </div>
            <div class="email">
                <label for="email">Correo Electrónico*</label>
                <br>
                <input type="email" id="email" required>
            </div>
            <div class="foto">
                <label for="foto">Subí tu foto*</label>
                <br>
                <input type="file" id="foto" name="foto" accept="image/*" capture="environment" hidden required>
                <label for="foto" class="btn-foto">Tomar foto</label>
            </div>
            <div class="login-boton">
                <input type="button" class="submit-boton" value="SIGUIENTE 🤙" onclick="window.location.href='{{ route('carga') }}'">
            </div>
        </form>
    </div>
</body>
</html>
