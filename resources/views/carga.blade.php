<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <img id="overlay-mano" src="{{ asset('mano.png') }}" alt="">
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

