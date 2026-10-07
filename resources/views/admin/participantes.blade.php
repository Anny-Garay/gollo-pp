@extends('admin.layout')

@section('title', 'Participantes')

@push('styles')
<style>
    h2 { color: #0b3a6d; margin: 0; }
    .header-actions-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }
    .top-actions-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .top-actions-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 6px;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: transform 0.1s, opacity 0.15s;
    }
    .btn-export:active { transform: scale(0.97); }
    .btn-export-csv {
        background: #107c41;
        color: #fff;
    }
    .btn-export-csv:hover { background: #0c5e31; }
    .btn-export-zip {
        background: #0284c7;
        color: #fff;
    }
    .btn-export-zip:hover { background: #0369a1; }

    .search-form { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
    .search-form input[type=text] {
        padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; width: 280px;
    }
    .search-form select {
        padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: #fff;
    }
    .search-form button {
        padding: 9px 16px; background: #0b3a6d; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 700;
    }
    .search-form a { padding: 9px 12px; color: #64748b; text-decoration: none; font-size: 0.85rem; align-self: center; }
    .badge { background: #0b3a6d; color: #fff; border-radius: 20px; padding: 3px 10px; font-size: 0.8rem; }
    
    .stock-banner-strip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .stock-chips {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .stock-chip {
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        color: #334155;
    }
    .stock-chip:hover {
        border-color: #0284c7;
    }
    .stock-chip.active {
        background: #0b3a6d;
        color: #fff;
        border-color: #0b3a6d;
    }
    .stock-chip-num {
        font-weight: 800;
        padding: 1px 6px;
        border-radius: 10px;
        font-size: 0.75rem;
    }
    .stock-chip .ok { background: #dcfce7; color: #15803d; }
    .stock-chip .zero { background: #fee2e2; color: #b91c1c; }

    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; min-width: 950px; }
    thead th { background: #0b3a6d; color: #fff; padding: 12px 14px; text-align: left; white-space: nowrap; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody tr:hover { background: #f1f5f9; }
    td { padding: 12px 14px; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
    .foto-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1; display: block; }
    .sin-foto { color: #94a3b8; font-size: 0.82rem; }
    
    .badge-cupon {
        display: inline-block;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 800;
        font-family: monospace;
        font-size: 0.9rem;
        padding: 4px 9px;
        border-radius: 6px;
        border: 1px solid #bae6fd;
    }
    .badge-monto {
        font-weight: 800;
        color: #e31837;
        font-size: 0.9rem;
        white-space: nowrap;
    }
    .sin-cupon {
        color: #94a3b8;
        font-size: 0.82rem;
        font-style: italic;
    }
    
    .btn-delete {
        background: #ef4444; color: #fff; border: none; border-radius: 6px;
        padding: 6px 12px; cursor: pointer; font-size: 0.82rem; font-weight: 700;
        transition: background 0.15s;
    }
    .btn-delete:hover { background: #dc2626; }
    .pagination-wrap { margin-top: 20px; display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; }
    .pagination-wrap a, .pagination-wrap span {
        padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 5px; color: #0b3a6d; text-decoration: none; font-size: 0.88rem;
    }
    .pagination-wrap span[aria-current] { background: #0b3a6d; color: #fff; border-color: #0b3a6d; }
    .empty { text-align: center; color: #94a3b8; padding: 40px 0; }
</style>
@endpush

@section('content')
<div class="card">
    <div class="header-actions-bar">
        <div class="top-actions-left">
            <h2>Participantes <span class="badge">{{ $participantes->total() }}</span></h2>
        </div>

        <div class="top-actions-right">
            <a href="{{ route('admin.participantes.export.csv', request()->query()) }}" class="btn-export btn-export-csv" title="Descargar listado completo en archivo CSV para Excel">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Descargar Lista (CSV)
            </a>
            <a href="{{ route('admin.participantes.export.fotos') }}" class="btn-export btn-export-zip" title="Descargar todas las fotos zippeadas con el nombre e ID de cada participante">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                    <circle cx="12" cy="13" r="4"></circle>
                </svg>
                Descargar Todas las Fotos (.ZIP)
            </a>
        </div>
    </div>

    {{-- ── BARRA DE BÚSQUEDA Y FILTROS ── --}}
    <div style="margin-bottom: 20px;">
        <form class="search-form" method="GET" action="{{ route('admin.participantes') }}">
            <input type="text" name="buscar" placeholder="Buscar por nombre, cédula, email o cupón…" value="{{ request('buscar') }}">
            @if(isset($cupones))
            <select name="cupon" onchange="this.form.submit()">
                <option value="">Todos los cupones</option>
                @foreach($cupones as $c)
                    <option value="{{ $c->codigo }}" {{ request('cupon') == $c->codigo ? 'selected' : '' }}>
                        {{ $c->codigo }} ({{ $c->monto_texto }})
                    </option>
                @endforeach
            </select>
            @endif
            <button type="submit">Buscar</button>
            @if(request('buscar') || request('cupon'))
                <a href="{{ route('admin.participantes') }}">✕ Limpiar</a>
            @endif
        </form>
    </div>

    {{-- ── QUICK STOCK BANNER ── --}}
    @if(isset($cupones))
    <div class="stock-banner-strip">
        <div style="font-size:0.85rem; color:#0b3a6d; font-weight:700;">
            🎟️ Cupones restantes en stock:
        </div>
        <div class="stock-chips">
            @foreach($cupones as $c)
                <a href="{{ route('admin.participantes', ['cupon' => $c->codigo]) }}"
                   class="stock-chip {{ request('cupon') == $c->codigo ? 'active' : '' }}"
                   title="{{ $c->rango_texto }} — {{ $c->monto_texto }}">
                    <strong>{{ $c->codigo }}:</strong>
                    <span class="stock-chip-num {{ $c->stock_disponible > 0 ? 'ok' : 'zero' }}">
                        {{ $c->stock_disponible }} / {{ $c->stock_total }}
                    </span>
                </a>
            @endforeach
            <a href="{{ route('admin.cupones') }}" style="font-size:0.82rem; font-weight:700; color:#0284c7; text-decoration:none; align-self:center; margin-left:4px;">
                Ver panel completo ↗
            </a>
        </div>
    </div>
    @endif

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    @if($participantes->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 70px;">Foto</th>
                    <th>Desviación</th>
                    <th>Cupón</th>
                    <th>Descuento</th>
                    <th>Nombre y Apellido</th>
                    <th>Cédula</th>
                    <th>Celular</th>
                    <th>Email</th>
                    <th>Registrado</th>
                    <th style="text-align: right; width: 90px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participantes as $p)
                <tr>
                    <td><strong>{{ $p->id }}</strong></td>
                    <td>
                        @if($p->foto)
                            <a href="{{ asset('img/' . $p->foto) }}" target="_blank" title="Ver foto original">
                                <img class="foto-thumb" src="{{ asset('img/' . $p->foto) }}" alt="foto">
                            </a>
                        @else
                            <span class="sin-foto">Sin foto</span>
                        @endif
                    </td>
                    <td><strong>{{ $p->angulo_menique !== null ? $p->angulo_menique . '°' : '—' }}</strong></td>
                    <td>
                        @if($p->cupon_codigo)
                            <span class="badge-cupon">{{ $p->cupon_codigo }}</span>
                        @else
                            <span class="sin-cupon">Sin cupón</span>
                        @endif
                    </td>
                    <td>
                        @if($p->cupon_monto_texto || $p->cupon_monto)
                            <span class="badge-monto">{{ $p->cupon_monto_texto ?? ('₡' . number_format($p->cupon_monto, 0, ',', '.')) }}</span>
                        @else
                            <span class="sin-cupon">—</span>
                        @endif
                    </td>
                    <td><strong>{{ $p->nombre }}</strong></td>
                    <td>{{ $p->cedula }}</td>
                    <td>{{ $p->celular }}</td>
                    <td>{{ $p->email }}</td>
                    <td style="white-space: nowrap; color: #64748b; font-size: 0.85rem;">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td style="text-align: right;">
                        <form method="POST" action="{{ route('admin.participantes.destroy', $p) }}"
                              onsubmit="return confirm('¿Eliminar a {{ addslashes($p->nombre) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-delete">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $participantes->links('pagination::simple-bootstrap-5') }}
    </div>
    @else
        <p class="empty">No hay participantes registrados aún.</p>
    @endif
</div>
@endsection
