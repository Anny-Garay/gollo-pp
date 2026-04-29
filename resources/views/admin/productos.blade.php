@extends('admin.layout')

@section('title', 'Productos')

@push('styles')
<style>
    h2 { color: #0b3a6d; margin-bottom: 20px; }

    .layout-2col {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
        align-items: start;
    }
    @media (max-width: 860px) {
        .layout-2col { grid-template-columns: 1fr; }
    }

    /* Table */
    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    thead th { background: #0b3a6d; color: #fff; padding: 10px 12px; text-align: left; }
    tbody tr:nth-child(even) { background: #f7f9fb; }
    tbody tr:hover { background: #e8f4fd; }
    td { padding: 10px 12px; vertical-align: middle; border-bottom: 1px solid #eee; }
    .prod-thumb { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
    .sin-foto { color: #aaa; font-size: 0.8rem; }
    .precio-cell { font-weight: 700; color: #0b3a6d; }
    .link-ext { color: #2a8fd8; font-size: 0.8rem; word-break: break-all; }
    .badge-on  { background: #d4edda; color: #155724; border-radius: 20px; padding: 2px 10px; font-size: 0.78rem; font-weight: 700; }
    .badge-off { background: #f8d7da; color: #721c24; border-radius: 20px; padding: 2px 10px; font-size: 0.78rem; font-weight: 700; }
    .td-actions { display: flex; gap: 6px; }
    .btn-edit   { background: #f0ad4e; color: #fff; border: none; border-radius: 5px; padding: 5px 10px; cursor: pointer; font-size: 0.82rem; text-decoration: none; }
    .btn-edit:hover { background: #d9922a; }
    .btn-delete { background: #e74c3c; color: #fff; border: none; border-radius: 5px; padding: 5px 10px; cursor: pointer; font-size: 0.82rem; }
    .btn-delete:hover { background: #c0392b; }
    .empty { text-align: center; color: #999; padding: 32px 0; }

    /* Form card */
    .form-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,.08); padding: 24px; }
    .form-card h3 { color: #0b3a6d; margin-bottom: 18px; font-size: 1rem; }
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 0.82rem; font-weight: 700; color: #444; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-group input[type=text],
    .form-group input[type=url],
    .form-group input[type=number],
    .form-group input[type=file] {
        width: 100%; padding: 9px 12px; border: 1px solid #ddd; border-radius: 6px;
        font-size: 0.92rem; box-sizing: border-box;
    }
    .form-group input:focus { outline: none; border-color: #0b3a6d; }
    .check-row { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; }
    .check-row input { width: auto; }
    .btn-submit { background: #0b3a6d; color: #fff; border: none; border-radius: 6px; padding: 10px 24px; font-size: 0.9rem; font-weight: 700; cursor: pointer; width: 100%; margin-top: 4px; }
    .btn-submit:hover { background: #0d4a8b; }
    .btn-cancel { display: block; text-align: center; margin-top: 8px; color: #888; font-size: 0.85rem; text-decoration: none; }
    .btn-cancel:hover { color: #333; }

    .preview-wrap { margin-bottom: 10px; }
    .preview-wrap img { max-height: 90px; border-radius: 6px; border: 1px solid #ddd; }
</style>
@endpush

@section('content')
<div class="layout-2col">

    {{-- ── LIST ── --}}
    <div class="card">
        <h2>Productos del Catálogo</h2>

        @if(session('success'))
            <div class="alert-success">✅ {{ session('success') }}</div>
        @endif

        @if($productos->count())
        <table>
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Link</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $p)
                <tr>
                    <td>{{ $p->orden }}</td>
                    <td>
                        @if($p->foto)
                            <img class="prod-thumb" src="{{ asset('img/' . $p->foto) }}" alt="{{ $p->nombre }}">
                        @else
                            <span class="sin-foto">Sin foto</span>
                        @endif
                    </td>
                    <td><strong>{{ $p->nombre }}</strong></td>
                    <td class="precio-cell">₡{{ number_format($p->precio, 2) }}</td>
                    <td><a class="link-ext" href="{{ $p->link_externo }}" target="_blank" rel="noopener">{{ Str::limit($p->link_externo, 35) }}</a></td>
                    <td>
                        @if($p->activo)
                            <span class="badge-on">Activo</span>
                        @else
                            <span class="badge-off">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <div class="td-actions">
                            <a href="{{ route('admin.productos.edit', $p) }}" class="btn-edit">Editar</a>
                            <form method="POST" action="{{ route('admin.productos.destroy', $p) }}"
                                  onsubmit="return confirm('¿Eliminar {{ addslashes($p->nombre) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p class="empty">No hay productos registrados aún.</p>
        @endif
    </div>

    {{-- ── FORM ── --}}
    <div class="form-card">
        @isset($producto)
            <h3>✏️ Editar producto</h3>
            <form method="POST" action="{{ route('admin.productos.update', $producto) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
        @else
            <h3>➕ Nuevo producto</h3>
            <form method="POST" action="{{ route('admin.productos.store') }}" enctype="multipart/form-data">
                @csrf
        @endif

            @if ($errors->any())
                <div class="alert-error">
                    <ul style="margin:0;padding-left:16px;font-size:0.85rem;">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label for="nombre">Nombre del producto *</label>
                <input type="text" id="nombre" name="nombre"
                       value="{{ old('nombre', $producto->nombre ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="precio">Precio (₡) *</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0"
                       value="{{ old('precio', $producto->precio ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="link_externo">Link externo (URL) *</label>
                <input type="url" id="link_externo" name="link_externo"
                       value="{{ old('link_externo', $producto->link_externo ?? '') }}"
                       placeholder="https://gollo.com/producto/..." required>
            </div>

            <div class="form-group">
                <label for="foto">Foto del producto</label>
                @isset($producto)
                    @if($producto->foto)
                        <div class="preview-wrap">
                            <img src="{{ asset('img/' . $producto->foto) }}" alt="Foto actual">
                        </div>
                    @endif
                @endisset
                <input type="file" id="foto" name="foto" accept="image/*">
                <small style="color:#999;font-size:0.78rem;">JPG, PNG, WEBP — máx 5 MB</small>
            </div>

            <div class="form-group">
                <label for="orden">Orden de aparición</label>
                <input type="number" id="orden" name="orden" min="0"
                       value="{{ old('orden', $producto->orden ?? 0) }}">
            </div>

            <div class="form-group">
                <label class="check-row">
                    <input type="checkbox" name="activo" value="1"
                           {{ old('activo', ($producto->activo ?? true) ? '1' : '') ? 'checked' : '' }}>
                    Producto activo (visible en catálogo)
                </label>
            </div>

            <button type="submit" class="btn-submit">
                @isset($producto) Guardar cambios @else Agregar producto @endisset
            </button>

        </form>

        @isset($producto)
            <a href="{{ route('admin.productos') }}" class="btn-cancel">← Cancelar edición</a>
        @endisset
    </div>

</div>
@endsection
