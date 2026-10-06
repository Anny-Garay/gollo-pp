@extends('admin.layout')

@section('title', 'Participantes')

@push('styles')
<style>
    h2 { color: #0b3a6d; margin-bottom: 20px; }
    .top-bar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .search-form { display: flex; gap: 8px; flex-wrap: wrap; }
    .search-form input[type=text] {
        padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 0.9rem; width: 260px;
    }
    .search-form select {
        padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 0.9rem; background: #fff;
    }
    .search-form button {
        padding: 8px 16px; background: #2a8fd8; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 700;
    }
    .search-form a { padding: 8px 12px; color: #666; text-decoration: none; font-size: 0.85rem; align-self: center; }
    .badge { background: #0b3a6d; color: #fff; border-radius: 20px; padding: 2px 10px; font-size: 0.8rem; }
    
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

    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    thead th { background: #0b3a6d; color: #fff; padding: 10px 12px; text-align: left; }
    tbody tr:nth-child(even) { background: #f7f9fb; }
    tbody tr:hover { background: #e8f4fd; }
    td { padding: 10px 12px; vertical-align: middle; border-bottom: 1px solid #eee; }
    .foto-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
    .sin-foto { color: #aaa; font-size: 0.8rem; }
    
    .badge-cupon {
        display: inline-block;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 800;
        font-family: monospace;
        font-size: 0.88rem;
        padding: 3px 8px;
        border-radius: 5px;
        border: 1px solid #bae6fd;
    }
    .badge-monto {
        font-weight: 800;
        color: #e31837;
        font-size: 0.88rem;
    }
    .sin-cupon {
        color: #94a3b8;
        font-size: 0.82rem;
        font-style: italic;
    }
    
    .btn-delete {
        background: #e74c3c; color: #fff; border: none; border-radius: 5px;
        padding: 5px 10px; cursor: pointer; font-size: 0.82rem;
    }
    .btn-delete:hover { background: #c0392b; }
    .pagination-wrap { margin-top: 20px; display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; }
    .pagination-wrap a, .pagination-wrap span {
        padding: 6px 12px; border: 1px solid #ccc; border-radius: 5px; color: #0b3a6d; text-decoration: none; font-size: 0.88rem;
    }
    .pagination-wrap span[aria-current] { background: #0b3a6d; color: #fff; border-color: #0b3a6d; }
    .empty { text-align: center; color: #999; padding: 32px 0; }
</style>
@endpush

@section('content')
<div class="card">
    <div class="top-bar">
        <h2>Participantes <span class="badge">{{ $participantes->total() }}</span></h2>
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

    @if($participantes->count())
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Foto</th>
                <th>Desviación</th>
                <th>Cupón</th>
                <th>Descuento</th>
                <th>Nombre y Apellido</th>
                <th>Cédula</th>
                <th>Celular</th>
                <th>Email</th>
                <th>Registrado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($participantes as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>
                    @if($p->foto)
                        <img class="foto-thumb" src="{{ asset('img/' . $p->foto) }}" alt="foto">
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
                <td>{{ $p->nombre }}</td>
                <td>{{ $p->cedula }}</td>
                <td>{{ $p->celular }}</td>
                <td>{{ $p->email }}</td>
                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                <td>
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

    <div class="pagination-wrap">
        {{ $participantes->links('pagination::simple-bootstrap-5') }}
    </div>
    @else
        <p class="empty">No hay participantes registrados aún.</p>
    @endif
</div>
@endsection
