@extends('layouts.blanco')

@section('content')

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
        <h1>¡Solo bueno!</h1>
        <p>Tu Pinky Promo está lista para ser canjeada</p>
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

        @include('compartir')

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

@endsection