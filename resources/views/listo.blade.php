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

        {{-- Body con datos de canje y cupón --}}
        <div class="doc-body">
            <div class="doc-row">
                <div class="doc-col">
                    <div class="doc-field-label">Beneficiario</div>
                    <div class="doc-field-value">{{ $nombre }}</div>
                </div>
                <div class="doc-col">
                    <div class="doc-field-label">Cédula / DIMEX</div>
                    <div class="doc-field-value">{{ $cedula }}</div>
                </div>
            </div>

            {{-- Cupón asignado --}}
            @if(!empty($cupon_codigo))
            <div class="doc-beneficio" style="background: #f0f7ff; border: 2px solid #004DB5; border-radius: 14px; padding: 14px 16px; margin-bottom: 16px;">
                <div class="doc-beneficio-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 6px;">
                    <span class="doc-beneficio-badge" style="color: #004DB5; font-size: 10px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">🎟️ Cupón oficial</span>
                    <span style="background: #004DB5; color: #fff; font-family: monospace; font-weight: 900; font-size: 15px; padding: 3px 10px; border-radius: 6px; letter-spacing: 1px;">
                        {{ $cupon_codigo }}
                    </span>
                </div>
                <div class="doc-beneficio-title" style="font-size: 22px; font-weight: 900; color: #e31837; margin: 4px 0 6px;">
                    {{ $cupon_monto_texto ?? ('₡' . number_format($cupon_monto, 0, ',', '.')) }} de descuento
                </div>
                <p style="font-size: 12px; color: #555; margin: 0; line-height: 1.4;">Presentá este cupón en cualquier tienda Gollo o en gollo.com para aplicar tu descuento.</p>
            </div>
            @elseif(!empty($cupon_agotado))
            <div class="doc-beneficio" style="background: #fff8f8; border: 1.5px solid #e74c3c; border-radius: 12px; padding: 12px 14px; margin-bottom: 16px;">
                <div style="font-size: 13px; font-weight: 800; color: #c0392b;">Stock de cupones agotado para esta categoría</div>
                <p style="font-size: 12px; color: #777; margin: 4px 0 0;">Los cupones para este nivel de inclinación se han agotado.</p>
            </div>
            @endif

            <div class="doc-row">
                <div class="doc-col">
                    <div class="doc-field-label">Desviación Meñique</div>
                    <div class="doc-field-value" style="color: #004DB5;">{{ $anguloDisplay }}° ({{ $nivel }})</div>
                </div>
                <div class="doc-col">
                    <div class="doc-field-label">Válido hasta</div>
                    <div class="doc-field-value doc-vence">{{ $vence }}</div>
                </div>
            </div>
        </div>

        @include('compartir')

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

</div>

@endsection