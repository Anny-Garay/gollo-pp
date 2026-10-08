<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>¡Listo! - Phone Pinky</title>
  <link rel="stylesheet" href="{{ asset('style.css') }}">
  <link href="{{ asset('/css/blanco.css?v=' . time()) }}" rel="stylesheet"/>
</head>
<body class="listo-body">
  @yield('content')

  <!-- ═══ Floating Scroll Navigation (Subir / Bajar) ═══ -->
  <div id="scroll-nav-container" style="position:fixed; bottom:20px; right:20px; z-index:9999; display:flex; gap:10px; align-items:center;">
    <button type="button" id="scroll-btn-up" class="scroll-nav-btn scroll-btn-hidden" aria-label="Subir" title="Subir">
      <img src="{{ asset('/img/abajo.png') }}" alt="Subir" style="transform: rotate(180deg);">
    </button>
    <button type="button" id="scroll-btn-down" class="scroll-nav-btn" aria-label="Bajar" title="Bajar">
      <img src="{{ asset('/img/abajo.png') }}" alt="Bajar">
    </button>
  </div>
  <style>
    .scroll-nav-btn {
      background: transparent;
      border: none;
      padding: 0;
      width: 52px;
      height: 52px;
      cursor: pointer;
      opacity: 1;
      transition: opacity 0.25s ease, transform 0.25s ease, filter 0.2s ease;
      animation: bounce-nav-float 2s ease-in-out infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      outline: none;
    }
    .scroll-nav-btn:hover {
      transform: scale(1.1);
      filter: drop-shadow(0 4px 10px rgba(0,0,0,0.45));
    }
    .scroll-nav-btn:active {
      transform: scale(0.92);
    }
    .scroll-nav-btn img {
      width: 100%;
      height: auto;
      display: block;
      pointer-events: none;
      filter: drop-shadow(0 4px 8px rgba(0,0,0,0.35));
    }
    .scroll-nav-btn.scroll-btn-hidden {
      opacity: 0 !important;
      pointer-events: none !important;
      transform: translateY(12px) scale(0.7) !important;
      visibility: hidden !important;
    }
    @keyframes bounce-nav-float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-6px); }
    }
  </style>
  <script>
    (function(){
      var btnUp = document.getElementById('scroll-btn-up');
      var btnDown = document.getElementById('scroll-btn-down');
      if (!btnUp || !btnDown) return;

      function checkScroll() {
        var scrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
        var windowH = window.innerHeight || document.documentElement.clientHeight || 0;
        var docH = Math.max(
          document.body.scrollHeight, document.documentElement.scrollHeight,
          document.body.offsetHeight, document.documentElement.offsetHeight,
          document.body.clientHeight, document.documentElement.clientHeight
        );

        if (docH <= windowH + 20) {
          btnUp.classList.add('scroll-btn-hidden');
          btnDown.classList.add('scroll-btn-hidden');
          return;
        }

        // Mostrar u ocultar arriba
        if (scrollY > 30) {
          btnUp.classList.remove('scroll-btn-hidden');
        } else {
          btnUp.classList.add('scroll-btn-hidden');
        }

        // Mostrar u ocultar abajo
        if (scrollY + windowH >= docH - 40) {
          btnDown.classList.add('scroll-btn-hidden');
        } else {
          btnDown.classList.remove('scroll-btn-hidden');
        }
      }

      window.addEventListener('scroll', checkScroll, { passive: true });
      window.addEventListener('resize', checkScroll);
      window.addEventListener('load', checkScroll);
      setTimeout(checkScroll, 300);
      setTimeout(checkScroll, 1000);

      btnDown.addEventListener('click', function () {
        var distance = Math.max(window.innerHeight * 0.9, 520);
        window.scrollBy({ top: distance, behavior: 'smooth' });
      });

      btnUp.addEventListener('click', function () {
        var distance = Math.max(window.innerHeight * 0.9, 520);
        window.scrollBy({ top: -distance, behavior: 'smooth' });
      });
    })();

    // Auto-notificar altura al padre (iframe)
    (function(){
      function notificarAltura() {
        try {
          if (window.parent && window.parent !== window) {
            var h = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
            window.parent.postMessage({ type: 'gollo-pinky-resize', height: h }, '*');
          }
        } catch(e) {}
      }
      window.addEventListener('load', notificarAltura);
      window.addEventListener('resize', notificarAltura);
      setTimeout(notificarAltura, 300);
      setTimeout(notificarAltura, 1000);
    })();
  </script>
</body>
</html>