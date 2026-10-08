@php
    $anguloVal    = min(20.0, (float)($angulo_menique ?? $angulo ?? 0));
    $nivelNum     = $anguloVal <= 4 ? 1 : ($anguloVal <= 8 ? 2 : ($anguloVal <= 12 ? 3 : ($anguloVal <= 16 ? 4 : 5)));
    $nivelTitulos = [1 => 'Nivel 1 — Leve', 2 => 'Nivel 2 — Bajo', 3 => 'Nivel 3 — Moderado', 4 => 'Nivel 4 — Alto', 5 => 'Nivel 5 — Severo'];
    $nivelCompleto = $nivelTitulos[$nivelNum];
    $nivelParts = explode(' — ', $nivelCompleto);
    $primer_nombre = explode(' ', trim($nombre ?? ''))[0] ?? 'Sofía';
    $shareUrl     = 'https://www.gollo.com/test-pinky';
@endphp

  <div id="compartir">
  
     <div class="logo-slot">
       <!-- Logo del cliente -->
       <img src="{{ asset('/img/logo.png') }}" alt="Pinky Promos" crossOrigin="anonymous"
             onerror="this.replaceWith(Object.assign(document.createElement('div'),{className:'img-placeholder',innerText:'LOGO\n(logo.png)'}))">
     </div>
     <div style="clear:both;"></div>
  
     <div class="compartir-titular">
       <div class="titular-linea1" id="txt-nombre">Uff, {{ $primer_nombre }}</div>
       <div class="titular-linea2" id="txt-nivel-num">{{ $nivelParts[0] ?? 'Nivel 1' }}</div>
       <div class="titular-linea3" id="txt-nivel-desc">{{ $nivelParts[1] ?? 'Leve' }}</div>
     </div>
 
    <div class="compartir-media">
 
      <div class="mano-slot-wrap">
        <div class="mano-slot">
          <!-- Foto/ilustración de la mano o guante -->
          <img src="{{ asset('/img/mano2.png') }}" alt="" crossOrigin="anonymous" />
        </div>
      </div>
 
      <div class="sello-torcido">
        <svg viewBox="0 0 200 200">
          <polygon fill="var(--yellow)" points="
            100,4 112,24 134,12 138,36 162,32 158,56 182,60 170,80
            190,92 174,108 190,124 170,132 178,154 154,156 150,180
            128,170 116,192 100,174 84,192 72,170 50,180 46,156
            22,154 30,132 10,124 26,108 10,92 30,80 18,60 42,56
            38,32 62,36 66,12 88,24
          "/>
        </svg>
        <div class="sello-texto">
          <span class="sello-pct" id="txt-porcentaje">{{ round($anguloVal) }}%</span>
          <span class="sello-label">de torcido</span>
        </div>
      </div>
 
    </div>
 
    @if(!empty($cupon_codigo) || !empty($cupon_monto_texto))
    <!--<div style="background: rgba(0, 0, 0, 0.28); border: 2.5px dashed var(--yellow); border-radius: 12px; padding: 12px 14px; margin-top: 18px; text-align: center; color: #fff;">
      <div style="font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap;">
        <span>🎟️ CUPÓN:</span>
        <strong style="color: var(--yellow); font-size: 22px; font-family: monospace; letter-spacing: 1px;">{{ $cupon_codigo }}</strong>
        <span style="color: var(--yellow); font-size: 20px;">•</span>
        <span style="font-size: 19px; font-weight: 900; color: #ffffff;">{{ $cupon_monto_texto }}</span>
      </div>
    </div>-->
    @endif

    <div class="compartir-cta">
      <div class="cta-linea1">Ingresá a</div>
      <div class="cta-linea2">gollo.com/pinky</div>
      <div class="cta-linea3">y medí el tuyo</div>
    </div>
 
  </div>
 
  <!-- ══════════ BOTONES DE ACCIÓN ══════════ -->
  <div class="share-bar">
    <button class="share-btn download" id="btn-descargar-compartir">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v11.17l3.59-3.58L17 12l-5 5-5-5 1.41-1.41L11 14.17V3h1Zm7 16v2H5v-2h14Z"/></svg>
      Descargar imagen
    </button>
    <button class="share-btn share-native" id="btn-compartir-nativo">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 16c-.79 0-1.5.31-2.03.81l-7.55-4.42c.03-.13.05-.26.05-.39s-.02-.26-.05-.39l7.55-4.42c.53.5 1.24.81 2.03.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .13.02.26.05.39l-7.55 4.42C7.5 10.31 6.79 10 6 10c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.03-.81l7.55 4.42c-.03.13-.05.26-.05.39 0 1.66 1.34 3 3 3s3-1.34 3-3-1.34-3-3-3z"/></svg>
      Compartir
    </button>
  </div>

  <div class="share-status" id="share-status" style="text-align:center; font-size:13px; font-weight:700; color:#004DB5; margin-top:8px; min-height:20px;"></div>

  {{-- ══════════ MODAL PREVIEW DE IMAGEN (Para guardar en iframe) ══════════ --}}
  <div id="modal-img-preview" class="pinky-modal-backdrop" style="display:none;">
    <div class="pinky-modal-dialog">
      <div class="pinky-modal-header">
        <h5 style="margin:0; font-size:16px; font-weight:800; color:#1a1a2e;">✨ ¡Tu imagen está lista!</h5>
        <button type="button" class="pinky-modal-close" onclick="cerrarModalImagen()">✕</button>
      </div>
      <div class="pinky-modal-body" style="text-align:center; padding: 14px 10px;">
        <img id="img-preview-target" src="" alt="Phone Pinky" style="max-width:100%; max-height:48vh; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.15); display:inline-block; margin-bottom:12px;">
        <div style="background:#f0f7ff; border:1px solid #cce3ff; border-radius:10px; padding:10px 12px; margin-bottom:12px; font-size:13px; color:#004DB5; font-weight:600; line-height:1.4;">
          💡 <strong>Para guardar la imagen:</strong><br>
          En celular: <em>Mantené presionada la foto y elegí "Guardar imagen"</em>.<br>
          En PC: <em>Clic derecho y elegí "Guardar imagen como..."</em>
        </div>
        <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
          <a id="btn-modal-open-tab" href="#" target="_blank" class="pinky-btn-action" style="background:#004DB5; color:#fff; text-decoration:none; padding:10px 16px; border-radius:30px; font-size:13px; font-weight:800;">
            ↗️ Abrir en pestaña nueva
          </a>
          <button type="button" class="pinky-btn-action" onclick="cerrarModalImagen()" style="background:#e9ecef; color:#495057; border:none; padding:10px 16px; border-radius:30px; font-size:13px; font-weight:700; cursor:pointer;">
            Listo
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- ══════════ MODAL OPCIONES DE COMPARTIR ══════════ --}}
  <div id="modal-share-options" class="pinky-modal-backdrop" style="display:none;">
    <div class="pinky-modal-dialog">
      <div class="pinky-modal-header">
        <h5 style="margin:0; font-size:16px; font-weight:800; color:#1a1a2e;">📢 Compartir resultado</h5>
        <button type="button" class="pinky-modal-close" onclick="cerrarModalShare()">✕</button>
      </div>
      <div class="pinky-modal-body" style="padding: 16px 14px;">
        <p style="font-size:13px; color:#666; margin-bottom:14px; text-align:center;">
          Elegí cómo querés compartir tu diagnóstico de Phone Pinky:
        </p>

        <div style="display:flex; flex-direction:column; gap:10px;">
          {{-- WhatsApp --}}
          <a id="share-link-whatsapp" href="#" target="_blank" class="share-option-btn" style="background:#25D366; color:#fff; text-decoration:none; padding:12px 18px; border-radius:12px; font-weight:800; font-size:14px; display:flex; align-items:center; justify-content:center; gap:10px; box-shadow:0 3px 10px rgba(37,211,102,0.3);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm5.79 14.07c-.24.68-1.2 1.25-1.74 1.29-.49.03-1.12.06-3.64-.98-2.64-1.09-4.34-3.77-4.47-3.95-.13-.17-1.07-1.42-1.07-2.71 0-1.28.67-1.91.91-2.17.24-.26.52-.33.7-.33.17 0 .35 0 .5.01.16.01.37-.06.58.44.22.52.75 1.83.82 1.97.07.14.11.31.02.49-.09.18-.14.29-.28.45-.14.16-.29.36-.42.48-.14.14-.29.3-.12.59.16.29.73 1.2 1.56 1.94 1.07.95 1.97 1.24 2.26 1.38.29.14.45.12.62-.07.17-.19.72-.84.91-1.13.19-.29.38-.24.64-.15.26.1 1.64.77 1.92.91.28.14.47.21.54.33.07.12.07.7-.17 1.38z"/></svg>
            Compartir por WhatsApp
          </a>

          {{-- Facebook --}}
          <a id="share-link-facebook" href="#" target="_blank" class="share-option-btn" style="background:#1877F2; color:#fff; text-decoration:none; padding:12px 18px; border-radius:12px; font-weight:800; font-size:14px; display:flex; align-items:center; justify-content:center; gap:10px; box-shadow:0 3px 10px rgba(24,119,242,0.3);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            Publicar en Facebook
          </a>

          {{-- Copiar Enlace --}}
          <button type="button" id="btn-copiar-enlace" class="share-option-btn" style="background:#f1f3f5; color:#212529; border:1.5px solid #ced4da; padding:12px 18px; border-radius:12px; font-weight:800; font-size:14px; display:flex; align-items:center; justify-content:center; gap:10px; cursor:pointer;">
            📋 Copiar enlace de la promo
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    .pinky-modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.65);
      z-index: 100000;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
      backdrop-filter: blur(4px);
      animation: pinkyFadeIn 0.2s ease-out;
    }
    .pinky-modal-dialog {
      background: #fff;
      border-radius: 20px;
      width: 100%;
      max-width: 440px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      animation: pinkyPopIn 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .pinky-modal-header {
      padding: 14px 18px;
      background: #f8f9fa;
      border-bottom: 1px solid #e9ecef;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .pinky-modal-close {
      background: transparent;
      border: none;
      font-size: 18px;
      font-weight: 800;
      color: #999;
      cursor: pointer;
      padding: 4px 8px;
      line-height: 1;
    }
    .pinky-modal-close:hover { color: #000; }
    @keyframes pinkyFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes pinkyPopIn { from { opacity: 0; transform: scale(0.92); } to { opacity: 1; transform: scale(1); } }
  </style>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
  const statusEl = document.getElementById('share-status');
  const promoUrl = '{{ $shareUrl }}';
  const pctTexto = document.getElementById('txt-porcentaje') ? document.getElementById('txt-porcentaje').textContent : '{{ round($anguloVal) }}%';
  const shareMsg = `¡Mi meñique tiene un ${pctTexto} de torcido por el celular! 📱 Medí el tuyo y ganá descuentos en Gollo: ${promoUrl}`;

  function setStatus(msg, ms = 3000) {
    if (!statusEl) return;
    statusEl.textContent = msg;
    if (ms) setTimeout(() => { statusEl.textContent = ''; }, ms);
  }

  async function generarImagen() {
    const elemento = document.getElementById('compartir');
    return html2canvas(elemento, {
      useCORS: true,
      allowTaint: true,
      scale: 2,
      backgroundColor: null,
      logging: false,
    });
  }

  function abrirModalImagen(dataUrl) {
    const modal = document.getElementById('modal-img-preview');
    const img = document.getElementById('img-preview-target');
    const btnOpen = document.getElementById('btn-modal-open-tab');
    if (modal && img) {
      img.src = dataUrl;
      if (btnOpen) btnOpen.href = dataUrl;
      modal.style.display = 'flex';
    }
  }

  function cerrarModalImagen() {
    const modal = document.getElementById('modal-img-preview');
    if (modal) modal.style.display = 'none';
  }

  function abrirModalShare() {
    const modal = document.getElementById('modal-share-options');
    const waLink = document.getElementById('share-link-whatsapp');
    const fbLink = document.getElementById('share-link-facebook');

    if (waLink) {
      waLink.href = `https://api.whatsapp.com/send?text=${encodeURIComponent(shareMsg)}`;
    }
    if (fbLink) {
      fbLink.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(promoUrl)}`;
    }

    if (modal) modal.style.display = 'flex';
  }

  function cerrarModalShare() {
    const modal = document.getElementById('modal-share-options');
    if (modal) modal.style.display = 'none';
  }

  // ── 1. DESCARGAR IMAGEN ──
  document.getElementById('btn-descargar-compartir').addEventListener('click', async () => {
    setStatus('Generando imagen...', 0);
    try {
      const canvas = await generarImagen();
      const dataUrl = canvas.toDataURL('image/png');

      // Intentar descarga automática en navegadores que lo permitan
      try {
        const link = document.createElement('a');
        link.download = 'pinky-promos.png';
        link.href = dataUrl;
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (e) {
        console.log('Descarga directa bloqueada por iframe, abriendo modal...');
      }

      setStatus('¡Imagen lista! Guardala en tu dispositivo.', 3500);
      abrirModalImagen(dataUrl);
    } catch (e) {
      console.error(e);
      setStatus('No se pudo procesar la imagen.');
    }
  });

  // ── 2. COMPARTIR ──
  document.getElementById('btn-compartir-nativo').addEventListener('click', async () => {
    // Si el navegador soporta Web Share API de forma nativa en iframe, intentarlo
    if (navigator.share) {
      try {
        await navigator.share({
          title: 'Pinky Promos Gollo',
          text: `¡Mi meñique tiene un ${pctTexto} de torcido por el celular! Medí el tuyo:`,
          url: promoUrl,
        });
        setStatus('¡Compartido con éxito! ✔');
        return;
      } catch (err) {
        if (err.name === 'AbortError') return; // El usuario canceló
        // Si falló por políticas de iframe o navegador de escritorio, abrir el modal
      }
    }

    // Fallback garantizado: modal con WhatsApp, Facebook y Copiar enlace
    abrirModalShare();
  });

  // ── 3. COPIAR ENLACE ──
  const btnCopiar = document.getElementById('btn-copiar-enlace');
  if (btnCopiar) {
    btnCopiar.addEventListener('click', async () => {
      try {
        if (navigator.clipboard && navigator.clipboard.writeText) {
          await navigator.clipboard.writeText(promoUrl);
        } else {
          const tempInput = document.createElement('input');
          tempInput.value = promoUrl;
          document.body.appendChild(tempInput);
          tempInput.select();
          document.execCommand('copy');
          document.body.removeChild(tempInput);
        }
        btnCopiar.textContent = '✅ ¡Enlace copiado!';
        btnCopiar.style.backgroundColor = '#d1e7dd';
        btnCopiar.style.borderColor = '#badbcc';
        setTimeout(() => {
          btnCopiar.textContent = '📋 Copiar enlace de la promo';
          btnCopiar.style.backgroundColor = '#f1f3f5';
          btnCopiar.style.borderColor = '#ced4da';
        }, 2500);
      } catch (e) {
        prompt('Copiá este enlace para compartir:', promoUrl);
      }
    });
  }
</script>