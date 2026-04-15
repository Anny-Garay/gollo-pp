<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Escaneando - Phone Pinky</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        .carga-outer {
            width: 100%;
            max-width: 430px;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
        }
        #paso-camara {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 28px 20px 16px;
            gap: 14px;
            align-items: center;
        }
        .camara-titulo {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            text-align: center;
            margin: 0;
        }
        .camera-container {
            position: relative;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            background: #000;
            flex: 1;
            max-height: 54vh;
        }
        #video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        #btn-capturar {
            width: 100%;
            padding: 16px;
            background: #e31837;
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 1.05rem;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.1s;
        }
        #btn-capturar:active { transform: scale(0.97); }

        /* Análisis */
        #paso-analisis {
            display: none;
            flex-direction: column;
            flex: 1;
            position: relative;
            overflow: hidden;
            min-height: 100dvh;
        }
        .scan-foto-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.5);
        }
        .scan-overlay {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 100dvh;
        }
        .scan-badge-wrap {
            display: flex;
            justify-content: center;
            padding-top: 22px;
        }
        .scan-badge {
            background: rgba(0,180,255,0.15);
            border: 1.5px solid #00c8ff;
            color: #00d8ff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2.5px;
            padding: 7px 22px;
            border-radius: 50px;
        }
        .scan-frame {
            position: absolute;
            top: 16px;
            left: 16px;
            right: 16px;
            bottom: 196px;
            z-index: 3;
            pointer-events: none;
        }
        .scan-frame::before,
        .scan-frame::after,
        .scan-frame .cbr,
        .scan-frame .cbl {
            content: '';
            position: absolute;
            width: 26px;
            height: 26px;
            border-color: #00c8ff;
            border-style: solid;
        }
        .scan-frame::before { top: 44px;  left: 0;  border-width: 2px 0 0 2px; }
        .scan-frame::after  { top: 44px;  right: 0; border-width: 2px 2px 0 0; }
        .scan-frame .cbr    { bottom: 0;  right: 0; border-width: 0 2px 2px 0; }
        .scan-frame .cbl    { bottom: 0;  left: 0;  border-width: 0 0 2px 2px; }
        .scan-line {
            position: absolute;
            left: 16px;
            right: 16px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00c8ff 40%, #00c8ff 60%, transparent);
            z-index: 4;
            top: 66px;
            animation: scanAnim 2.8s ease-in-out infinite;
        }
        @keyframes scanAnim {
            0%   { transform: translateY(0);    opacity: 1; }
            75%  { transform: translateY(55vh); opacity: 1; }
            90%  { transform: translateY(55vh); opacity: 0; }
            100% { transform: translateY(0);    opacity: 0; }
        }
        .scan-cursor {
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 3px;
            background: #00c8ff;
            z-index: 4;
            right: 20px;
            top: 45%;
            animation: cursorAnim 2.5s ease-in-out infinite;
        }
        @keyframes cursorAnim {
            0%   { transform: translateY(-50px); opacity: 0.9; }
            50%  { transform: translateY(50px);  opacity: 1;   }
            100% { transform: translateY(-50px); opacity: 0.9; }
        }
        .scan-dot {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #e31837;
            z-index: 4;
            opacity: 0;
            animation: popDot 4s ease-in-out infinite;
        }
        .scan-dot:nth-child(1) { top:22%; left:32%; animation-delay:0.6s; }
        .scan-dot:nth-child(2) { top:38%; left:62%; animation-delay:1.3s; }
        .scan-dot:nth-child(3) { top:50%; left:28%; animation-delay:2.1s; }
        .scan-dot:nth-child(4) { top:28%; left:72%; animation-delay:1.7s; }
        .scan-dot:nth-child(5) { top:44%; left:52%; animation-delay:0.9s; }
        @keyframes popDot {
            0%,20% { opacity:0; transform:scale(0);   }
            32%    { opacity:1; transform:scale(1.2); }
            70%    { opacity:1; transform:scale(1);   }
            88%    { opacity:0; transform:scale(0.5); }
            100%   { opacity:0; }
        }
        .scan-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(6,16,36,0.93);
            border-radius: 22px 22px 0 0;
            padding: 18px 20px 10px;
            z-index: 5;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .scan-status { display:flex; align-items:center; gap:9px; }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #00b4ff;
            flex-shrink: 0;
            animation: blink 1.1s ease-in-out infinite;
        }
        @keyframes blink {
            0%,100% { opacity:1; }
            50%     { opacity:0.35; }
        }
        .scan-status span { color:#fff; font-size:0.95rem; font-weight:600; }
        .scan-bar-wrap { display:flex; flex-direction:column; gap:5px; }
        .scan-bar-labels { display:flex; justify-content:space-between; align-items:center; }
        .scan-bar-brand { color:rgba(255,255,255,0.45); font-size:10px; }
        .scan-bar-pct   { color:#00c8ff; font-size:11px; font-weight:700; }
        .scan-bar-track {
            height: 5px;
            background: rgba(255,255,255,0.12);
            border-radius: 99px;
            overflow: hidden;
        }
        .scan-bar-fill {
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, #00aaff, #e31837);
            width: 0%;
            transition: width 0.35s ease;
        }
        .scan-msg {
            text-align: center;
            color: #fff;
            font-size: 0.94rem;
            font-weight: 700;
            margin: 4px 0 2px;
            line-height: 1.4;
        }
        .step-dots {
            display: flex;
            gap: 7px;
            justify-content: center;
            padding: 8px 0 6px;
        }
        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.28);
        }
        .step-dot--active {
            background: #e31837;
            width: 20px;
            border-radius: 4px;
        }
        #canvas { display:none; }
        #form-resultados { display:none; }
    </style>
