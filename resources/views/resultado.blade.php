<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Phone Pinky</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        .resultado-body {
            background: #f0f2f5 !important;
            background-image: none !important;
            align-items: unset !important;
            min-height: 100dvh;
        }
        .res-page {
            max-width: 430px;
            width: 100%;
            margin: 0 auto;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* ── Photo strip ── */
        .res-photo-wrap {
            position: relative;
            width: 100%;
            height: 38vh;
            min-height: 190px;
            max-height: 280px;
            overflow: hidden;
            background: #1a1a2e;
        }
        .res-hand-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .res-photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
        }
        .res-back-btn {
            position: absolute;
            top: 14px;
            left: 14px;
            width: 36px;
            height: 36px;
            background: rgba(0,0,0,0.45);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            z-index: 5;
        }
        .res-angle-badge {
            position: absolute;
            bottom: 14px;
            right: 14px;
            background: rgba(227,24,55,0.88);
            color: #fff;
            font-size: 18px;
            font-weight: 900;
            padding: 4px 14px;
            border-radius: 20px;
        }

        /* ── Cards area ── */
        .res-content {
            padding: 13px 13px 32px;
            display: flex;
            flex-direction: column;
            gap: 11px;
        }
        .res-card {
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
        }

        /* ── Diagnostic card ── */
        .diag-header {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #999;
            text-align: center;
            text-transform: uppercase;
            padding: 16px 16px 0;
            margin: 0;
        }
        .gauge-svg-wrap {
            padding: 8px 16px 0;
        }
        .gauge-angle-val {
            text-align: center;
            font-size: 46px;
            font-weight: 900;
            color: #e31837;
            line-height: 1;
            margin: 2px 0 4px;
        }
        .gauge-angle-val sup { font-size: 24px; vertical-align: super; }
        .nivel-wrap {
            display: flex;
            justify-content: center;
            padding: 0 0 16px;
        }
        .nivel-badge {
            padding: 9px 32px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: 1px;
            border-width: 1.5px;
            border-style: solid;
        }

        /* ── Description card ── */
        .desc-card {
            background: #fff;
            border-radius: 18px;
            padding: 16px 16px 16px 18px;
            border-left: 4px solid #e31837;
        }
        .desc-card-title {
            font-size: 14px;
            font-weight: 800;
            color: #1a1a2e;
            margin: 0 0 6px;
        }
        .desc-card-body {
            font-size: 13.5px;
            color: #555;
            line-height: 1.6;
            margin: 0;
        }

        /* ── Form card ── */
        .form-card {
            background: #fff;
            border-radius: 18px;
            padding: 20px 16px 16px;
        }
        .form-card-head {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 18px;
        }
        .form-card-head-icon { font-size: 22px; flex-shrink: 0; margin-top: 1px; }
        .form-card-head-text strong {
            display: block;
            font-size: 16px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 2px;
        }
        .form-card-head-text small { font-size: 12px; color: #999; }
        .form-errores {
            background: #fde8e8;
            border-radius: 10px;
            padding: 10px 14px;
            color: #c00;
            font-size: 13px;
            margin-bottom: 12px;
        }
        .form-errores ul { margin: 0; padding-left: 16px; }
        .form-field { margin-bottom: 12px; }
        .form-field-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
        }
        .badge-formato {
            font-size: 10px;
            font-weight: 700;
            color: #e31837;
        }
        .form-field input[type="text"],
        .form-field input[type="email"] {
            width: 100%;
            padding: 13px 14px;
            border-radius: 10px;
            border: 1.5px solid #e5e5e5;
            background: #fafafa;
            font-size: 15px;
            color: #333;
            box-sizing: border-box;
            outline: none;
            font-family: Arial, Helvetica, sans-serif;
            transition: border-color 0.15s;
        }
        .form-field input::placeholder { color: #bbb; }
        .form-field input:focus { border-color: #aaa; background: #fff; }
        .btn-guardar {
            width: 100%;
            padding: 16px;
            background: #e31837;
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 6px;
            font-family: Arial, Helvetica, sans-serif;
            transition: transform 0.1s;
        }
        .btn-guardar:active { transform: scale(0.97); }

        /* ── Step dots ── */
        .step-dots {
            display: flex;
            gap: 7px;
            justify-content: center;
            padding: 12px 0 0;
        }
        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(0,0,0,0.14);
        }
        .step-dot--active {
            background: #e31837;
            width: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body class="resultado-body">
@php
    $angulo    = (float)($angulo_menique ?? 0);
    $maxGauge  = 40.0;
    $ratio     = min(max($angulo / $maxGauge, 0.0), 1.0);
    $arcDeg    = 180.0 * (1.0 - $ratio);
    $arcRad    = deg2rad($arcDeg);

    // SVG gauge center (110,115), radius 90
    $cx = 110; $cy = 115;
    $rN   = 66; // needle length
    $rDot = 92; // dot on arc
    $nx   = round($cx + $rN  * cos($arcRad), 2);
    $ny   = round($cy - $rN  * sin($arcRad), 2);
    $dotX = round($cx + $rDot * cos($arcRad), 2);
    $dotY = round($cy - $rDot * sin($arcRad), 2);

    if ($angulo < 10)     { $nivel = 'NIVEL LEVE';     $nBg = '#e8f5e9'; $nTxt = '#2e7d32'; $nBor = '#2e7d32'; }
    elseif ($angulo < 20) { $nivel = 'NIVEL MODERADO'; $nBg = '#fff3e0'; $nTxt = '#e65100'; $nBor = '#e65100'; }
    else                  { $nivel = 'NIVEL ALTO';     $nBg = '#fff0f0'; $nTxt = '#e31837'; $nBor = '#e31837'; }

    $anguloDisplay = $angulo_menique !== null ? number_format($angulo, 1) : '—';
@endphp

<div class="res-page">

    {{-- ── TOP PHOTO ── --}}
    <div class="res-photo-wrap">
        @if($imagen_ruta)
            <img class="res-hand-photo" src="{{ asset('storage/' . $imagen_ruta) }}" alt="Tu mano">
        @else
            <div class="res-photo-placeholder">✋</div>
        @endif
        <a href="{{ route('carga') }}" class="res-back-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M15 19l-7-7 7-7" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        @if($angulo_menique !== null)
            <div class="res-angle-badge">{{ $anguloDisplay }}°</div>
        @endif
    </div>

    <div class="res-content">

        {{-- ── DIAGNOSTIC CARD ── --}}
        <div class="res-card">
            <p class="diag-header">🏥 Resultado del diagnóstico</p>

            <div class="gauge-svg-wrap">
                {{--
                    SVG Gauge — center (110,115), radius 90
                    5 segments each 36° of 180° sweep (CW in SVG = upper semicircle)
                    Angles (standard math, CCW from right):
                      180° → (20, 115)
                      144° → (37.19, 62.08)
                      108° → (82.19, 29.41)
                       72° → (137.81, 29.41)
                       36° → (182.81, 62.08)
                        0° → (200, 115)
                    Arc direction: sweep=1 (CW in SVG) traces the upper semicircle
                --}}
                <svg viewBox="0 0 220 120" width="100%" style="display:block;max-width:320px;margin:0 auto">
                    {{-- Background track --}}
                    <path d="M 20 115 A 90 90 0 0 1 200 115"
                          stroke="#eeeeee" stroke-width="22" fill="none" stroke-linecap="round"/>
                    {{-- Segment 1: green --}}
                    <path d="M 20 115 A 90 90 0 0 1 37.19 62.08"
                          stroke="#27ae60" stroke-width="18" fill="none" stroke-linecap="butt"/>
                    {{-- Segment 2: lime --}}
                    <path d="M 37.19 62.08 A 90 90 0 0 1 82.19 29.41"
                          stroke="#8bc34a" stroke-width="18" fill="none" stroke-linecap="butt"/>
                    {{-- Segment 3: yellow --}}
                    <path d="M 82.19 29.41 A 90 90 0 0 1 137.81 29.41"
                          stroke="#fdd835" stroke-width="18" fill="none" stroke-linecap="butt"/>
                    {{-- Segment 4: orange --}}
                    <path d="M 137.81 29.41 A 90 90 0 0 1 182.81 62.08"
                          stroke="#f57c00" stroke-width="18" fill="none" stroke-linecap="butt"/>
                    {{-- Segment 5: red --}}
                    <path d="M 182.81 62.08 A 90 90 0 0 1 200 115"
                          stroke="#e53935" stroke-width="18" fill="none" stroke-linecap="butt"/>

                    {{-- Needle --}}
                    <line x1="110" y1="115" x2="{{ $nx }}" y2="{{ $ny }}"
                          stroke="#e31837" stroke-width="3" stroke-linecap="round"/>
                    {{-- Dot on arc --}}
                    <circle cx="{{ $dotX }}" cy="{{ $dotY }}" r="8" fill="#e31837"/>
                    <circle cx="{{ $dotX }}" cy="{{ $dotY }}" r="4" fill="#fff"/>
                    {{-- Center pivot --}}
                    <circle cx="110" cy="115" r="7" fill="#ddd"/>
                    <circle cx="110" cy="115" r="4" fill="#fff"/>

                    {{-- Labels --}}
                    <text x="12"  y="112" font-size="9" fill="#bbb" font-family="Arial" font-weight="700">LEVE</text>
                    <text x="168" y="112" font-size="9" fill="#bbb" font-family="Arial" font-weight="700">SEVERO</text>
                </svg>
            </div>

            <div class="gauge-angle-val">{{ $anguloDisplay }}<sup>°</sup></div>
            <div class="nivel-wrap">
                <div class="nivel-badge" style="background:{{ $nBg }};color:{{ $nTxt }};border-color:{{ $nBor }}">
                    {{ $nivel }}
                </div>
            </div>
        </div>

        {{-- ── DESCRIPTION CARD ── --}}
        <div class="desc-card">
            <p class="desc-card-title">🔥 ¡Mirá vos, ese meñique ya pide un respiro! 🎉</p>
            <p class="desc-card-body">Nuestros cálculos detectaron que tu dedo ha hecho un gran esfuerzo sosteniendo tu cel. Para que no se canse más, aquí tenés tu documento de canje por un modelo más liviano en Gollo. ¡Aprovechá y estrenálo hoy mismo!</p>
        </div>

        {{-- ── FORM CARD ── --}}
        <div class="form-card">
            <div class="form-card-head">
                <span class="form-card-head-icon">🛡️</span>
                <div class="form-card-head-text">
                    <strong>Datos para tu documento</strong>
                    <small>Llenar para hacer la vuelta 📝</small>
                </div>
            </div>

            @if ($errors->any())
                <div class="form-errores">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('guardar') }}">
                @csrf
                <input type="hidden" name="humana_score"   value="{{ $humana_score }}">
                <input type="hidden" name="angulo_menique" value="{{ $angulo_menique }}">

                <div class="form-field">
                    <label class="form-field-label" for="nombre">Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre') }}"
                           placeholder="Ej: Juan Carlos Rodríguez Jiménez"
                           required autocomplete="name">
                </div>
                <div class="form-field">
                    <label class="form-field-label" for="cedula">
                        Número de Cédula *
                        <span class="badge-formato">(formato tico)</span>
                    </label>
                    <input type="text" id="cedula" name="cedula"
                           value="{{ old('cedula') }}"
                           placeholder="1 - 2345 - 6789"
                           required autocomplete="off">
                </div>
                <div class="form-field">
                    <label class="form-field-label" for="celular">Celular *</label>
                    <input type="text" id="celular" name="celular"
                           value="{{ old('celular') }}"
                           placeholder="8888 - 8888"
                           required autocomplete="tel">
                </div>
                <div class="form-field">
                    <label class="form-field-label" for="email">Correo Electrónico *</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="correo@ejemplo.com"
                           required autocomplete="email">
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
</div>
</body>
</html>
