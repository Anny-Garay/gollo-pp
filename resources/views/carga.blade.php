@extends('layouts.web')

@section('content')
  <!-- ═══════════════════════════════ HERO ═══════════════════════════════ -->
  <section class="hero" style="min-height: initial;">
    <div class="container-fluid h-100 px-0">

        <!-- Logo -->
        <div class="logo-block text-center mb-3">
        <img src="{{ asset('/img/logo.png') }}" alt="Puño" class="logo">
        </div>

    </div>
  </section>
  
  <div class="carga-body">
    <div class="carga-outer">

        {{-- ── PASO 1: Cámara ── --}}
        <div id="paso-camara" style="display:flex">
            <h4 class="text-center">Para escanear tu dedo, colocá tu mano según la figura en la imagen y capturá la foto para recibir tu descuento.</h4>

            {{-- Alert CSS Danger para errores de captura/cámara --}}
            <div id="scan-alert-box" class="alert alert-danger shadow-sm" role="alert" style="display: none; width: 100%; max-width: 540px; border-radius: 14px; border: 2px solid #dc3545; background-color: #fff5f5; color: #842029; padding: 14px 18px; margin: 10px 0 16px;">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="font-size: 24px; line-height: 1; flex-shrink: 0;">⚠️</div>
                    <div style="flex-grow: 1;">
                        <strong style="font-size: 15px; font-weight: 800; display: block; margin-bottom: 3px; color: #b02a37;">Atención</strong>
                        <div id="scan-alert-msg" style="font-size: 14px; line-height: 1.4; color: #58151c; font-weight: 600;"></div>
                    </div>
                    <button type="button" class="btn-close" aria-label="Cerrar" onclick="ocultarError()" style="flex-shrink: 0;"></button>
                </div>
            </div>

            <div class="camera-container">
                <video id="video" autoplay playsinline muted></video>
                <img id="guia-mano" src="{{ asset('img/scan/Recurso_6-2.png') }}" alt="Guía de mano">
            </div>
            <div class="manos">
              <button class="btn-cta" id="btn-mano-izquierda">
                Mano Izquierda
              </button>
              <button class="btn-cta" id="btn-mano-derecha">
                Mano Derecha
              </button>
            </div>
            <button class="btn-cta" id="btn-capturar">
              ¡Captura la foto!
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
            <div class="scan-module">

                <!-- fondo -->
                <img class="scan-grid" src="{{ asset('img/scan/Recurso_4.png') }}" alt="">
                <div class="scan-vignette"></div>

                <!-- marcos -->
                <img class="frame-top"    src="{{ asset('img/scan/Recurso_2.png') }}" alt="">
                <img class="frame-bottom" src="{{ asset('img/scan/Recurso_3.png') }}" alt="">

                <!-- badge -->
                <div class="scan-header">
                    <span class="scan-dash"></span>
                    <span class="scan-badge">Escaneando ...</span>
                    <span class="scan-dash"></span>
                </div>

                <!-- 3 loaders superiores -->
                <div class="loader-row top">
                    <img class="loader" src="{{ asset('img/scan/Recurso_13.png') }}" alt="">
                    <img class="loader" src="{{ asset('img/scan/Recurso_13.png') }}" alt="">
                    <img class="loader" src="{{ asset('img/scan/Recurso_13.png') }}" alt="">
                </div>

                <!-- zona central: foto capturada + línea de escaneo + puntos -->
                <div class="scan-stage">
                    <img id="scan-foto" src="" alt="" class="scan-hand">
                    <img class="scan-line" src="{{ asset('img/scan/Recurso_16.png') }}" alt="">
                    <div class="scan-dot"></div>
                    <div class="scan-dot"></div>
                    <div class="scan-dot"></div>
                    <div class="scan-dot"></div>
                    <div class="scan-dot"></div>
                </div>

                <!-- footer: barras + loaders + track + progreso funcional -->
                <div class="scan-footer">
                    <img class="bars bars-left" src="{{ asset('img/scan/Recurso_8.png') }}" alt="">

                    <div class="footer-center">
                        <div class="footer-loaders">
                            <div class="loader-group">
                                <img class="loader" src="{{ asset('img/scan/Recurso_13.png') }}" alt="">
                                <img class="loader" src="{{ asset('img/scan/Recurso_13.png') }}" alt="">
                                <img class="loader" src="{{ asset('img/scan/Recurso_13.png') }}" alt="">
                            </div>
                            <div class="hazard-track"></div>
                            <div class="loader-group">
                                <img class="loader" src="{{ asset('img/scan/Recurso_13.png') }}" alt="">
                                <img class="loader" src="{{ asset('img/scan/Recurso_13.png') }}" alt="">
                                <img class="loader" src="{{ asset('img/scan/Recurso_13.png') }}" alt="">
                            </div>
                        </div>

                        <div class="scan-progress-module">
                            <div class="scan-bar-labels">
                                <span class="scan-bar-brand">Pinky Promos AI v2.1</span>
                                <span class="scan-bar-pct" id="scan-pct">0%</span>
                            </div>
                            <div class="scan-bar-track">
                                <div class="scan-bar-fill" id="scan-bar"></div>
                            </div>
                        </div>
                    </div>

                    <img class="bars bars-right" src="{{ asset('img/scan/Recurso_10.png') }}" alt="">
                </div>

                <img class="base-line" src="{{ asset('img/scan/Recurso_16.png') }}" alt="">

            </div>
        </div>

        {{-- ── Form oculto → GET /resultado ── --}}
        <form id="form-resultados" method="GET" action="{{ route('resultado') }}">
            <input type="hidden" id="fi-imagen-temp" name="imagen_temp">
            <input type="hidden" id="fi-humana"       name="humana_score">
            <input type="hidden" id="fi-angulo"       name="angulo_menique">
            <input type="hidden" id="fi-pinky-points" name="pinky_points">
        </form>

    </div>

    <canvas id="canvas"></canvas>
  </div>

  <script>
      const guiaMano = document.getElementById('guia-mano');

      document.getElementById('btn-mano-izquierda').addEventListener('click', () => {
        guiaMano.style.transform = '';
      });

      document.getElementById('btn-mano-derecha').addEventListener('click', () => {
        guiaMano.style.transform = 'scaleX(-1)';
      });

      const CSRF = document.querySelector('meta[name="csrf-token"]').content;

      let capturedDataUrl  = null;
      let stream           = null;
      let aiDone           = false;
      let aiScore          = null;
      let aiAngulo         = null;
      let aiSoloMenique    = null;
      let aiPinkyPoints    = null;
      let aiImagenTemp     = null;   // ruta temporal en storage (del paso 1)
      let currentPct       = 0;
      let progressInterval = null;

      const pasoCamara   = document.getElementById('paso-camara');
      const pasoAnalisis = document.getElementById('paso-analisis');
      const videoEl      = document.getElementById('video');
      const canvas       = document.getElementById('canvas');
      const scanFoto     = document.getElementById('scan-foto');
      const scanBar      = document.getElementById('scan-bar');
      const scanPct      = document.getElementById('scan-pct');

      function mostrarError(mensaje) {
          const alertBox = document.getElementById('scan-alert-box');
          const alertMsg = document.getElementById('scan-alert-msg');
          if (alertBox && alertMsg) {
              alertMsg.textContent = mensaje;
              alertBox.style.display = 'block';
              alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
      }

      function ocultarError() {
          const alertBox = document.getElementById('scan-alert-box');
          if (alertBox) {
              alertBox.style.display = 'none';
          }
      }

      async function iniciarCamara() {
          try {
              stream = await navigator.mediaDevices.getUserMedia({
                  video: { facingMode: 'environment' },
                  audio: false
              });
              videoEl.srcObject = stream;
              ocultarError();
          } catch (e) {
              let msg = 'No se pudo acceder a la cámara: ' + e.message;
              if (e.name === 'NotAllowedError' || e.name === 'PermissionDeniedError') {
                  msg = 'No se pudo acceder a la cámara. Por favor asegurate de autorizar los permisos de cámara en tu navegador para continuar.';
              }
              mostrarError(msg);
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
          const params = new URLSearchParams({
              imagen_temp: aiImagenTemp || '',
              humana_score: aiScore !== null ? aiScore : '',
              angulo_menique: aiAngulo !== null ? aiAngulo : '',
              pinky_points: aiPinkyPoints ? JSON.stringify(aiPinkyPoints) : ''
          });
          window.location.href = '{{ route("resultado") }}?' + params.toString();
      }

      function resetCaptura() {
          capturedDataUrl = null;
          aiDone          = false;
          aiScore         = null;
          aiAngulo        = null;
          aiSoloMenique   = null;
          aiPinkyPoints   = null;
          aiImagenTemp    = null;
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
          ocultarError();
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
              if (!resp.ok) {
                  throw new Error('Error en el servidor (' + resp.status + ')');
              }
              const data = await resp.json();
              aiScore       = data.humana_score   ?? null;
              aiAngulo      = data.angulo_menique ?? null;
              aiSoloMenique = data.solo_menique   ?? null;
              aiPinkyPoints = data.pinky_points   ?? null;
              aiImagenTemp  = data.imagen_temp    ?? null;
          } catch (e) {
              clearInterval(progressInterval);
              progressInterval = null;
              setTimeout(() => {
                  resetCaptura();
                  mostrarError('Ocurrió un problema de conexión al analizar la foto. Por favor intentá de nuevo.');
              }, 400);
              return;
          }

          // Validar: solo el meñique extendido
          if (aiSoloMenique === false) {
              clearInterval(progressInterval);
              progressInterval = null;
              setTimeout(() => {
                  resetCaptura();
                  mostrarError('La foto debe ser con un puño cerrado y únicamente el dedo meñique extendido. Por favor intentá de nuevo.');
              }, 400);
              return;
          }

          // Validar: mano real
          if (aiScore !== null && aiScore < 40) {
              clearInterval(progressInterval);
              progressInterval = null;
              setTimeout(() => {
                  resetCaptura();
                  mostrarError('Necesitamos una foto más nítida de tu mano real. Por favor intentá de nuevo con buena iluminación.');
              }, 400);
              return;
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