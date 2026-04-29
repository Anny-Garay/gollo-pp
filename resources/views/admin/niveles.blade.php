@extends('admin.layout')

@section('title', 'Textos por Nivel')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    h2 { color: #0b3a6d; margin-bottom: 6px; }
    .page-subtitle { color: #666; font-size: 0.9rem; margin-bottom: 24px; }

    .niveles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(480px, 1fr));
        gap: 20px;
    }

    .nivel-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0,0,0,.08);
        overflow: hidden;
    }
    .nivel-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #eee;
    }
    .nivel-num {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 15px;
        color: #fff;
        flex-shrink: 0;
    }
    .nivel-num-1 { background: #27ae60; }
    .nivel-num-2 { background: #8bc34a; }
    .nivel-num-3 { background: #fdd835; color: #555; }
    .nivel-num-4 { background: #f57c00; }
    .nivel-num-5 { background: #e53935; }

    .nivel-rango {
        font-size: 0.8rem;
        color: #999;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .nivel-card-body { padding: 18px 20px 20px; }

    .field-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: #444;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .field-titulo {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
        margin-bottom: 14px;
        box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
    }
    .field-titulo:focus { outline: none; border-color: #0b3a6d; }

    .quill-container {
        border-radius: 6px;
        overflow: hidden;
    }
    .ql-container { font-size: 0.93rem; font-family: Arial, Helvetica, sans-serif; }
    .ql-editor { min-height: 110px; }

    .btn-save {
        margin-top: 14px;
        background: #0b3a6d;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 9px 22px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        width: 100%;
    }
    .btn-save:hover { background: #0d4a8b; }

    .alert-success {
        background: #d4edda; color: #155724; border: 1px solid #c3e6cb;
        border-radius: 6px; padding: 10px 16px; margin-bottom: 20px;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="card">
    <h2>Textos por Nivel de Meñique</h2>
    <p class="page-subtitle">
        Definí el título y el contenido que se mostrará en el resultado según el ángulo detectado.
        Los rangos son: Nivel 1 (0–4°) · Nivel 2 (5–8°) · Nivel 3 (9–12°) · Nivel 4 (13–16°) · Nivel 5 (17–20°).
    </p>

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    <div class="niveles-grid">
        @php
            $rangos = ['0–4°', '5–8°', '9–12°', '13–16°', '17–20°'];
        @endphp

        @for($n = 1; $n <= 5; $n++)
        @php $texto = $niveles->get($n); @endphp
        <div class="nivel-card">
            <div class="nivel-card-header">
                <span class="nivel-num nivel-num-{{ $n }}">{{ $n }}</span>
                <div>
                    <div style="font-weight:700;color:#0b3a6d;">Nivel {{ $n }}</div>
                    <div class="nivel-rango">Rango: {{ $rangos[$n - 1] }}</div>
                </div>
            </div>
            <div class="nivel-card-body">
                <form method="POST" action="{{ route('admin.niveles.update', $n) }}"
                      onsubmit="syncQuill({{ $n }})">
                    @csrf @method('PUT')

                    <label class="field-label" for="titulo-{{ $n }}">Título del badge</label>
                    <input type="text"
                           id="titulo-{{ $n }}"
                           name="titulo"
                           class="field-titulo"
                           value="{{ old('titulo', $texto?->titulo ?? 'NIVEL '.$n) }}"
                           required>

                    <label class="field-label">Descripción (WYSIWYG)</label>
                    <div class="quill-container">
                        <div id="editor-{{ $n }}">{!! $texto?->contenido ?? '' !!}</div>
                    </div>
                    <input type="hidden" name="contenido" id="contenido-{{ $n }}">

                    <button type="submit" class="btn-save">Guardar Nivel {{ $n }}</button>
                </form>
            </div>
        </div>
        @endfor
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    const quills = {};

    @for($n = 1; $n <= 5; $n++)
    quills[{{ $n }}] = new Quill('#editor-{{ $n }}', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });
    @endfor

    function syncQuill(nivel) {
        const html = quills[nivel].root.innerHTML;
        document.getElementById('contenido-' + nivel).value = html;
    }
</script>
@endsection
