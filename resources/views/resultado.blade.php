@extends('layouts.blanco')

@section('content')

@php
    if (isset($_GET['dev'])) {
        $dayFactor      = max(1, (int)date('j'));
        $fakeAngle      = 3.0 + ($dayFactor % 10) * 0.9;
        $humana_score   = round(60 + ($dayFactor % 36), 1);
        $angulo_menique = $fakeAngle;

        $bx = 200; $by = 380;
        $px = 180; $py = 260;
        $a2 = atan2($py - $by, $px - $bx);
        $a1 = $a2 + deg2rad($fakeAngle);
        $dt = 280;
        $tx = $bx + $dt * cos($a1);
        $ty = $by + $dt * sin($a1);
        $dx = ($px + $tx) / 2;
        $dy = ($py + $ty) / 2;

        $pinky_points = [
            ['x' => $bx, 'y' => $by],
            ['x' => $px, 'y' => $py],
            ['x' => round($dx, 2), 'y' => round($dy, 2)],
            ['x' => round($tx, 2), 'y' => round($ty, 2)],
        ];
    }

    $angulo    = min(20.0, (float)($angulo_menique ?? 0));
    $maxGauge  = 20.0;
    $ratio     = min(max($angulo / $maxGauge, 0.0), 1.0);
    $arcDeg    = 180.0 * (1.0 - $ratio);
    $arcRad    = deg2rad($arcDeg);

    // SVG gauge center (110,115), radius 90
    $cx = 110; $cy = 115;
    $rN   = 66; // needle length
    $rDot = 92; // dot on arc
    $nx   = round($cx + $rN  * cos($arcRad), 2);
    $ny   = round($cy - $rN  * sin($arcRad), 2);
    $dotX = round($cx + $rDot * cos($arcRad), 2);
    $dotY = round($cy - $rDot * sin($arcRad), 2);

    if ($angulo <= 4)      { $nivel_num = 1; $nBg = '#e8f5e9'; $nTxt = '#2e7d32'; $nBor = '#2e7d32'; }
    elseif ($angulo <= 8)  { $nivel_num = 2; $nBg = '#f9fbe7'; $nTxt = '#558b2f'; $nBor = '#558b2f'; }
    elseif ($angulo <= 12) { $nivel_num = 3; $nBg = '#fffde7'; $nTxt = '#f9a825'; $nBor = '#f9a825'; }
    elseif ($angulo <= 16) { $nivel_num = 4; $nBg = '#fff3e0'; $nTxt = '#e65100'; $nBor = '#e65100'; }
    else                   { $nivel_num = 5; $nBg = '#fff0f0'; $nTxt = '#e31837'; $nBor = '#e31837'; }

    $nivelTexto = \App\Models\NivelTexto::where('nivel', $nivel_num)->first();
    $nivel      = $nivelTexto?->titulo ?? "NIVEL {$nivel_num}";

    $anguloDisplay = $angulo_menique !== null ? number_format($angulo, 1) : '—';
@endphp

