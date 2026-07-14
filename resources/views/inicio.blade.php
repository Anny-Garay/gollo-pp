@extends('layouts.web')

@section('content')
  <!-- ═══════════════════════════════ HERO ═══════════════════════════════ -->
  <section class="hero">
    <div class="container-fluid h-100 px-0">

      <!-- Logo -->
      <div class="logo-block text-center mb-3">
        <img src="{{ asset('/img/logo.png') }}" alt="Puño" class="logo">
      </div>

      <div class="row g-0 align-items-center" style="flex-direction: row-reverse;">

        <!-- RIGHT – logo + tagline -->
        <div class="col-lg-6 pe-4 pe-lg-5">

          <!-- Tagline -->
          <p class="tagline text-end">
            ¡Tu dedo<br>tiene algo<br>que decirte!
          </p>

        </div>

        <!-- LEFT – hand photo -->
        <div class="col-lg-6 position-relative head-left">
          <div class="hero-hand">
            <img src="{{ asset('/img/dedo-grafico.png') }}" alt="Mano" class="dedo-grafico">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═════════════════════ SECCIÓN ANALIZAR ═════════════════════ -->
  <div class="bajar">
    <a href="javascript:void(0)" onclick="document.querySelector('.bajar').scrollIntoView({ behavior: 'smooth', block: 'start' })">
      <img src="{{ asset('/img/abajo.png') }}" alt="Bajar" class="bajar-icon">
    </a>
  </div>
  <section class="section-analizar">
    <div class="container">
      <div class="row align-items-center justify-content-center text-center">

        <!-- Ilustración izquierda -->
        <div class="col-auto">
          <img src="{{ asset('/img/imagen1.png') }}" alt="Mano izquierda" style="width:130px;">
        </div>

        <!-- Texto central -->
        <div class="col-6 py-3">
          <p class="analizar-title">
            Ahora podés recibir un<br>
            descuento dependiendo<br>
            de qué tan doblado tenés<br>
            el meñique
          </p>
        </div>

        <!-- Ilustración derecha -->
        <div class="col-auto hide-mobile">
          <img src="{{ asset('/img/imagen2.png') }}" alt="Celular en mano" style="width:130px;">
        </div>

          </div>
        </div>

      </div>

      <!-- ── Badges ── -->
      <div class="badges-row">

        <!-- Badge 1 -->
        <div class="badge-card">
          <img src="{{ asset('/img/ico-robot.png') }}" alt="IA real" class="badge-icon">
          <span class="badge-label">IA real</span>
          <span class="badge-sub">Tecnología</span>
        </div>

        <!-- Badge 2 -->
        <div class="badge-card">
          <img src="{{ asset('/img/ico-giftbox.png') }}" alt="IA real" class="badge-icon">
          <span class="badge-label">Descuento</span>
          <span class="badge-sub">Asegurado</span>
        </div>

        <!-- Badge 3 -->
        <div class="badge-card">
          <img src="{{ asset('/img/ico-flash.png') }}" alt="IA real" class="badge-icon">
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
        Si tenés el meñique torcido por tu celular,<br>
        te damos un descuento dependiendo de<br>
        qué tanto lo tenés doblado para que<br>
        estrenés un celular más liviano y moderno<br>
        de Gollo.
      </p>

      <div class="text-center mt-5">
        <a href="{{ route('carga') }}" class="btn-cta">
          ¡Vamos!
        </a>
      </div>

    </div>
  </section>
@endsection