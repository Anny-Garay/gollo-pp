<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="resultado-body">
    <div class="container-img">
        <img src="{{ asset('img/Recurso6.png') }}" alt="" class="img-resultado">
    </div>
    <div class="texto-resultado">
        <h1>¡Felicidades!</h1>
        <p>Ganaste un 15%<br>de descuento
        </p>
        @if (!is_null($humana_score) || !is_null($angulo_menique))
        <div class="analisis-ia" style="margin-top:18px; font-size:0.95rem; opacity:0.85;">
            @if (!is_null($humana_score))
                <p>🤚 Mano humana: <strong>{{ $humana_score }}/100</strong></p>
            @endif
            @if (!is_null($angulo_menique))
                <p>🤙 Ángulo del meñique: <strong>{{ $angulo_menique }}°</strong></p>
            @endif
        </div>
        @endif
    </div>
</body>
</html>