<div class="res-page">

    <div class="res-top-actions">
        <a href="{{ route('carga') }}" class="res-back-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M15 19l-7-7 7-7" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>

    <div class="res-content">

        {{-- ── PINKY TRACE ── --}}
        @if($pinky_points)
        <div class="res-card">
            <p class="diag-header">🏥 Resultado del diagnóstico</p>
            <div class="gauge-angle-val">{{ $anguloDisplay }}<sup>°</sup></div>
            <div class="trace-wrap">
                <canvas id="pinky-trace" width="400" height="480"></canvas>
            </div>
        </div>
        @endif

        {{-- ── DESCRIPTION CARD ── --}}
        <div class="desc-card">
            @if($nivelTexto)
                <p class="desc-card-title">{{ $nivelTexto->titulo }}</p>
                <div class="desc-card-body">{!! $nivelTexto->contenido !!}</div>
            @else
                <p class="desc-card-title">🔥 ¡Mirá vos, ese meñique ya pide un respiro! 🎉</p>
                <p class="desc-card-body">Nuestros cálculos detectaron que tu dedo ha hecho un gran esfuerzo sosteniendo tu cel. Para que no se canse más, aquí tenés tu documento de canje por un modelo más liviano en Gollo. ¡Aprovechá y estrenálo hoy mismo!</p>
            @endif
        </div>

        {{-- ── FORM CARD ── --}}
        <div class="form-card">
            <div class="form-card-head">
                <span class="form-card-head-icon">🛡️</span>
                <div class="form-card-head-text">
                    <strong>Datos para tu documento</strong>
                    <small>Llená para recibir tu descuento</small>
                </div>
            </div>

            @if ($errors->any())
                <div class="form-errores">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('guardar') }}">
                @csrf
                <input type="hidden" name="humana_score"   value="{{ $humana_score }}">
                <input type="hidden" name="angulo_menique" value="{{ $angulo_menique }}">

                <div class="form-field">
                    <label class="form-field-label" for="nombre">Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre') }}"
                           placeholder="Ej: Juan Carlos Rodríguez Jiménez"
                           required autocomplete="name">
                </div>
                <div class="form-field">
                    <label class="form-field-label" for="cedula">
                        Número de Cédula o DIMEX *
                    </label>
                    <input type="text" id="cedula" name="cedula"
                           value="{{ old('cedula') }}"
                           placeholder="1 - 2345 - 6789"
                           required autocomplete="off">
                </div>
                <div class="form-field">
                    <label class="form-field-label" for="celular">Celular *</label>
                    <input type="text" id="celular" name="celular"
                           value="{{ old('celular') }}"
                           placeholder="8888 - 8888"
                           required autocomplete="tel">
                </div>
                <div class="form-field">
                    <label class="form-field-label" for="email">Correo Electrónico *</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="correo@ejemplo.com"
                           required autocomplete="email">
                </div>

                <button type="submit" class="btn-guardar">Obtener mi descuento</button>
            </form>
            <sub>Solo usamos tus datos para generar el descuento</sub>
        </div>

        <div class="step-dots">
            <span class="step-dot"></span>
            <span class="step-dot"></span>
            <span class="step-dot step-dot--active"></span>
            <span class="step-dot"></span>
            <span class="step-dot"></span>
        </div>

    </div>
</div>

