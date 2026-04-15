<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados - Phone Pinky</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        .resultado-outer {
            width: 100%;
            max-width: 430px;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            padding: 28px 20px 30px;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Logo */
        .resultado-logo {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 22px;
        }
        .resultado-logo img {
            height: 44px;
            width: auto;
        }

        /* Angle card */
        .angulo-card {
            background: rgba(255,255,255,0.12);
            border-radius: 18px;
            padding: 22px 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .angulo-label {
            color: rgba(255,255,255,0.65);
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .angulo-value {
            color: #fff;
            font-size: 64px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 6px;
        }
        .angulo-value span {
            font-size: 32px;
            font-weight: 700;
            vertical-align: super;
        }
        .angulo-desc {
            color: rgba(255,255,255,0.70);
            font-size: 13px;
            line-height: 1.4;
        }

        /* Foto thumb */
        .foto-thumb-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 18px;
        }
        .foto-thumb {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.3);
        }

        /* Form card */
        .form-card {
            background: #ffffff;
            border-radius: 22px;
            padding: 24px 20px 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .form-card-titulo {
            color: #165fc6;
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 4px;
        }
        .form-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .form-field label {
            color: #165fc6;
            font-size: 13px;
            font-weight: 700;
        }
        .form-field input[type="text"],
        .form-field input[type="email"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 2px solid #165fc6;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.15s;
        }
        .form-field input:focus {
            border-color: #0a40a0;
        }
        .errores-card {
            background: #fde8e8;
            border-radius: 10px;
            padding: 10px 14px;
            color: #900;
            font-size: 13px;
        }
        .errores-card ul { margin: 0; padding-left: 16px; }
        .btn-guardar {
            width: 100%;
            padding: 16px;
            background: #e31837;
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 1.05rem;
            font-weight: 800;
            cursor: pointer;
            margin-top: 4px;
            transition: transform 0.1s;
        }
        .btn-guardar:hover { background: #c5102c; }
        .btn-guardar:active { transform: scale(0.97); }

        /* Step dots */
        .step-dots {
            display: flex;
            gap: 7px;
            justify-content: center;
            padding: 18px 0 0;
        }
        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.28);
        }
        .step-dot--active {
            background: #e31837;
            width: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body class="resultado-body">
<div class="resultado-outer">

    <div class="resultado-logo">
        <img src="{{ asset('img/Recurso 12.png') }}" alt="Gollo">
    </div>

    {{-- Foto thumb + ángulo --}}
    @if ($imagen_ruta)
    <div class="foto-thumb-wrap">
        <img class="foto-thumb" src="{{ asset('storage/' . $imagen_ruta) }}" alt="Tu foto">
    </div>
    @endif

    <div class="angulo-card">
        <p class="angulo-label">Grado de inclinación del meñique</p>
        <div class="angulo-value">
            {{ $angulo_menique !== null ? number_format((float)$angulo_menique, 1) : '—' }}<span>°</span>
        </div>
        <p class="angulo-desc">Así ha marcado tu cel tu dedo 👆<br>¡Ahora registrate y obtené tu canje!</p>
    </div>

    {{-- Formulario --}}
    <div class="form-card">
        <h3 class="form-card-titulo">Completá tus datos</h3>

        @if ($errors->any())
            <div class="errores-card">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('guardar') }}">
            @csrf
            {{-- Campos internos (no visibles) --}}
            <input type="hidden" name="humana_score"   value="{{ $humana_score }}">

            <div class="form-field">
                <label for="nombre">Nombre y Apellido *</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
            </div>
            <div class="form-field">
                <label for="cedula">Número de Cédula *</label>
                <input type="text" id="cedula" name="cedula" value="{{ old('cedula') }}" required>
            </div>
            <div class="form-field">
                <label for="celular">Celular *</label>
                <input type="text" id="celular" name="celular" value="{{ old('celular') }}" required>
            </div>
            <div class="form-field">
                <label for="email">Correo Electrónico *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <button type="submit" class="btn-guardar">SIGUIENTE 🤙</button>
        </form>
    </div>

    <div class="step-dots">
        <span class="step-dot"></span>
        <span class="step-dot"></span>
        <span class="step-dot step-dot--active"></span>
        <span class="step-dot"></span>
        <span class="step-dot"></span>
    </div>

</div>
</body>
</html>
