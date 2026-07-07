@php
    $anguloVal    = min(20.0, (float)($angulo_menique ?? $angulo ?? 0));
    $nivelNum     = $anguloVal <= 4 ? 1 : ($anguloVal <= 8 ? 2 : ($anguloVal <= 12 ? 3 : ($anguloVal <= 16 ? 4 : 5)));
    $nivelTitulos = [1 => 'Nivel 1 — Leve', 2 => 'Nivel 2 — Bajo', 3 => 'Nivel 3 — Moderado', 4 => 'Nivel 4 — Alto', 5 => 'Nivel 5 — Severo'];
    $nivelCompleto = $nivelTitulos[$nivelNum];
    $nivelParts = explode(' — ', $nivelCompleto);
@endphp

  <div id="compartir">
  
     <div class="logo-slot">
       <!-- Logo del cliente: reemplazar src -->
       <img src="img/logo.png" alt="Pinky Promos"
             onerror="this.replaceWith(Object.assign(document.createElement('div'),{className:'img-placeholder',innerText:'LOGO\n(logo.png)'}))">
     </div>
     <div style="clear:both;"></div>
  
     <div class="compartir-titular">
       <div class="titular-linea1" id="txt-nombre">Uff, {{ $nombre ?? 'Sofía' }}</div>
       <div class="titular-linea2" id="txt-nivel-num">{{ $nivelParts[0] ?? 'Nivel 1' }}</div>
       <div class="titular-linea3" id="txt-nivel-desc">{{ $nivelParts[1] ?? 'Leve' }}</div>
     </div>
 
    <div class="compartir-media">
 
      <div class="mano-slot-wrap">
        <div class="mano-slot">
          <!-- Foto/ilustración de la mano o guante -->
          <img src="img/mano2.jpeg" alt="" />
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
 
    <div class="compartir-cta">
      <div class="cta-linea1">Ingresá a</div>
      <div class="cta-linea2">gollo.com/pinky</div>
      <div class="cta-linea3">y medí el tuyo</div>
    </div>
 
    <div class="compartir-footer">XXXXX &nbsp;|&nbsp; GOLLO.COM</div>
 
  </div>
 
  <!-- ══════════ BOTONES ══════════ -->
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

  <div class="share-status" id="share-status"></div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
  const statusEl = document.getElementById('share-status');
  function setStatus(msg, ms = 2500) {
    statusEl.textContent = msg;
    if (ms) setTimeout(() => { statusEl.textContent = ''; }, ms);
  }

  async function generarImagen() {
    return html2canvas(document.getElementById('compartir'), {
      useCORS: true,
      scale: 2,
      backgroundColor: null,
    });
  }

  document.getElementById('btn-descargar-compartir').addEventListener('click', async () => {
    setStatus('Generando imagen...', 0);
    try {
      const canvas = await generarImagen();
      const link = document.createElement('a');
      link.download = 'pinky-promos.png';
      link.href = canvas.toDataURL('image/png');
      link.click();
      setStatus('Imagen descargada ✔');
    } catch (e) {
      setStatus('No se pudo generar la imagen (revisá CORS de las imágenes)');
    }
  });

  document.getElementById('btn-compartir-nativo').addEventListener('click', async () => {
    setStatus('Generando imagen...', 0);
    try {
      const canvas = await generarImagen();
      const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
      const file = new File([blob], 'pinky-promos.png', { type: 'image/png' });

      if (navigator.canShare && navigator.canShare({ files: [file] })) {
        await navigator.share({
          files: [file],
          title: 'Pinky Promos',
          text: 'Uff, ' + (document.getElementById('txt-nombre').textContent.replace('Uff, ','')) + ' no tiene su pinky tan torcido... ¡el mío está al ' + document.getElementById('txt-porcentaje').textContent + ' de torcido! Medí el tuyo en',
          url: 'https://gollo.com/pinky',
        });
        setStatus('Compartido ✔');
      } else {
        setStatus('Compartir no disponible en este navegador. Usá "Descargar" y compartí manualmente.');
      }
    } catch (e) {
      if (e.name !== 'AbortError') {
        setStatus('No se pudo compartir');
      }
    }
  });
</script>