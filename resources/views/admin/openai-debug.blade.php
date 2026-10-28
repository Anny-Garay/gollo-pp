@extends('admin.layout')

@section('title', 'Debug OpenAI')

@push('styles')
<style>
    .debug-pre {
        background: #0f172a;
        color: #e2e8f0;
        padding: 14px;
        border-radius: 8px;
        overflow: auto;
        font-size: 12px;
        line-height: 1.45;
        white-space: pre-wrap;
        word-break: break-word;
        max-height: 420px;
    }
    .debug-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin: 18px 0;
    }
    .debug-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 14px;
    }
    .debug-item .k {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #64748b;
        font-weight: 700;
    }
    .debug-item .v {
        font-size: 18px;
        font-weight: 800;
        color: #0b3a6d;
        margin-top: 4px;
        word-break: break-word;
    }
    .debug-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        border-radius: 8px;
        padding: 12px 14px;
        margin: 14px 0;
        font-weight: 700;
    }
    .debug-ok {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        border-radius: 8px;
        padding: 12px 14px;
        margin: 14px 0;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="card">
    <h2>🔍 Debug OpenAI Vision</h2>
    <p style="color:#555; margin-bottom:18px;">
        Subí una foto para ver exactamente qué devuelve el modelo. Esta página solo es accesible con el login de administrador y no crea participantes ni cupones.
    </p>

    <form method="POST" action="{{ route('admin.debug.openai.analyze') }}" id="debug-form">
        @csrf
        <input type="file" id="debug-file" accept="image/*" capture="environment" required>
        <input type="hidden" name="imagen" id="debug-imagen">
        <button type="submit" id="debug-submit" disabled style="background:#0284c7; color:#fff; border:none; padding:10px 16px; border-radius:8px; font-weight:700; cursor:pointer;">
            Analizar con OpenAI
        </button>
        <span id="debug-status" style="margin-left:10px; color:#64748b; font-size:13px;"></span>
    </form>

    <script>
        (function () {
            const fileInput = document.getElementById('debug-file');
            const hidden    = document.getElementById('debug-imagen');
            const submit    = document.getElementById('debug-submit');
            const status    = document.getElementById('debug-status');

            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                hidden.value = '';
                submit.disabled = true;

                if (!file) {
                    status.textContent = '';
                    return;
                }

                status.textContent = 'Procesando foto...';

                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = new Image();
                    img.onload = function () {
                        let w = img.width;
                        let h = img.height;
                        const max = 1024;

                        if (w > max || h > max) {
                            if (w >= h) {
                                h = Math.round(h * max / w);
                                w = max;
                            } else {
                                w = Math.round(w * max / h);
                                h = max;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = w;
                        canvas.height = h;
                        canvas.getContext('2d').drawImage(img, 0, 0, w, h);

                        hidden.value = canvas.toDataURL('image/jpeg', 0.85);
                        submit.disabled = false;
                        status.textContent = 'Foto lista para analizar.';
                    };
                    img.onerror = function () {
                        status.textContent = 'No se pudo leer la imagen.';
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>

    @if ($errors->any())
        <div class="alert-error" style="margin-top:14px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @isset($resultado)
        @php
            $debug  = $resultado['debug'] ?? [];
            $parsed = $debug['parsed'] ?? null;
            $points = $resultado['pinky_points'] ?? null;
        @endphp

        @if (!empty($debug['error']))
            <div class="debug-error">⚠️ {{ $debug['error'] }}</div>
        @else
            <div class="debug-ok">✅ Llamada completada sin error de transporte/parseo.</div>
        @endif

        @isset($imagen)
            <div style="margin:16px 0;">
                <img src="{{ $imagen }}" alt="Foto analizada" style="max-width:320px; width:100%; border-radius:10px; border:1px solid #e2e8f0;">
            </div>
        @endisset

        <div class="debug-grid">
            <div class="debug-item">
                <div class="k">humana_score</div>
                <div class="v">{{ $resultado['humana_score'] ?? '—' }}</div>
            </div>
            <div class="debug-item">
                <div class="k">solo_menique</div>
                <div class="v">{{ is_null($resultado['solo_menique'] ?? null) ? '—' : ($resultado['solo_menique'] ? 'true' : 'false') }}</div>
            </div>
            <div class="debug-item">
                <div class="k">ángulo calculado</div>
                <div class="v">{{ is_null($resultado['angulo_menique'] ?? null) ? '—' : $resultado['angulo_menique'] . '°' }}</div>
            </div>
            <div class="debug-item">
                <div class="k">ángulo crudo (sin ajuste)</div>
                <div class="v">{{ is_null($debug['angulo_crudo'] ?? null) ? '—' : $debug['angulo_crudo'] . '°' }}</div>
            </div>
            <div class="debug-item">
                <div class="k">HTTP / modelo</div>
                <div class="v">{{ $debug['status'] ?? '—' }} · {{ $debug['model'] ?? '—' }}</div>
            </div>
            <div class="debug-item">
                <div class="k">finish_reason / max_tokens</div>
                <div class="v">{{ $debug['finish_reason'] ?? '—' }} · {{ $debug['max_tokens'] ?? '—' }}</div>
            </div>
        </div>

        <p style="color:#64748b; font-size:13px; margin-bottom:18px;">
            El flujo público eleva el ángulo a 2.0° como mínimo y lo limita a 20°. Acá ves el <strong>ángulo crudo</strong> antes de ese ajuste.
            Si <strong>finish_reason</strong> es <strong>length</strong>, la respuesta se cortó por <strong>max_tokens</strong>.
        </p>

        <h3 style="color:#0b3a6d; margin:20px 0 8px;">pinky_points devueltos</h3>
        <pre class="debug-pre">{{ $points === null ? '—' : json_encode($points, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

        <h3 style="color:#0b3a6d; margin:20px 0 8px;">Contenido crudo del modelo</h3>
        <pre class="debug-pre">{{ $debug['content'] ?? '—' }}</pre>

        <h3 style="color:#0b3a6d; margin:20px 0 8px;">JSON parseado</h3>
        <pre class="debug-pre">{{ $parsed === null ? '—' : json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

        <details style="margin-top:20px;">
            <summary style="cursor:pointer; font-weight:700; color:#0b3a6d;">Ver respuesta HTTP completa</summary>
            <pre class="debug-pre" style="margin-top:10px;">{{ $debug['body'] ?? '—' }}</pre>
        </details>

        @if (!empty($debug['usage']))
            <details style="margin-top:12px;">
                <summary style="cursor:pointer; font-weight:700; color:#0b3a6d;">Ver uso de tokens</summary>
                <pre class="debug-pre" style="margin-top:10px;">{{ json_encode($debug['usage'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
        @endif
    @endisset
</div>
@endsection
