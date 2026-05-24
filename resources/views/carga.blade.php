@extends('layouts.web')

@section('content')
  <!-- ═══════════════════════════════ HERO ═══════════════════════════════ -->
  <section class="hero" style="min-height: initial;">
    <div class="container-fluid h-100 px-0">
      <div class="row g-0 align-items-center">

        <!-- LEFT – hand photo -->
        <div class="col-6 position-relative"></div>

        <!-- RIGHT – logo + tagline -->
        <div class="col-6 d-flex flex-column justify-content-center align-items-end pe-4 pe-md-5">

          <!-- Logo -->
          <div class="logo-block text-end mb-3">
            <div class="d-flex align-items-center justify-content-end gap-2">
              <img src="{{ asset('/img/logo.png') }}" alt="Puño" class="logo">
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
  
  <div class="carga-body">
    <div class="carga-outer">

        {{-- ── PASO 1: Cámara ── --}}
        <div id="paso-camara" style="display:flex">
            <div class="camera-container">
                <video id="video" autoplay playsinline muted></video>
                <img id="guia-mano" src="{{ asset('img/mano.png') }}" alt="Guía de mano">
            </div>
            <button class="btn-cta">
              <span class="cta-arrows">»</span>
              &nbsp;Capturar&nbsp;
              <span class="cta-arrows">»</span>
            </button>
            @if(request()->query('web') == '1')
            <div id="dev-upload-wrap">
                <label>DEV — subir imagen</label>
                <input type="file" id="dev-upload" accept="image/*">
            </div>
            @endif
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
            <input type="hidden" id="fi-imagen-path" name="imagen_path">
            <input type="hidden" id="fi-humana"       name="humana_score">
            <input type="hidden" id="fi-angulo"       name="angulo_menique">
        </form>

    </div>

    <canvas id="canvas"></canvas>
  </div>

  <script>
      const CSRF = document.querySelector('meta[name="csrf-token"]').content;

      let capturedDataUrl  = null;
      let stream           = null;
      let aiDone           = false;
      let aiScore          = null;
      let aiAngulo         = null;
      let aiSoloMenique    = null;
      let aiImagenTemp     = null;   // ruta temporal en storage (del paso 1)
      let aiImagenPath     = null;   // ruta definitiva anotada (del paso 2)
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
          document.getElementById('fi-imagen-path').value = aiImagenPath || aiImagenTemp || '';
          document.getElementById('fi-humana').value      = aiScore  !== null ? aiScore  : '';
          document.getElementById('fi-angulo').value      = aiAngulo !== null ? aiAngulo : '';
          document.getElementById('form-resultados').submit();
      }

      function resetCaptura() {
          capturedDataUrl = null;
          aiDone          = false;
          aiScore         = null;
          aiAngulo        = null;
          aiSoloMenique   = null;
          aiImagenTemp    = null;
          aiImagenPath    = null;
          currentPct      = 0;
          setBar(0);
          pasoAnalisis.style.display = 'none';
          pasoCamara.style.display   = 'flex';
          iniciarCamara();
      }

      // Redimensiona la imagen a max 1024px para evitar payloads enormes
      function resizeDataUrl(dataUrl, maxPx, quality) {
          return new Promise(resolve => {
              const img = new Image();
              img.onload = () => {
                  let w = img.width, h = img.height;
                  if (w > maxPx || h > maxPx) {
                      if (w >= h) { h = Math.round(h * maxPx / w); w = maxPx; }
                      else        { w = Math.round(w * maxPx / h); h = maxPx; }
                  }
                  const c = document.createElement('canvas');
                  c.width = w; c.height = h;
                  c.getContext('2d').drawImage(img, 0, 0, w, h);
                  resolve(c.toDataURL('image/jpeg', quality));
              };
              img.src = dataUrl;
          });
      }

      async function procesarCaptura(dataUrl) {
          // Comprimir antes de enviar para evitar 413
          capturedDataUrl = await resizeDataUrl(dataUrl, 1024, 0.85);

          pasoCamara.style.display   = 'none';
          pasoAnalisis.style.display = 'flex';
          scanFoto.src = capturedDataUrl;
          startProgress();

          // ── PASO 1: análisis (GPT-4o) ──────────────────────────────────────
          try {
              const resp = await fetch('{{ route("analizar") }}', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                  body: JSON.stringify({ imagen: capturedDataUrl }),
              });
              const data = await resp.json();
              aiScore       = data.humana_score   ?? null;
              aiAngulo      = data.angulo_menique ?? null;
              aiSoloMenique = data.solo_menique   ?? null;
              aiImagenTemp  = data.imagen_temp    ?? null;
          } catch (e) {
              aiScore = aiAngulo = aiSoloMenique = aiImagenTemp = null;
          }

          // Validar: solo el meñique extendido
          if (aiSoloMenique === false) {
              clearInterval(progressInterval); progressInterval = null;
              setTimeout(() => {
                  alert('La foto debe ser con un puño y solo el dedo meñique extendido. Por favor intentá de nuevo.');
                  resetCaptura();
              }, 500);
              return;
          }

          // Validar: mano real
          if (aiScore !== null && aiScore < 40) {
              clearInterval(progressInterval); progressInterval = null;
              setTimeout(() => {
                  alert('Necesitamos una foto más nítida de tu mano real. Por favor intentá de nuevo en buena iluminación 📸');
                  resetCaptura();
              }, 500);
              return;
          }

          // ── PASO 2: generar imagen anotada (gpt-image-1) ───────────────────
          if (aiImagenTemp && aiAngulo !== null) {
              try {
                  const resp2 = await fetch('{{ route("generar.anotada") }}', {
                      method: 'POST',
                      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                      body: JSON.stringify({ imagen_temp: aiImagenTemp, angulo_menique: aiAngulo }),
                  });
                  const data2 = await resp2.json();
                  aiImagenPath = data2.imagen_path ?? null;
              } catch (e) {
                  aiImagenPath = null;
              }
          }

          aiDone = true;
      }

      document.getElementById('btn-capturar').addEventListener('click', async () => {
          canvas.width  = videoEl.videoWidth;
          canvas.height = videoEl.videoHeight;
          canvas.getContext('2d').drawImage(videoEl, 0, 0);
          const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

          if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }

          await procesarCaptura(dataUrl);
      });

      const devUpload = document.getElementById('dev-upload');
      if (devUpload) {
          devUpload.addEventListener('change', async () => {
              const file = devUpload.files[0];
              if (!file) return;
              const reader = new FileReader();
              reader.onload = async (e) => {
                  if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
                  await procesarCaptura(e.target.result);
              };
              reader.readAsDataURL(file);
          });
      }
  </script>
@endsection