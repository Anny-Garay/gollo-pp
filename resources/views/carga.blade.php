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
        .btn-opciones { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .btn-opcion {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            background: #0b3a6d;
            color: #fff;
            transition: background 0.2s;
        }
        .btn-opcion:hover { background: #2a8fd8; }
        .btn-opcion.activo { background: #2a8fd8; }

        /* --- Cámara --- */
        #seccion-camara { display: none; flex-direction: column; align-items: center; gap: 12px; width: 100%; max-width: 400px; }
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
        }
        #btn-capturar:hover { background: #c0392b; }

        /* --- Subir imagen --- */
        #seccion-upload { display: none; flex-direction: column; align-items: center; gap: 12px; width: 100%; max-width: 400px; }
        .zona-upload {
            width: 100%;
            border: 2px dashed #2a8fd8;
            border-radius: 12px;
            padding: 32px 16px;
            text-align: center;
            cursor: pointer;
            color: #2a8fd8;
            font-size: 0.95rem;
        }
        .zona-upload:hover { background: #eaf4fb; }

        /* --- Preview --- */
        #seccion-preview { display: none; flex-direction: column; align-items: center; gap: 14px; width: 100%; max-width: 400px; }
        #img-preview {
            width: 100%;
            max-height: 340px;
            object-fit: contain;
            border-radius: 12px;
            border: 2px solid #ccc;
        }
        #btn-procesar {
            padding: 14px 36px;
            background: #27ae60;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            max-width: 360px;
        }
        #btn-procesar:hover { background: #219150; }
        #btn-reintentar {
            background: none;
            border: none;
            color: #2a8fd8;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: underline;
        }
        #canvas { display: none; }
    </style>
</head>
<body class="carga-body">
    <div class="carga-wrap">
        <p class="titulo-carga">¿Cómo querés agregar tu foto?</p>

        <div class="btn-opciones">
            <button class="btn-opcion" id="btn-ir-camara">📷 Tomar foto</button>
            <button class="btn-opcion" id="btn-ir-upload">🖼️ Subir imagen</button>
        </div>

        {{-- Sección cámara --}}
        <div id="seccion-camara">
            <div class="camera-container">
                <video id="video" autoplay playsinline muted></video>
                <img id="overlay-mano" src="{{ asset('mano.png') }}" alt="">
            </div>
            <button id="btn-capturar">⚪ Capturar</button>
        </div>

        {{-- Sección subir imagen --}}
        <div id="seccion-upload">
            <div class="zona-upload" onclick="document.getElementById('input-archivo').click()">
                <p>Tocá acá para elegir una imagen</p>
                <p style="font-size:0.8em; color:#999; margin-top:4px;">JPG, PNG, WEBP — máx. 10MB</p>
            </div>
            <input type="file" id="input-archivo" accept="image/*" hidden>
        </div>

        {{-- Preview + procesar --}}
        <div id="seccion-preview">
            <img id="img-preview" src="" alt="Vista previa">
            <button id="btn-reintentar">← Volver a intentar</button>
        </div>

        {{-- Formulario oculto para envío --}}
        <form id="form-procesar" method="POST" action="{{ route('carga.store') }}" enctype="multipart/form-data" style="width:100%;max-width:360px;display:none;">
            @csrf
            <input type="hidden" id="input-tipo" name="tipo" value="">
            {{-- Para cámara: base64 --}}
            <input type="hidden" id="input-imagen-b64" name="imagen" value="">
            {{-- Para upload: file --}}
            <input type="file" id="input-imagen-file" name="imagen" accept="image/*" style="display:none;">
            <button type="submit" id="btn-procesar">✅ Procesar</button>
        </form>

        <canvas id="canvas"></canvas>
    </div>

    <script>
        // --- Estado ---
        let modo = null; // 'camara' | 'upload'
        let stream = null;
        let imagenBlob = null; // para upload

        const secCamara    = document.getElementById('seccion-camara');
        const secUpload    = document.getElementById('seccion-upload');
        const secPreview   = document.getElementById('seccion-preview');
        const formProcesar = document.getElementById('form-procesar');
        const video        = document.getElementById('video');
        const canvas       = document.getElementById('canvas');
        const imgPreview   = document.getElementById('img-preview');
        const inputTipo    = document.getElementById('input-tipo');
        const inputB64     = document.getElementById('input-imagen-b64');
        const inputFile    = document.getElementById('input-imagen-file');
        const btnIrCamara  = document.getElementById('btn-ir-camara');
        const btnIrUpload  = document.getElementById('btn-ir-upload');

        function resetSecciones() {
            secCamara.style.display    = 'none';
            secUpload.style.display    = 'none';
            secPreview.style.display   = 'none';
            formProcesar.style.display = 'none';
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
            btnIrCamara.classList.remove('activo');
            btnIrUpload.classList.remove('activo');
        }

        // --- Cámara ---
        btnIrCamara.addEventListener('click', async () => {
            resetSecciones();
            modo = 'camara';
            btnIrCamara.classList.add('activo');
            secCamara.style.display = 'flex';
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                video.srcObject = stream;
            } catch (e) {
                alert('No se pudo acceder a la cámara: ' + e.message);
            }
        });

        document.getElementById('btn-capturar').addEventListener('click', () => {
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            mostrarPreview(dataUrl);
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
            inputTipo.value  = 'camara';
            inputB64.value   = dataUrl;
        });

        // --- Upload ---
        btnIrUpload.addEventListener('click', () => {
            resetSecciones();
            modo = 'upload';
            btnIrUpload.classList.add('activo');
            secUpload.style.display = 'flex';
        });

        document.getElementById('input-archivo').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                mostrarPreview(e.target.result);
                // Pasamos el archivo al input oculto del form
                const dt = new DataTransfer();
                dt.items.add(file);
                inputFile.files = dt.files;
                inputB64.value  = ''; // no usar base64
                inputTipo.value = 'upload';
            };
            reader.readAsDataURL(file);
        });

        // --- Preview ---
        function mostrarPreview(src) {
            imgPreview.src = src;
            secCamara.style.display    = 'none';
            secUpload.style.display    = 'none';
            secPreview.style.display   = 'flex';
            formProcesar.style.display = 'block';
        }

        document.getElementById('btn-reintentar').addEventListener('click', () => {
            resetSecciones();
        });

        // --- Envío: si es upload, sincronizar el file al form ---
        formProcesar.addEventListener('submit', function (e) {
            if (inputTipo.value === 'upload' && inputFile.files.length === 0) {
                e.preventDefault();
                alert('No se encontró la imagen.');
            }
        });
    </script>
</body>
</html>

