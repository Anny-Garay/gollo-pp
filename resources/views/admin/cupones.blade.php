@extends('admin.layout')

@section('title', 'Cupones y Control de Stock')

@push('styles')
<style>
    h2 { color: #0b3a6d; margin-bottom: 20px; }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .stat-label {
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    .stat-value {
        font-size: 1.8rem;
        font-weight: 900;
        color: #0b3a6d;
    }
    .stat-sub {
        font-size: 0.8rem;
        color: #94a3b8;
    }
    .stat-card.highlight {
        background: #ecfdf5;
        border-color: #a7f3d0;
    }
    .stat-card.highlight .stat-value { color: #059669; }
    .stat-card.warning {
        background: #fffbeb;
        border-color: #fde68a;
    }
    .stat-card.warning .stat-value { color: #d97706; }

    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; margin-top: 10px; }
    thead th { background: #0b3a6d; color: #fff; padding: 12px 14px; text-align: left; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody tr:hover { background: #f1f5f9; }
    td { padding: 12px 14px; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }

    .cupon-tag {
        display: inline-block;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 800;
        font-family: monospace;
        font-size: 0.95rem;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #bae6fd;
    }
    .monto-tag {
        font-weight: 800;
        color: #e31837;
        font-size: 0.95rem;
    }
    .badge-stock {
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .stock-ok { background: #dcfce7; color: #15803d; }
    .stock-low { background: #fef3c7; color: #b45309; }
    .stock-empty { background: #fee2e2; color: #b91c1c; }

    .progress-bar-wrap {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        margin-top: 4px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 3px;
        background: #0284c7;
    }

    .form-stock-inline {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .input-stock {
        width: 70px;
        padding: 6px 8px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.88rem;
        text-align: center;
    }
    .btn-save-stock {
        background: #0284c7;
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-save-stock:hover { background: #0369a1; }
</style>
@endpush

@section('content')
<div class="card">
    <h2>🎟️ Panel de Cupones y Control de Stock</h2>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- ── METRICS ── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total Cupones Iniciales</span>
            <span class="stat-value">{{ number_format($totalStock) }}</span>
            <span class="stat-sub">Stock asignado en la promoción</span>
        </div>
        <div class="stat-card warning">
            <span class="stat-label">Cupones Entregados</span>
            <span class="stat-value">{{ number_format($totalEntregados) }}</span>
            <span class="stat-sub">Participantes con cupón activo</span>
        </div>
        <div class="stat-card highlight">
            <span class="stat-label">Cupones Restantes</span>
            <span class="stat-value">{{ number_format($totalDisponible) }}</span>
            <span class="stat-sub">Stock disponible para canje</span>
        </div>
    </div>

    {{-- ── TABLA DE CUPONES SEGÚN CONFIGURACIÓN OFICIAL ── --}}
    <h3 style="color:#0b3a6d; margin: 20px 0 10px; font-size:1.1rem;">Tabla Oficial de Cupones por Grado de Inclinación</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Desviación</th>
                    <th>Nombre del cupón</th>
                    <th>Monto de descuento</th>
                    <th>Stock Inicial</th>
                    <th>Entregados</th>
                    <th>Cupones Restantes</th>
                    <th style="min-width: 170px;">Ajustar Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cupones as $c)
                @php
                    $entregados = $c->entregados;
                    $pct = $c->stock_total > 0 ? round(($c->stock_disponible / $c->stock_total) * 100) : 0;
                    $stockClass = $c->stock_disponible > 5 ? 'stock-ok' : ($c->stock_disponible > 0 ? 'stock-low' : 'stock-empty');
                @endphp
                <tr>
                    <td><strong>{{ $c->rango_texto }}</strong></td>
                    <td>
                        <span class="cupon-tag">{{ $c->codigo }}</span>
                    </td>
                    <td>
                        <span class="monto-tag">{{ $c->monto_texto }}</span>
                        <div style="font-size:0.75rem; color:#888;">{{ $c->monto_colones }}</div>
                    </td>
                    <td>{{ $c->stock_total }}</td>
                    <td>
                        <a href="{{ route('admin.participantes', ['cupon' => $c->codigo]) }}" style="font-weight:700; color:#0284c7; text-decoration:none;">
                            {{ $entregados }} participante{{ $entregados != 1 ? 's' : '' }} ↗
                        </a>
                    </td>
                    <td>
                        <span class="badge-stock {{ $stockClass }}">
                            @if($c->stock_disponible > 0)
                                ● {{ $c->stock_disponible }} disponibles
                            @else
                                ✖ Agotado
                            @endif
                        </span>
                        <div class="progress-bar-wrap">
                            <div class="progress-bar-fill" style="width: {{ $pct }}%; background: {{ $c->stock_disponible > 5 ? '#10b981' : ($c->stock_disponible > 0 ? '#f59e0b' : '#ef4444') }}"></div>
                        </div>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.cupones.update', $c) }}" class="form-stock-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="stock_total" value="{{ $c->stock_total }}">
                            <input type="number" name="stock_disponible" class="input-stock" value="{{ $c->stock_disponible }}" min="0" title="Stock disponible">
                            <input type="hidden" name="activo" value="1">
                            <button type="submit" class="btn-save-stock">Guardar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
