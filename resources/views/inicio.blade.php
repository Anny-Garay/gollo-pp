<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="inicio-body">
    <div class="contenedor">
        <div class="columna-imagen">
            <picture>
                  <!-- PC -->
                <source media="(min-width: 768px)" srcset="{{ asset('img/Recurso3.png') }}">
                <!-- Móvil (fallback obligatorio) -->
                <img src="{{ asset('img/Recurso2.png') }}" alt="" class="img-inicio">
            </picture>
        </div>
        <div class="columna-texto">
            <div class="texto-inicio">
                <h1>¡Tu dedo tiene<br>algo que decirte!</h1>
                <p>Vamos a analizar cómo el peso de tu<br>
                    cel ha marcado tu mano. Si tu dedo ya<br>
                    se puso en 'modo descanso', te damos<br>
                    un documento de canje oficial para<br>
                    que estrenés un celular más liviano y<br>
                    moderno en Gollo.¡Llegate a ver<br>
                    cuánto ganás!
                </p>
            </div>
            <a href="{{ route('carga') }}" class="boton-inicio">¡Darle viaje! 🤙</a>
            <p class="info-inicio">No guardamos fotos. Solo medimos el ángulo. 🔒</p>
        </div>
    </div>  

</body>
</html>
