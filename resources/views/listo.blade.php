<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Listo! - Phone Pinky</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        .listo-body {
            background: #f0f2f5 !important;
            background-image: none !important;
            align-items: unset !important;
            min-height: 100dvh;
        }
        .listo-page {
            max-width: 430px;
            width: 100%;
            margin: 0 auto;
            font-family: Arial, Helvetica, sans-serif;
            padding-bottom: 40px;
        }

        /* ── Top hero ── */
        .listo-hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 36px 20px 20px;
            text-align: center;
        }
        .listo-check {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 3px solid #1a1a2e;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }
        .listo-hero h1 {
            font-size: 1.9rem;
            font-weight: 900;
            color: #1a1a2e;
            margin: 0 0 6px;
        }
        .listo-hero p {
            font-size: 0.95rem;
            color: #555;
            margin: 0;
        }

        /* ── Document card ── */
        .doc-card {
            margin: 0 13px;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
        }

        /* Header */
        .doc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1046a0;
            padding: 14px 16px;
        }
        .doc-header-logo {
            height: 36px;
            object-fit: contain;
        }
        .doc-header-right {
            text-align: right;
        }
        .doc-header-right .doc-label {
            display: block;
            color: rgba(255,255,255,0.65);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .doc-header-right .doc-number {
            color: #fff;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        /* Banner */
        .doc-banner {
            background: #1a1a2e;
            text-align: center;
            padding: 9px 12px;
            color: rgba(255,255,255,0.75);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Body */
        .doc-body {
            padding: 18px 18px 0;
        }
        .doc-field-label {
            font-size: 9.5px;
            font-weight: 700;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 2px;
        }
        .doc-field-value {
            font-size: 18px;
            font-weight: 800;
            color: #1a1a2e;
            margin: 0 0 14px;
        }
        .doc-row {
            display: flex;
            gap: 24px;
            margin-bottom: 14px;
        }
        .doc-row .doc-col { flex: 1; }
        .doc-row .doc-col .doc-field-value { font-size: 14px; }
        .doc-vence {
            color: #e31837 !important;
        }

        /* Diagnóstico box */
        .doc-diag {
            border: 1.5px solid #f57c00;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 14px;
            background: #fffaf5;
        }
        .doc-diag-title {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #f57c00;
            margin: 0 0 8px;
        }
        .doc-diag-main {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .doc-diag-angle-wrap {
            text-align: center;
            flex-shrink: 0;
        }
        .doc-diag-angle {
            font-size: 38px;
            font-weight: 900;
            color: #e31837;
            line-height: 1;
            display: block;
        }
        .doc-diag-angle-label {
            font-size: 8px;
            font-weight: 700;
            color: #aaa;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .doc-diag-info strong {
            display: block;
            font-size: 15px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 3px;
        }
        .doc-diag-info p {
            font-size: 12px;
            color: #777;
            margin: 0;
            line-height: 1.4;
        }

        /* Beneficio box */
        .doc-beneficio {
            border: 1.5px solid #27ae60;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 16px;
            background: #f6fff8;
        }
        .doc-beneficio-header {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 6px;
        }
        .doc-beneficio-badge {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #27ae60;
        }
        .doc-beneficio-title {
            font-size: 15px;
            font-weight: 800;
            color: #1a1a2e;
            margin: 0 0 5px;
        }
        .doc-beneficio-loc {
            display: flex;
            align-items: flex-start;
            gap: 5px;
            font-size: 12px;
            color: #666;
        }

        /* QR / Sello row */
        .doc-footer {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 18px 16px;
        }
        .doc-qr img {
            width: 80px;
            height: 80px;
            display: block;
            border-radius: 6px;
        }
        .doc-qr-info {
            flex: 1;
        }
        .doc-qr-info p {
            font-size: 11.5px;
            color: #555;
            margin: 0 0 4px;
            line-height: 1.4;
        }
        .doc-qr-info .doc-qr-site {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 700;
            color: #e31837;
            text-decoration: none;
        }
        .doc-sello {
            flex-shrink: 0;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 2.5px solid #e31837;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .doc-sello-check {
            font-size: 18px;
            line-height: 1;
            color: #e31837;
        }
        .doc-sello-text {
            font-size: 7px;
            font-weight: 800;
            color: #e31837;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .doc-sello-sub {
            font-size: 7px;
            color: #aaa;
            margin-top: 2px;
        }
        .doc-divider {
            height: 1px;
            background: #f0f0f0;
            margin: 0 18px 14px;
        }
        .doc-fine {
            padding: 0 18px 18px;
            font-size: 9.5px;
            color: #bbb;
            line-height: 1.5;
            text-align: center;
        }

        /* ── Product catalog ── */
        .catalogo-section {
            margin: 0 13px;
        }
        .catalogo-title {
            font-size: 13px;
            font-weight: 900;
            color: #1a1a2e;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin: 0 0 12px;
            text-align: center;
        }
        .catalogo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .prod-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
        }
        .prod-card-img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            display: block;
            background: #f0f2f5;
        }
        .prod-card-img-placeholder {
            width: 100%;
            aspect-ratio: 1 / 1;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
        }
        .prod-card-body {
            padding: 10px 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }
        .prod-card-name {
            font-size: 13px;
            font-weight: 800;
            color: #1a1a2e;
            line-height: 1.3;
            margin: 0;
        }
        .prod-card-price {
            font-size: 15px;
            font-weight: 900;
            color: #e31837;
            margin: 0;
        }
        .prod-card-btn {
            display: block;
            text-align: center;
            background: #1a1a2e;
            color: #fff;
            font-size: 11.5px;
            font-weight: 800;
            padding: 8px 6px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: auto;
            letter-spacing: 0.5px;
        }
        .prod-card-btn:active { opacity: 0.85; }
            display: flex;
            gap: 7px;
            justify-content: center;
            padding: 20px 0 0;
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
<body class="listo-body">
@php
    $angulo        = min(20.0, (float)($angulo_menique ?? 0));
    $anguloDisplay = $angulo_menique !== null ? number_format($angulo, 1) : '—';

    if ($angulo <= 4)      { $nivel = 'Nivel 1 — Leve';    }
    elseif ($angulo <= 8)  { $nivel = 'Nivel 2 — Bajo';    }
    elseif ($angulo <= 12) { $nivel = 'Nivel 3 — Moderado';}
    elseif ($angulo <= 16) { $nivel = 'Nivel 4 — Alto';    }
    else                   { $nivel = 'Nivel 5 — Severo';  }

    $hoy         = \Carbon\Carbon::now();
    $emision     = $hoy->format('d/m/Y');
    $vence       = $hoy->copy()->addDays(30)->format('d/m/Y');

    $qrData      = urlencode($doc_number . ' | ' . $nombre . ' | gollo.com');
    $qrUrl       = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' . $qrData;
@endphp

<div class="listo-page">

    {{-- ── HERO ── --}}
    <div class="listo-hero">
        <div class="listo-check">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <path d="M5 13l4 4L19 7" stroke="#1a1a2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1>¡Listo, compa!</h1>
        <p>Tu Documento de canje está ready 🎉</p>
    </div>

    {{-- ── DOCUMENT CARD ── --}}
    <div class="doc-card">

        {{-- Header azul --}}
        <div class="doc-header">
            <img src="{{ asset('img/Recurso 12.png') }}" alt="Gollo" class="doc-header-logo">
            <div class="doc-header-right">
                <span class="doc-label">N° Documento</span>
                <span class="doc-number">{{ $doc_number }}</span>
            </div>
        </div>

        {{-- Banner negro --}}
        <div class="doc-banner">Documento de canje oficial &mdash; Phone Pinky™</div>

        {{-- Datos del beneficiario --}}
        <div class="doc-body">
            <p class="doc-field-label">Beneficiario</p>
            <p class="doc-field-value">{{ $nombre }}</p>

            <div class="doc-row">
                <div class="doc-col">
                    <p class="doc-field-label">Cédula</p>
                    <p class="doc-field-value">{{ $cedula }}</p>
                </div>
                <div class="doc-col">
                    <p class="doc-field-label">Emisión</p>
                    <p class="doc-field-value">{{ $emision }}</p>
                </div>
            </div>

            <p class="doc-field-label">Vence</p>
            <p class="doc-field-value doc-vence">{{ $vence }}</p>

            {{-- Diagnóstico --}}
            <div class="doc-diag">
                <p class="doc-diag-title">Diagnóstico Phone Pinky™</p>
                <div class="doc-diag-main">
                    <div class="doc-diag-angle-wrap">
                        <span class="doc-diag-angle">{{ $anguloDisplay }}°</span>
                        <span class="doc-diag-angle-label">Inclinación</span>
                    </div>
                    <div class="doc-diag-info">
                        <strong>{{ $nivel }}</strong>
                        <p>Tu meñique ya tiene el sello del celular.</p>
                    </div>
                </div>
            </div>

            {{-- Beneficio --}}
            <div class="doc-beneficio">
                <div class="doc-beneficio-header">
                    <span style="color:#27ae60;font-size:13px;">✓</span>
                    <span class="doc-beneficio-badge">Beneficio Otorgado</span>
                </div>
                <p class="doc-beneficio-title">Beneficio especial en tu próximo celular</p>
                <div class="doc-beneficio-loc">
                    <span>📍</span>
                    <span>Aplicable en cualquier Tienda Gollo del país</span>
                </div>
            </div>
        </div>

        <div class="doc-divider"></div>

        {{-- QR + Sello --}}
        <div class="doc-footer">
            <div class="doc-qr">
                <img src="{{ $qrUrl }}" alt="QR Documento" loading="lazy">
            </div>
            <div class="doc-qr-info">
                <p>Mostrá QR o el documento en tienda</p>
                <a href="https://gollo.com" class="doc-qr-site" target="_blank" rel="noopener">
                    📞 gollo.com
                </a>
            </div>
            <div class="doc-sello">
                <span class="doc-sello-check">✓</span>
                <span class="doc-sello-text">Sello<br>Oficial</span>
                <span class="doc-sello-sub">Gollo Digital</span>
            </div>
        </div>

        <div class="doc-fine">
            Válido por 30 días desde la fecha de emisión. No acumulable con otras promociones.
            Sujeto a disponibilidad de inventario. Documento generado con Phone Pinky™ IA.
        </div>
    </div>

    <div class="step-dots">
        <span class="step-dot"></span>
        <span class="step-dot"></span>
        <span class="step-dot"></span>
        <span class="step-dot step-dot--active"></span>
        <span class="step-dot"></span>
    </div>

    {{-- ── PRODUCT CATALOG ── --}}
    @if(isset($productos) && $productos->count())
    <div style="height:20px;"></div>
    <div class="catalogo-section">
        <p class="catalogo-title">📱 Celulares recomendados para vos</p>
        <div class="catalogo-grid">
            @foreach($productos as $prod)
            <div class="prod-card">
                @if($prod->foto)
                    <img class="prod-card-img" src="{{ asset('img/' . $prod->foto) }}" alt="{{ $prod->nombre }}">
                @else
                    <div class="prod-card-img-placeholder">📱</div>
                @endif
                <div class="prod-card-body">
                    <p class="prod-card-name">{{ $prod->nombre }}</p>
                    <p class="prod-card-price">₡{{ number_format($prod->precio, 0, ',', '.') }}</p>
                    <a class="prod-card-btn" href="{{ $prod->link_externo }}" target="_blank" rel="noopener">
                        Ver en Gollo →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
</body>
</html>
