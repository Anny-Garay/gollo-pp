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
    <a href="https://www.gollo.com" target="_blank" rel="noopener noreferrer" class="share-btn gollo-link" id="btn-ir-a-gollo" style="background: #e31837; text-decoration: none; color: #ffffff;">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
      IR A GOLLO.COM
    </a>
  </div>

  <div class="share-status" id="share-status" style="text-align:center; font-size:13px; font-weight:700; color:#004DB5; margin-top:8px; min-height:20px;"></div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
  const statusEl = document.getElementById('share-status');
  function setStatus(msg, ms = 2500) {
    if (!statusEl) return;
    statusEl.textContent = msg;
    if (ms) setTimeout(() => { statusEl.textContent = ''; }, ms);
  }

  async function generarImagen() {
    return html2canvas(document.getElementById('compartir'), {
      useCORS: true,
      allowTaint: true,
      scale: 2,
      backgroundColor: null,
      logging: false,
    });
  }

  // ── DESCARGAR IMAGEN ──
  document.getElementById('btn-descargar-compartir').addEventListener('click', async () => {
    setStatus('Generando imagen...', 0);
    try {
      const canvas = await generarImagen();
      const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.download = 'pinky-promos.png';
      link.href = url;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      setTimeout(() => URL.revokeObjectURL(url), 2500);
      setStatus('Imagen descargada ✔');
    } catch (e) {
      console.error(e);
      setStatus('No se pudo generar la imagen');
    }
  });

  // ── COMPARTIR DIRECTAMENTE A REDES SOCIALES (CON ARCHIVO DE IMAGEN) ──
  document.getElementById('btn-compartir-nativo').addEventListener('click', async () => {
    setStatus('Generando imagen...', 0);
    try {
      const canvas = await generarImagen();
      const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
      const file = new File([blob], 'pinky-promos.png', { type: 'image/png' });
      const pct = document.getElementById('txt-porcentaje') ? document.getElementById('txt-porcentaje').textContent : '{{ round($anguloVal) }}%';

      // 1. En iPhone / Android: compartir el archivo de imagen directamente al selector de apps nativo
      if (navigator.canShare && navigator.canShare({ files: [file] })) {
        try {
          await navigator.share({
            files: [file],
            title: 'Pinky Promos Gollo',
            text: '¡Mi pinky está al ' + pct + ' de torcido! Medí el tuyo en gollo.com/pinky',
          });
          setStatus('Compartido ✔');
          return;
        } catch (err) {
          if (err.name === 'AbortError') {
            setStatus('');
            return;
          }
          // Fallback para iOS: compartir solo el archivo si falla con texto conjunto
          try {
            await navigator.share({ files: [file] });
            setStatus('Compartido ✔');
            return;
          } catch (e2) {
            if (e2.name === 'AbortError') { setStatus(''); return; }
          }
        }
      }

      // 2. Si el navegador no soporta compartir archivos directamente (ej. PC/Mac de escritorio)
      if (navigator.share) {
        await navigator.share({
          title: 'Pinky Promos Gollo',
          text: '¡Mi pinky está al ' + pct + ' de torcido! Medí el tuyo en',
          url: '{{ url('/') }}',
        });
        setStatus('Compartido ✔');
      } else {
        setStatus('Compartir no disponible en este navegador. Usá "Descargar imagen" y compartila.');
      }
    } catch (e) {
      if (e.name !== 'AbortError') {
        console.error(e);
        setStatus('No se pudo compartir');
      } else {
        setStatus('');
      }
    }
  });
</script>