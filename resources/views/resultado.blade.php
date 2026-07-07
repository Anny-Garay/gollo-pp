@extends('layouts.blanco')

@section('content')

@php
    $angulo    = min(20.0, (float)($angulo_menique ?? 0));
    $maxGauge  = 20.0;
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

    if ($angulo <= 4)      { $nivel_num = 1; $nBg = '#e8f5e9'; $nTxt = '#2e7d32'; $nBor = '#2e7d32'; }
    elseif ($angulo <= 8)  { $nivel_num = 2; $nBg = '#f9fbe7'; $nTxt = '#558b2f'; $nBor = '#558b2f'; }
    elseif ($angulo <= 12) { $nivel_num = 3; $nBg = '#fffde7'; $nTxt = '#f9a825'; $nBor = '#f9a825'; }
    elseif ($angulo <= 16) { $nivel_num = 4; $nBg = '#fff3e0'; $nTxt = '#e65100'; $nBor = '#e65100'; }
    else                   { $nivel_num = 5; $nBg = '#fff0f0'; $nTxt = '#e31837'; $nBor = '#e31837'; }

    $nivelTexto = \App\Models\NivelTexto::where('nivel', $nivel_num)->first();
    $nivel      = $nivelTexto?->titulo ?? "NIVEL {$nivel_num}";

    $anguloDisplay = $angulo_menique !== null ? number_format($angulo, 1) : '—';
@endphp

<div class="res-page">

    <div class="res-top-actions">
        <a href="{{ route('carga') }}" class="res-back-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M15 19l-7-7 7-7" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
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
            @if($nivelTexto)
                <p class="desc-card-title">{{ $nivelTexto->titulo }}</p>
                <div class="desc-card-body">{!! $nivelTexto->contenido !!}</div>
            @else
                <p class="desc-card-title">🔥 ¡Mirá vos, ese meñique ya pide un respiro! 🎉</p>
                <p class="desc-card-body">Nuestros cálculos detectaron que tu dedo ha hecho un gran esfuerzo sosteniendo tu cel. Para que no se canse más, aquí tenés tu documento de canje por un modelo más liviano en Gollo. ¡Aprovechá y estrenálo hoy mismo!</p>
            @endif
        </div>

        {{-- ── FORM CARD ── --}}
        <div class="form-card">
            <div class="form-card-head">
                <span class="form-card-head-icon">🛡️</span>
                <div class="form-card-head-text">
                    <strong>Datos para tu documento</strong>
                    <small>Llená para recibir tu descuento</small>
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
                        Número de Cédula o DIMEX *
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

                <button type="submit" class="btn-guardar">Obtener mi descuento</button>
            </form>
            <sub>Solo usamos tus datos para generar el descuento</sub>
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

@endsection