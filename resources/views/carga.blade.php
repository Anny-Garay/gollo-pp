<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Carga</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        .carga-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
            gap: 20px;
        }
        .titulo-carga { font-size: 1.3rem; font-weight: bold; color: #fff; text-align: center; }

        /* --- Cámara --- */
        #paso-camara { display: flex; flex-direction: column; align-items: center; gap: 12px; width: 100%; max-width: 400px; }
        .camera-container {
            position: relative;
            width: 100%;
            max-width: 360px;
            border-radius: 12px;
            overflow: hidden;
            background: #000;
        }
        #video { width: 100%; display: block; border-radius: 12px; }
        #overlay-mano {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            opacity: 0.55;
            pointer-events: none;
        }
        #btn-capturar {
            padding: 12px 32px;
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            max-width: 360px;
        }
        #btn-capturar:hover { background: #c0392b; }

        /* --- Análisis --- */
        .paso-wrap { display: flex; flex-direction: column; align-items: center; gap: 14px; width: 100%; max-width: 400px; }
        .img-preview-small {
            width: 100%;
            max-height: 220px;
            object-fit: contain;
            border-radius: 12px;
            border: 2px solid #ccc;
        }
        .loader {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #cde;
            font-size: 0.95rem;
        }
        .spinner {
            width: 24px; height: 24px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .card-resultado {
            width: 100%;
            background: rgba(255,255,255,0.12);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .card-resultado p { margin: 0; color: #fff; font-size: 1rem; }
        .card-resultado strong { font-size: 1.35rem; }
        .barra-wrap { background: rgba(0,0,0,0.2); border-radius: 99px; height: 10px; overflow: hidden; }
        .barra-fill { height: 100%; border-radius: 99px; background: #2ecc71; transition: width 0.6s ease; }

        /* --- Botones acción --- */
        .btn-principal {
            padding: 14px 36px;
            background: #27ae60;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        .btn-principal:hover { background: #219150; }
        .btn-volver {
            background: none;
            border: none;
            color: #2a8fd8;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: underline;
        }

        /* --- Formulario --- */
        .form-datos { width: 100%; display: flex; flex-direction: column; gap: 12px; }
        .form-datos label { font-size: 0.85rem; color: #cde; margin-bottom: 2px; display: block; }
        .form-datos input[type="text"],
        .form-datos input[type="email"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #2a8fd8;
            font-size: 1rem;
            background: #fff;
            box-sizing: border-box;
        }
        #canvas { display: none; }
    </style>
</head>
<body class="carga-body">
    <div class="carga-wrap">

        {{-- ── PASO 1: Cámara ── --}}
        <div id="paso-camara">
            <p class="titulo-carga">Tomá una foto de tu mano</p>
            <div class="camera-container">
                <video id="video" autoplay playsinline muted></video>
                <img id="overlay-mano" src="{{ asset('mano.jpeg') }}" alt="">
            </div>
            <button id="btn-capturar">⚪ Capturar</button>
        </div>

        {{-- ── PASO 2: Resultados IA ── --}}
        <div id="paso-analisis" style="display:none" class="paso-wrap">
            <p id="titulo-analisis" class="titulo-carga">Analizando tu foto...</p>
            <img id="img-prev-analisis" class="img-preview-small" src="" alt="Vista previa">

            {{-- Loading --}}
            <div id="analisis-loading" class="loader">
                <div class="spinner"></div>
                <span>Consultando inteligencia artificial…</span>
            </div>

            {{-- Resultado --}}
            <div id="analisis-resultado" style="display:none; width:100%">
                <div class="card-resultado">
                    <p>🤚 ¿Es una mano humana?</p>
                    <div class="barra-wrap"><div class="barra-fill" id="barra-humana" style="width:0%"></div></div>
                    <p><strong id="res-humana">—</strong><span style="font-size:0.85rem; opacity:0.7">/100</span></p>

                    <p style="margin-top:6px">🤙 Ángulo del meñique (pinky phone)</p>
                    <p><strong id="res-angulo">—</strong><span style="font-size:0.85rem; opacity:0.7"> grados</span></p>
                </div>

                <div style="display:flex; flex-direction:column; gap:10px; margin-top:14px; width:100%">
                    <button class="btn-principal" id="btn-continuar" style="display:none">📤 Subir foto y cargar datos</button>
                    <button class="btn-principal" id="btn-reintentar-analisis">← Volver a tomar</button>
                </div>
            </div>
        </div>

        {{-- ── PASO 3: Formulario ── --}}
        <div id="paso-formulario" style="display:none" class="paso-wrap">
            <p class="titulo-carga">¡Listo! Completá tus datos</p>
            <img id="img-prev-form" class="img-preview-small" src="" alt="Vista previa">
            <button class="btn-volver" id="btn-reintentar-form" type="button">← Volver a resultados</button>

            <form id="form-datos" class="form-datos" method="POST" action="{{ route('carga.store') }}">
                @csrf
                <input type="hidden" name="tipo" value="camara">
                <input type="hidden" id="input-imagen-b64"      name="imagen"         value="">
                <input type="hidden" id="input-humana-score"    name="humana_score"   value="">
                <input type="hidden" id="input-angulo-menique"  name="angulo_menique" value="">

                @if ($errors->any())
                    <div style="background:#fdd;border-radius:8px;padding:10px;color:#900;">
                        <ul style="margin:0;padding-left:16px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="nombre">Nombre y Apellido*</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                </div>
                <div>
                    <label for="cedula">Número de Cédula*</label>
                    <input type="text" id="cedula" name="cedula" value="{{ old('cedula') }}" required>
                </div>
                <div>
                    <label for="celular">Celular*</label>
                    <input type="text" id="celular" name="celular" value="{{ old('celular') }}" required>
                </div>
                <div>
                    <label for="email">Correo Electrónico*</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <button type="submit" class="btn-principal" style="margin-top:4px">✅ Guardar</button>
            </form>
        </div>

        <canvas id="canvas"></canvas>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        let capturedDataUrl = null;
        let humanaScoreVal  = null;
        let anguloVal       = null;
        let stream          = null;

        const pasoCamara   = document.getElementById('paso-camara');
        const pasoAnalisis = document.getElementById('paso-analisis');
        const pasoForm     = document.getElementById('paso-formulario');

        const video              = document.getElementById('video');
        const canvas             = document.getElementById('canvas');
        const imgPrevAnalisis    = document.getElementById('img-prev-analisis');
        const imgPrevForm        = document.getElementById('img-prev-form');
        const analisisLoading    = document.getElementById('analisis-loading');
        const analisisResultado  = document.getElementById('analisis-resultado');
        const tituloAnalisis     = document.getElementById('titulo-analisis');
        const resHumana          = document.getElementById('res-humana');
        const resAngulo          = document.getElementById('res-angulo');
        const barraHumana        = document.getElementById('barra-humana');
        const inputImagenB64     = document.getElementById('input-imagen-b64');
        const inputHumanaScore   = document.getElementById('input-humana-score');
        const inputAnguloMenique = document.getElementById('input-angulo-menique');

        // ── Cámara ──────────────────────────────────────────────
        async function iniciarCamara() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                video.srcObject = stream;
            } catch (e) {
                alert('No se pudo acceder a la cámara: ' + e.message);
            }
        }
        iniciarCamara();

        // ── Capturar ─────────────────────────────────────────────
        document.getElementById('btn-capturar').addEventListener('click', async () => {
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            capturedDataUrl = canvas.toDataURL('image/jpeg', 0.9);

            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }

            // Mostrar paso análisis con loading
            pasoCamara.style.display   = 'none';
            pasoAnalisis.style.display = 'flex';
            imgPrevAnalisis.src        = capturedDataUrl;
            tituloAnalisis.textContent = 'Analizando tu foto…';
            analisisLoading.style.display   = 'flex';
            analisisResultado.style.display = 'none';

            // Llamada AJAX a /analizar
            try {
                const resp = await fetch('{{ route("analizar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ imagen: capturedDataUrl }),
                });
                const data = await resp.json();
                humanaScoreVal = data.humana_score;
                anguloVal      = data.angulo_menique;
            } catch (e) {
                humanaScoreVal = null;
                anguloVal      = null;
            }

            // Mostrar resultados
            tituloAnalisis.textContent = '¡Resultados!';
            resHumana.textContent = humanaScoreVal !== null ? humanaScoreVal : '—';
            resAngulo.textContent = anguloVal      !== null ? anguloVal      : '—';
            barraHumana.style.width = (humanaScoreVal !== null ? humanaScoreVal : 0) + '%';

            analisisLoading.style.display   = 'none';
            analisisResultado.style.display = 'block';
        });

        // ── Continuar al formulario ───────────────────────────────
        document.getElementById('btn-continuar').addEventListener('click', () => {
            inputImagenB64.value     = capturedDataUrl;
            inputHumanaScore.value   = humanaScoreVal !== null ? humanaScoreVal : '';
            inputAnguloMenique.value = anguloVal      !== null ? anguloVal      : '';
            imgPrevForm.src          = capturedDataUrl;

            pasoAnalisis.style.display = 'none';
            pasoForm.style.display     = 'flex';
        });

        // ── Volver de análisis a cámara ───────────────────────────
        document.getElementById('btn-reintentar-analisis').addEventListener('click', () => {
            pasoAnalisis.style.display = 'none';
            pasoCamara.style.display   = 'flex';
            capturedDataUrl = null;
            iniciarCamara();
        });

        // ── Volver del formulario a análisis ─────────────────────
        document.getElementById('btn-reintentar-form').addEventListener('click', () => {
            pasoForm.style.display     = 'none';
            pasoAnalisis.style.display = 'flex';
        });

        // ── Restaurar si hay errores de validación ────────────────
        @if ($errors->any())
        (function () {
            const imgGuardada = @json(old('imagen', ''));
            if (imgGuardada) {
                capturedDataUrl          = imgGuardada;
                inputImagenB64.value     = imgGuardada;
                inputHumanaScore.value   = @json(old('humana_score', ''));
                inputAnguloMenique.value = @json(old('angulo_menique', ''));
                imgPrevForm.src          = imgGuardada;
                pasoCamara.style.display   = 'none';
                pasoForm.style.display     = 'flex';
            }
        })();
        @endif
    </script>
</body>
</html>
    <style>
        .carga-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
            gap: 20px;
        }
        .titulo-carga { font-size: 1.3rem; font-weight: bold; color: #fff; text-align: center; }

        /* --- Cámara --- */
        #paso-camara { display: flex; flex-direction: column; align-items: center; gap: 12px; width: 100%; max-width: 400px; }
        .camera-container {
            position: relative;
            width: 100%;
            max-width: 360px;
            border-radius: 12px;
            overflow: hidden;
            background: #000;
        }
        #video {
            width: 100%;
            display: block;
            border-radius: 12px;
        }
        #overlay-mano {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            opacity: 0.55;
            pointer-events: none;
        }
        #btn-capturar {
            padding: 12px 32px;
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            max-width: 360px;
        }
        #btn-capturar:hover { background: #c0392b; }

        /* --- Formulario con preview --- */
        #paso-formulario { display: none; flex-direction: column; align-items: center; gap: 14px; width: 100%; max-width: 400px; }
        #img-preview {
            width: 100%;
            max-height: 280px;
            object-fit: contain;
            border-radius: 12px;
            border: 2px solid #ccc;
        }
        #btn-reintentar {
            background: none;
            border: none;
            color: #2a8fd8;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: underline;
        }
        .form-datos {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .form-datos label {
            font-size: 0.85rem;
            color: #cde;
            margin-bottom: 2px;
            display: block;
        }
        .form-datos input[type="text"],
        .form-datos input[type="email"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #2a8fd8;
            font-size: 1rem;
            background: #fff;
            box-sizing: border-box;
        }
        #btn-guardar {
            padding: 14px 36px;
            background: #27ae60;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        #btn-guardar:hover { background: #219150; }
        #canvas { display: none; }
    </style>
</head>
<body class="carga-body">
    <div class="carga-wrap">

        {{-- Paso 1: Cámara --}}
        <div id="paso-camara">
            <p class="titulo-carga">Tomá una foto de tu mano</p>
            <div class="camera-container">
                <video id="video" autoplay playsinline muted></video>
                <img id="overlay-mano" src="{{ asset('mano.jpeg') }}" alt="">
            </div>
            <button id="btn-capturar">⚪ Capturar</button>
        </div>

        {{-- Paso 2: Preview + Formulario --}}
        <div id="paso-formulario">
            <p class="titulo-carga">¡Listo! Completá tus datos</p>
            <img id="img-preview" src="" alt="Vista previa">
            <button id="btn-reintentar" type="button">← Volver a tomar</button>

            <form id="form-datos" class="form-datos" method="POST" action="{{ route('carga.store') }}">
                @csrf
                <input type="hidden" name="tipo" value="camara">
                <input type="hidden" id="input-imagen-b64" name="imagen" value="">

                @if ($errors->any())
                    <div style="background:#fdd;border-radius:8px;padding:10px;color:#900;">
                        <ul style="margin:0;padding-left:16px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="nombre">Nombre y Apellido*</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                </div>
                <div>
                    <label for="cedula">Número de Cédula*</label>
                    <input type="text" id="cedula" name="cedula" value="{{ old('cedula') }}" required>
                </div>
                <div>
                    <label for="celular">Celular*</label>
                    <input type="text" id="celular" name="celular" value="{{ old('celular') }}" required>
                </div>
                <div>
                    <label for="email">Correo Electrónico*</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <button type="submit" id="btn-guardar">✅ Guardar</button>
            </form>
        </div>

        <canvas id="canvas"></canvas>
    </div>

    <script>
        const pasoCamara     = document.getElementById('paso-camara');
        const pasoFormulario = document.getElementById('paso-formulario');
        const video          = document.getElementById('video');
        const canvas         = document.getElementById('canvas');
        const imgPreview     = document.getElementById('img-preview');
        const inputB64       = document.getElementById('input-imagen-b64');
        let stream           = null;

        // Auto-arrancar cámara al cargar
        async function iniciarCamara() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                video.srcObject = stream;
            } catch (e) {
                alert('No se pudo acceder a la cámara: ' + e.message);
            }
        }
        iniciarCamara();

        // Capturar foto
        document.getElementById('btn-capturar').addEventListener('click', () => {
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

            // Detener cámara
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }

            // Mostrar preview y formulario
            imgPreview.src   = dataUrl;
            inputB64.value   = dataUrl;
            pasoCamara.style.display     = 'none';
            pasoFormulario.style.display = 'flex';
        });

        // Volver a tomar
        document.getElementById('btn-reintentar').addEventListener('click', () => {
            pasoFormulario.style.display = 'none';
            pasoCamara.style.display     = 'flex';
            imgPreview.src  = '';
            inputB64.value  = '';
            iniciarCamara();
        });

        @if ($errors->any())
        // Si hay errores de validación, restaurar la imagen capturada previamente
        const imgGuardada = '{{ old('imagen') }}';
        if (imgGuardada) {
            imgPreview.src  = imgGuardada;
            inputB64.value  = imgGuardada;
            pasoCamara.style.display     = 'none';
            pasoFormulario.style.display = 'flex';
        }
        @endif
    </script>
</body>
</html>