</head>
<body class="carga-body">
<div class="carga-outer">

    {{-- ── PASO 1: Cámara ── --}}
    <div id="paso-camara" style="display:flex">
        <p class="camara-titulo">Tomá una foto de tu mano</p>
        <div class="camera-container">
            <video id="video" autoplay playsinline muted></video>
        </div>
        <button id="btn-capturar">⚪ Capturar</button>
        <div class="step-dots">
            <span class="step-dot"></span>
            <span class="step-dot step-dot--active"></span>
            <span class="step-dot"></span>
            <span class="step-dot"></span>
            <span class="step-dot"></span>
        </div>
    </div>

    {{-- ── PASO 2: Animación análisis ── --}}
    <div id="paso-analisis">
        <img id="scan-foto" src="" alt="" class="scan-foto-bg">
        <div class="scan-overlay">
            <div class="scan-badge-wrap">
                <span class="scan-badge">IA ESCANEANDO</span>
            </div>
            <div class="scan-frame">
                <span class="cbr"></span>
                <span class="cbl"></span>
            </div>
            <div class="scan-line"></div>
            <div class="scan-cursor"></div>
            <div class="scan-dot"></div>
            <div class="scan-dot"></div>
            <div class="scan-dot"></div>
            <div class="scan-dot"></div>
            <div class="scan-dot"></div>
            <div class="scan-bottom">
                <div class="scan-status">
                    <span class="status-dot"></span>
                    <span>Detectando meñique...</span>
                </div>
                <div class="scan-bar-wrap">
                    <div class="scan-bar-labels">
                        <span class="scan-bar-brand">Phone Pinky™ AI v2.1</span>
                        <span class="scan-bar-pct" id="scan-pct">0%</span>
                    </div>
                    <div class="scan-bar-track">
                        <div class="scan-bar-fill" id="scan-bar"></div>
                    </div>
                </div>
                <p class="scan-msg">Aguantá un toque, la IA está<br>midiendo tu dedo...</p>
                <div class="step-dots">
                    <span class="step-dot"></span>
                    <span class="step-dot step-dot--active"></span>
                    <span class="step-dot"></span>
                    <span class="step-dot"></span>
                    <span class="step-dot"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Form oculto → POST /resultados ── --}}
    <form id="form-resultados" method="POST" action="{{ route('resultados.store') }}">
        @csrf
        <input type="hidden" id="fi-imagen"  name="imagen">
        <input type="hidden" id="fi-humana"  name="humana_score">
        <input type="hidden" id="fi-angulo"  name="angulo_menique">
    </form>

