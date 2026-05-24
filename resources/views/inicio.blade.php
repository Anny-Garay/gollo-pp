@extends('layouts.web')

@section('content')
  <!-- ═══════════════════════════════ HERO ═══════════════════════════════ -->
  <section class="hero">
    <div class="container-fluid h-100 px-0">
      <div class="row g-0 align-items-center" style="min-height:480px;">

        <!-- LEFT – hand photo -->
        <div class="col-6 position-relative" style="min-height:480px;">
          <div class="hero-hand">
            <img src="{{ asset('/img/dedo-grafico.png') }}" alt="Mano" class="dedo-grafico">
          </div>
        </div>

        <!-- RIGHT – logo + tagline -->
        <div class="col-6 d-flex flex-column justify-content-center align-items-end pe-4 pe-md-5" style="min-height:480px;">

          <!-- Logo -->
          <div class="logo-block text-end mb-3">
            <div class="d-flex align-items-center justify-content-end gap-2">
              <img src="{{ asset('/img/logo.png') }}" alt="Puño" class="logo">
            </div>
          </div>

          <!-- Tagline -->
          <p class="tagline text-end">
            ¡Tu dedo<br>tiene algo<br>que decirte!
          </p>

        </div>
      </div>
    </div>
  </section>


  <!-- ═════════════════════ SECCIÓN ANALIZAR ═════════════════════ -->
  <section class="section-analizar">
    <div class="container">
      <div class="row align-items-center justify-content-center text-center">

        <!-- Ilustración izquierda -->
        <div class="col-auto">
          <img src="{{ asset('/img/imagen1.png') }}" alt="Mano izquierda" style="width:130px;">
        </div>

        <!-- Texto central -->
        <div class="col-12 col-md-6 py-3">
          <p class="analizar-title">
            Vamos a analizar<br>
            cómo el peso de tú<br>
            cel marca tu mano
          </p>
        </div>

        <!-- Ilustración derecha -->
        <div class="col-auto">
          <img src="{{ asset('/img/imagen2.png') }}" alt="Celular en mano" style="width:130px;">
        </div>

          </div>
        </div>

      </div>

      <!-- ── Badges ── -->
      <div class="badges-row">

        <!-- Badge 1 -->
        <div class="badge-card">
          <img src="{{ asset('/img/icono1.png') }}" alt="IA real" class="badge-icon">
          <span class="badge-label">IA real</span>
          <span class="badge-sub">Tecnología</span>
        </div>

        <!-- Badge 2 -->
        <div class="badge-card">
          <img src="{{ asset('/img/icono2.png') }}" alt="IA real" class="badge-icon">
          <span class="badge-label">Totalmente</span>
          <span class="badge-sub">Gratis</span>
        </div>

        <!-- Badge 3 -->
        <div class="badge-card">
          <img src="{{ asset('/img/icono3.png') }}" alt="IA real" class="badge-icon">
          <span class="badge-label">3 segundos</span>
          <span class="badge-sub">Diagnóstico</span>
        </div>

      </div>

    </div>
  </section>


  <!-- ═════════════════════ TEXTO FINAL + CTA ═════════════════════ -->
  <section style="padding: 0 0 56px;">
    <div class="container">

      <p class="footer-text">
        Si tu meñique ya se puso en 'modo descanso', te<br>
        damos un documento de canje oficial para que<br>
        estrenés un celular más liviano y moderno en Gollo.
      </p>

      <div class="text-center mt-5">
        <a href="{{ route('carga') }}" class="btn-cta">
          <span class="cta-arrows">»</span>
          &nbsp;¡Darle viaje!&nbsp;
          <span class="cta-arrows">»</span>
        </a>
      </div>

    </div>
  </section>
@endsection