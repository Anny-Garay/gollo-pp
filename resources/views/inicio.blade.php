<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Pinky - Gollo</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="inicio-body">
    <div class="inicio-wrapper">

        <div class="inicio-logo">
            <img src="{{ asset('img/Recurso 12.png') }}" alt="Gollo" class="logo-img">
        </div>

        <div class="inicio-content">
            <div class="thumb-emoji">👍</div>
            <div class="pinky-badge">PHONE PINKY™</div>
            <h1 class="inicio-titulo">¡Tu dedo tiene<br>algo que decirte!</h1>
            <p class="inicio-desc">
                Vamos a anlizar cómo el peso de tu cel ha marcado tu mano. Si tu dedo ya se puso en 'modo descanso', te damos un documento de canje oficial para que estrenés un celular más liviano y moderno en Gollo. ¡Llegate a ver cuánto ganás!
            </p>
            <div class="inicio-features">
                <div class="feature-card">
                    <span class="feature-icon">⚡</span>
                    <strong>3 segundos</strong>
                    <small>Diagnóstico</small>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">🤖</span>
                    <strong>IA real</strong>
                    <small>Tecnología</small>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">🎁</span>
                    <strong>Totalmente</strong>
                    <small>Gratis</small>
                </div>
            </div>
        </div>

        <div class="inicio-cta">
            <a href="{{ route('carga') }}" class="boton-inicio">¡Darle viaje! 👍</a>
            <p class="info-inicio">No guardamos fotos. Solo medimos el ángulo. 🔒</p>
        </div>

        <div class="progress-dots">
            <span class="dot dot--active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>

    </div>
</body>
</html>
