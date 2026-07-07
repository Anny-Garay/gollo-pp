<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>¡Listo! - Phone Pinky</title>
  <link rel="stylesheet" href="{{ asset('style.css') }}">
  <link href="{{ asset('/css/blanco.css?v=' . time()) }}" rel="stylesheet"/>
</head>
<body class="listo-body">
  @yield('content')
</body>
</html>