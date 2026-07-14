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

  <!-- ═══ Scroll hint ═══ -->
  <div id="scroll-hint" style="position:fixed; bottom:24px; right:24px; z-index:9999; width:50px; height:50px; cursor:pointer; opacity:1; transition:opacity .3s ease, transform .3s ease; animation:bounce-float 2s ease-in-out infinite;">
    <img src="{{ asset('/img/abajo.png') }}" alt="Bajar" style="width:100%; height:auto; display:block;">
  </div>
  <style>
    #scroll-hint.hidden { opacity:0; pointer-events:none; transform:translateY(10px); }
    @keyframes bounce-float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-10px); } }
  </style>
  <script>
    (function(){
      var btn = document.getElementById('scroll-hint');
      if (!btn) return;
      function checkScroll() {
        var scrollBottom = window.scrollY + window.innerHeight;
        var pageHeight = document.documentElement.scrollHeight;
        if (pageHeight <= window.innerHeight + 2 || scrollBottom >= pageHeight - 100) {
          btn.classList.add('hidden');
        } else {
          btn.classList.remove('hidden');
        }
      }
      window.addEventListener('scroll', checkScroll);
      window.addEventListener('resize', checkScroll);
      checkScroll();
      btn.addEventListener('click', function () {
        window.scrollBy({ top: window.innerHeight * 0.8, behavior: 'smooth' });
      });
    })();
  </script>
</body>
</html>