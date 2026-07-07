 <div id="compartir">
 
    <div class="logo-slot">
      <!-- Logo del cliente: reemplazar src -->
      <img src="img/logo.png" alt="Pinky Promos"
            onerror="this.replaceWith(Object.assign(document.createElement('div'),{className:'img-placeholder',innerText:'LOGO\n(logo.png)'}))">
    </div>
    <div style="clear:both;"></div>
 
    <div class="compartir-titular">
      <div class="titular-linea1" id="txt-nombre">Uff, Sofía</div>
      <div class="titular-linea2">No tiene su pinky</div>
      <div class="titular-linea3">Tan torcido</div>
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
          <span class="sello-pct" id="txt-porcentaje">8%</span>
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
 
  <!-- ══════════ BOTONES DE COMPARTIR ══════════ -->
  <div class="share-bar">
 
    <button class="share-btn whatsapp" id="btn-whatsapp">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.39 1.26 4.81L2 22l5.42-1.36a9.87 9.87 0 0 0 4.62 1.14h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.13-2.9-7C17.19 3.03 14.71 2 12.04 2Zm5.8 14.03c-.24.68-1.19 1.25-1.94 1.4-.51.11-1.18.19-3.42-.73-2.86-1.18-4.7-4.06-4.84-4.25-.14-.19-1.16-1.54-1.16-2.94 0-1.4.73-2.09 1-2.38.23-.25.51-.31.68-.31.17 0 .34 0 .49.01.16.01.37-.06.58.44.24.58.8 2 .87 2.14.07.15.11.32.02.51-.09.19-.14.31-.28.47-.14.16-.29.36-.42.48-.14.13-.28.28-.12.55.16.27.71 1.17 1.53 1.9 1.05.94 1.94 1.23 2.21 1.37.27.14.43.12.59-.07.16-.19.68-.79.86-1.06.18-.27.36-.22.6-.13.24.09 1.53.72 1.79.85.26.13.44.19.5.3.06.11.06.65-.18 1.33Z"/></svg>
      WhatsApp
    </button>
 
    <button class="share-btn facebook" id="btn-facebook">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-8h2.7l.4-3.13h-3.1V7.9c0-.9.25-1.52 1.55-1.52h1.66V3.6C16.4 3.5 15.4 3.42 14.25 3.42c-2.4 0-4.05 1.46-4.05 4.15V9.87H7.5V13h2.7v8h3.3Z"/></svg>
      Facebook
    </button>
 
    <button class="share-btn x" id="btn-x">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.24 2.75h3.05l-6.66 7.62 7.84 10.38h-6.14l-4.81-6.3-5.5 6.3H2.97l7.13-8.15L2.6 2.75h6.3l4.35 5.76 4.99-5.76Zm-1.07 16.17h1.69L7.9 4.5H6.08l11.09 14.42Z"/></svg>
      X
    </button>
 
    <button class="share-btn download" id="btn-download">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v11.17l3.59-3.58L17 12l-5 5-5-5 1.41-1.41L11 14.17V3h1Zm7 16v2H5v-2h14Z"/></svg>
      Descargar
    </button>
 
    <button class="share-btn copy" id="btn-copy">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1Zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Zm0 16H8V7h11v14Z"/></svg>
      Copiar link
    </button>
 
  </div>
 
  <div class="share-status" id="share-status"></div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
  const shareUrl   = 'https://gollo.com/pinky';
  const shareTexto = () =>
    `Uff, ${document.getElementById('txt-nombre').textContent.replace('Uff, ','')} no tiene su pinky tan torcido... ¡el mío está al ${document.getElementById('txt-porcentaje').textContent} de torcido! Medí el tuyo:`;
 
  const statusEl = document.getElementById('share-status');
  function setStatus(msg, ms = 2500) {
    statusEl.textContent = msg;
    if (ms) setTimeout(() => { statusEl.textContent = ''; }, ms);
  }
 
  document.getElementById('btn-whatsapp').addEventListener('click', () => {
    const texto = encodeURIComponent(shareTexto() + ' ' + shareUrl);
    window.open(`https://wa.me/?text=${texto}`, '_blank');
  });
 
  document.getElementById('btn-facebook').addEventListener('click', () => {
    const u = encodeURIComponent(shareUrl);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${u}`, '_blank', 'width=600,height=500');
  });
 
  document.getElementById('btn-x').addEventListener('click', () => {
    const texto = encodeURIComponent(shareTexto());
    const u = encodeURIComponent(shareUrl);
    window.open(`https://twitter.com/intent/tweet?text=${texto}&url=${u}`, '_blank', 'width=600,height=500');
  });
 
  document.getElementById('btn-copy').addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(shareUrl);
      setStatus('Link copiado ✔');
    } catch (e) {
      setStatus('No se pudo copiar el link');
    }
  });
 
  document.getElementById('btn-download').addEventListener('click', async () => {
    setStatus('Generando imagen...', 0);
    try {
      const canvas = await html2canvas(document.getElementById('compartir'), {
        useCORS: true,
        scale: 2,
        backgroundColor: null,
      });
      const link = document.createElement('a');
      link.download = 'pinky-promos.png';
      link.href = canvas.toDataURL('image/png');
      link.click();
      setStatus('Imagen descargada ✔');
    } catch (e) {
      setStatus('No se pudo generar la imagen (revisá CORS de las imágenes)');
    }
  });
 
  // Si el navegador soporta compartir archivos nativamente (mobile), lo ofrecemos también
  if (navigator.canShare) {
    // Se podría agregar un botón "Compartir" nativo que use navigator.share({files:[...]})
    // generando el PNG con html2canvas antes de invocarlo.
  }
</script>