</div>

<canvas id="canvas"></canvas>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    let capturedDataUrl  = null;
    let stream           = null;
    let aiDone           = false;
    let aiScore          = null;
    let aiAngulo         = null;
    let currentPct       = 0;
    let progressInterval = null;

    const pasoCamara   = document.getElementById('paso-camara');
    const pasoAnalisis = document.getElementById('paso-analisis');
    const videoEl      = document.getElementById('video');
    const canvas       = document.getElementById('canvas');
    const scanFoto     = document.getElementById('scan-foto');
    const scanBar      = document.getElementById('scan-bar');
    const scanPct      = document.getElementById('scan-pct');

    async function iniciarCamara() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' },
                audio: false
            });
            videoEl.srcObject = stream;
        } catch (e) {
            alert('No se pudo acceder a la cámara: ' + e.message);
        }
    }
    iniciarCamara();

    function setBar(pct) {
        const p = Math.round(pct);
        scanBar.style.width = p + '%';
        scanPct.textContent = p + '%';
    }

    function startProgress() {
        currentPct = 0;
        progressInterval = setInterval(() => {
            if (aiDone) {
                clearInterval(progressInterval);
                progressInterval = null;
                animateTo100();
                return;
            }
            if (currentPct < 85) {
                currentPct = Math.min(currentPct + (currentPct < 50 ? 1.4 : 0.45), 85);
                setBar(currentPct);
            }
        }, 200);
    }

    function animateTo100() {
        let v = currentPct;
        const go = setInterval(() => {
            v = Math.min(v + 2, 100);
            setBar(v);
            if (v >= 100) {
                clearInterval(go);
                setTimeout(submitResultados, 700);
            }
        }, 35);
    }

    function submitResultados() {
        document.getElementById('fi-imagen').value = capturedDataUrl;
        document.getElementById('fi-humana').value = aiScore  !== null ? aiScore  : '';
        document.getElementById('fi-angulo').value = aiAngulo !== null ? aiAngulo : '';
        document.getElementById('form-resultados').submit();
    }

    document.getElementById('btn-capturar').addEventListener('click', async () => {
        canvas.width  = videoEl.videoWidth;
        canvas.height = videoEl.videoHeight;
        canvas.getContext('2d').drawImage(videoEl, 0, 0);
        capturedDataUrl = canvas.toDataURL('image/jpeg', 0.9);

        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }

        pasoCamara.style.display   = 'none';
        pasoAnalisis.style.display = 'flex';
        scanFoto.src = capturedDataUrl;
        startProgress();

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
            aiScore  = data.humana_score   ?? null;
            aiAngulo = data.angulo_menique ?? null;
        } catch (e) {
            aiScore  = null;
            aiAngulo = null;
        }

        // Si la foto no parece una mano real, volvemos a la cámara
        if (aiScore !== null && aiScore < 40) {
            clearInterval(progressInterval);
            progressInterval = null;
            setTimeout(() => {
                alert('Necesitamos una foto más nítida de tu mano real. Por favor intentá de nuevo en buena iluminación 📸');
                pasoAnalisis.style.display = 'none';
                pasoCamara.style.display   = 'flex';
                capturedDataUrl = null;
                aiDone   = false;
                aiScore  = null;
                aiAngulo = null;
                currentPct = 0;
                setBar(0);
                iniciarCamara();
            }, 500);
            return;
        }

        aiDone = true;
    });
</script>
</body>
</html>
