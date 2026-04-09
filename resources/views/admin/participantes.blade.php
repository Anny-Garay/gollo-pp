@extends('admin.layout')

@section('title', 'Participantes')

@push('styles')
<style>
    h2 { color: #0b3a6d; margin-bottom: 20px; }
    .top-bar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .search-form { display: flex; gap: 8px; }
    .search-form input[type=text] {
        padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 0.9rem; width: 240px;
    }
    .search-form button {
        padding: 8px 16px; background: #2a8fd8; color: #fff; border: none; border-radius: 6px; cursor: pointer;
    }
    .search-form a { padding: 8px 12px; color: #666; text-decoration: none; font-size: 0.85rem; }
    .badge { background: #0b3a6d; color: #fff; border-radius: 20px; padding: 2px 10px; font-size: 0.8rem; }
    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    thead th { background: #0b3a6d; color: #fff; padding: 10px 12px; text-align: left; }
    tbody tr:nth-child(even) { background: #f7f9fb; }
    tbody tr:hover { background: #e8f4fd; }
    td { padding: 10px 12px; vertical-align: middle; border-bottom: 1px solid #eee; }
    .foto-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
    .sin-foto { color: #aaa; font-size: 0.8rem; }
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
            <input type="text" name="buscar" placeholder="Buscar por nombre, cédula o email…" value="{{ request('buscar') }}">
            <button type="submit">Buscar</button>
            @if(request('buscar'))
                <a href="{{ route('admin.participantes') }}">✕ Limpiar</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($participantes->count())
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Foto</th>
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
                        <img class="foto-thumb" src="{{ asset('storage/' . $p->foto) }}" alt="foto">
                    @else
                        <span class="sin-foto">Sin foto</span>
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