@if($pinky_points)
<script>
  (function() {
    var points = @json($pinky_points);
    var canvas = document.getElementById('pinky-trace');
    if (!canvas || !points || points.length < 4) return;

    var ctx = canvas.getContext('2d');
    var W = canvas.width;
    var H = canvas.height;

    // ── Calcular bounds de los puntos para ajustarlos al canvas ──
    var minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
    points.forEach(function(p) {
      if (p.x < minX) minX = p.x;
      if (p.x > maxX) maxX = p.x;
      if (p.y < minY) minY = p.y;
      if (p.y > maxY) maxY = p.y;
    });
    var rangeX = maxX - minX || 1;
    var rangeY = maxY - minY || 1;

    var padL = 70, padR = 40, padT = 60, padB = 70;
    var drawW = W - padL - padR;
    var drawH = H - padT - padB;

    var scale = Math.min(drawW / rangeX, drawH / rangeY);
    var offX = padL + (drawW - rangeX * scale) / 2;
    var offY = padT + (drawH - rangeY * scale) / 2;

    var pts = points.map(function(p) {
      return {
        x: offX + (p.x - minX) * scale,
        y: offY + (p.y - minY) * scale
      };
    });

    // ── Estáticos (se dibujan una vez) ──
    var bg = '#0b1120';
    var gridColor = 'rgba(0, 200, 255, 0.07)';
    var neon = '#00e5ff';
    var accent = '#ff3366';
    var textDim = 'rgba(255,255,255,0.5)';
    var textBright = '#fff';
    var labels = ['BASE', 'PIP', 'DIP', 'TIP'];
    var angleDeg = {{ $angulo_menique ?? 0 }};

    function drawBackground() {
      // Fondo
      ctx.fillStyle = bg;
      ctx.fillRect(0, 0, W, H);

      // Grid
      ctx.strokeStyle = gridColor;
      ctx.lineWidth = 1;
      for (var i = 0; i <= 10; i++) {
        var x = padL + (drawW / 10) * i;
        var y = padT + (drawH / 10) * i;
        ctx.beginPath(); ctx.moveTo(x, padT); ctx.lineTo(x, padT + drawH); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(padL, y); ctx.lineTo(padL + drawW, y); ctx.stroke();
      }

      // Scanlines
      ctx.fillStyle = 'rgba(255,255,255,0.015)';
      for (var y = 0; y < H; y += 3) {
        ctx.fillRect(0, y, W, 1);
      }

      // HUD brackets
      var bSize = 14, bGap = 4;
      ctx.strokeStyle = neon;
      ctx.lineWidth = 2;
      ctx.globalAlpha = 0.3;
      function bracket(x, y, dx, dy) {
        ctx.beginPath();
        ctx.moveTo(x + dx * bSize, y);
        ctx.lineTo(x, y);
        ctx.lineTo(x, y + dy * bSize);
        ctx.stroke();
      }
      bracket(bGap, bGap, 1, 1);
      bracket(W - bGap, bGap, -1, 1);
      bracket(bGap, H - bGap, 1, -1);
      bracket(W - bGap, H - bGap, -1, -1);
      ctx.globalAlpha = 1;

      // Header
      /*ctx.fillStyle = 'rgba(0, 229, 255, 0.15)';
      ctx.fillRect(0, 0, W, 32);
      ctx.fillStyle = neon;
      ctx.font = '10px monospace';
      ctx.textAlign = 'left';
      ctx.textBaseline = 'middle';
      ctx.fillText('◈ PINKY ANALYSIS v2.1', 12, 16);
      ctx.textAlign = 'right';
      ctx.fillStyle = textDim;
      ctx.fillText('PHONE PINKY™', W - 12, 16);*/

      // Línea de referencia base→punta
      ctx.beginPath();
      ctx.strokeStyle = 'rgba(255,255,255,0.15)';
      ctx.lineWidth = 1.5;
      ctx.setLineDash([5, 5]);
      ctx.moveTo(pts[0].x, pts[0].y);
      ctx.lineTo(pts[3].x, pts[3].y);
      ctx.stroke();
      ctx.setLineDash([]);

      // Arco del ángulo
      if (angleDeg > 0.1) {
        var cx = pts[0].x, cy = pts[0].y;
        var angle1 = Math.atan2(pts[3].y - cy, pts[3].x - cx);
        var angle2 = Math.atan2(pts[1].y - cy, pts[1].x - cx);
        var arcR = 30;
        var startA = Math.min(angle1, angle2);
        var endA = Math.max(angle1, angle2);
        ctx.beginPath();
        ctx.strokeStyle = accent;
        ctx.lineWidth = 2;
        ctx.globalAlpha = 0.7;
        ctx.arc(cx, cy, arcR, startA, endA);
        ctx.stroke();
        ctx.globalAlpha = 1;

        var midA = (startA + endA) / 2;
        var labelX = cx + (arcR + 18) * Math.cos(midA);
        var labelY = cy + (arcR + 18) * Math.sin(midA);
        ctx.fillStyle = accent;
        ctx.font = 'bold 12px monospace';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';
        ctx.fillText(angleDeg.toFixed(1) + '°', labelX, labelY);
      }

      // Footer
      /*ctx.fillStyle = 'rgba(0, 229, 255, 0.08)';
      ctx.fillRect(0, H - 26, W, 26);
      ctx.fillStyle = textDim;
      ctx.font = '9px monospace';
      ctx.textAlign = 'left';
      ctx.textBaseline = 'middle';
      ctx.fillText('TRACE: ' + pts.length + ' pts  |  GRID: 10×10  |  REF: BASE→TIP', 12, H - 13);
      ctx.textAlign = 'right';
      ctx.fillStyle = accent;
      ctx.font = 'bold 11px monospace';
      ctx.fillText(angleDeg.toFixed(1) + '° deviation', W - 12, H - 13);*/
    }

    function drawTrace() {
      // Línea del trazo — sutil, sin glow
      ctx.shadowBlur = 0;
      ctx.strokeStyle = 'rgba(0, 229, 255, 0.4)';
      ctx.lineWidth = 2;
      ctx.lineJoin = 'round';
      ctx.lineCap = 'round';
      ctx.beginPath();
      ctx.moveTo(pts[0].x, pts[0].y);
      for (var i = 1; i < pts.length; i++) {
        ctx.lineTo(pts[i].x, pts[i].y);
      }
      ctx.stroke();

      // Segunda pasada para centro más claro
      ctx.strokeStyle = 'rgba(255,255,255,0.25)';
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.moveTo(pts[0].x, pts[0].y);
      for (var i = 1; i < pts.length; i++) {
        ctx.lineTo(pts[i].x, pts[i].y);
      }
      ctx.stroke();
    }

    function drawPoints(glow) {
      // glow: 0–1, intensity of the pulsing
      for (var i = 0; i < pts.length; i++) {
        var isTip = (i === pts.length - 1);
        var r = (i === 0 || isTip) ? 7 : 5;
        var baseGlowR = r + 6;
        var glowR = baseGlowR + glow * 10;

        // ── Cian para BASE/PIP/DIP │ Rosado para TIP ──
        var ptColor = isTip ? accent : neon;
        var ptGlow = isTip ? 'rgba(255,51,102,' : 'rgba(0,229,255,';
        var ptGlowAlpha = (0.25 + glow * 0.45).toFixed(3);

        // Glow exterior pulsante
        var grad = ctx.createRadialGradient(pts[i].x, pts[i].y, 0, pts[i].x, pts[i].y, glowR);
        grad.addColorStop(0, ptGlow + ptGlowAlpha + ')');
        grad.addColorStop(1, 'transparent');
        ctx.fillStyle = grad;
        ctx.beginPath();
        ctx.arc(pts[i].x, pts[i].y, glowR, 0, Math.PI * 2);
        ctx.fill();

        // Círculo exterior
        ctx.beginPath();
        ctx.strokeStyle = ptColor;
        ctx.lineWidth = 2;
        ctx.arc(pts[i].x, pts[i].y, r + 3, 0, Math.PI * 2);
        ctx.stroke();

        // Círculo interior
        ctx.beginPath();
        ctx.fillStyle = ptColor;
        ctx.arc(pts[i].x, pts[i].y, r, 0, Math.PI * 2);
        ctx.fill();

        // Label
        ctx.fillStyle = textBright;
        ctx.font = 'bold 10px monospace';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';
        ctx.fillText(labels[i], pts[i].x, pts[i].y - r - 10);
      }
    }

    // ── Animación ──
    drawBackground();

    function animate(time) {
      var glow = 0.5 + 0.5 * Math.sin(time / 300);

      // Borrar solo el área de puntos (desde el grid hacia abajo, excluyendo header/footer)
      // Más simple: redibujar trazo + puntos sobre el fondo estático
      // (el fondo no se repinta, se mantiene)

      // Limpiar el área de dibujo para no acumular
      ctx.clearRect(padL - 10, padT - 10, drawW + 20, drawH + 40);

      // Restaurar fondo en esa zona (un rectángulo del color de fondo basta,
      // pero como tenemos grid y scanlines, mejor dibujamos todo de nuevo
      // sobre el fondo — es más limpio aunque repita el fondo)
      // NOTA: para mantener el grid, basta con redibujar el fondo completo
      // y luego los elementos estáticos que estarán detrás.
      // Pero ya drawBackground dibujó todo. Para evitar borrarlo,
      // simplemente redibujamos el área limpia con el color de fondo + grid locals
      // Más fácil: redibujamos todo el background cada frame (es rápido en canvas 2D)
      drawBackground();

      // Trazo sutil
      drawTrace();

      // Puntos con glow pulsante
      drawPoints(glow);

      requestAnimationFrame(animate);
    }

    requestAnimationFrame(animate);
  })();
</script>
@endif

@endsection