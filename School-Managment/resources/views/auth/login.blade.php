@extends('layouts.public')

@section('title', 'Iniciar sesión - ZiberEibar')

@section('content')
<section class="container auth">
    <svg class="auth__traces" viewBox="0 0 760 200" preserveAspectRatio="none" fill="none" aria-hidden="true">
        <path d="M0 120 H420 L500 40 H760" style="stroke: var(--line-2);" stroke-width="2"/>
        <path d="M0 160 H440 L520 80 H760" style="stroke: var(--line-2);" stroke-width="2"/>
        <path class="pulse" style="--d: 1.5s; stroke: var(--acc);" d="M0 120 H420 L500 40 H760" pathLength="100" stroke-width="3" stroke-linecap="round"/>
    </svg>

    <div class="auth__copy">
        <span class="kicker fx-fade">acceso // alumnado y administración</span>
        <h1 class="h-hero">
            <span class="fx-type">Inicia</span>
            <span class="fx-type acc" style="--d: 0.7s">sesión.<span class="cursor" aria-hidden="true"></span></span>
        </h1>
        <div class="auth__notes fx-fade" style="--d: 1.3s">
            <div><span class="prompt">$</span> ziber login</div>
            <div>&gt; entra con el email con el que te dio de alta el centro</div>
            <div>&gt; 5 intentos fallidos bloquean el acceso durante 1 minuto</div>
        </div>
    </div>

    <div class="panel panel--float auth__form fx-fade" style="--d: 0.3s">
        <x-pads :count="2" />
        <div class="panel__row"><span class="lbl">zibereibar@eibar: ~/login</span><span class="lbl">tty1</span></div>

        @if ($errors->any())
            <x-alert type="error" class="fx-shake">{{ $errors->first() }}</x-alert>
        @endif

        <form method="POST" action="{{ route('login') }}" class="form">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">email</label>
                <input type="email" id="email" name="email" class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="tu@email.com">
            </div>
            <div class="form-group">
                <label for="password" class="form-label">contraseña</label>
                <div class="input-with-btn">
                    <input type="password" id="password" name="password" class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}" required autocomplete="current-password" placeholder="tu contraseña">
                    <button type="button" class="input-btn" data-toggle-password="password" aria-pressed="false" aria-label="Mostrar u ocultar la contraseña">mostrar</button>
                </div>
            </div>
            <label class="check">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                mantener la sesión iniciada
            </label>
            <button type="submit" class="btn btn-primary btn-block">Iniciar sesión</button>
        </form>

        <div class="hr"></div>
        <div class="stack-sm small muted">
            <span>¿Primera vez? Activa tu cuenta con el enlace que te enviamos por email.</span>
            <a href="{{ route('register') }}" class="link-arrow">no me llegó el email de activación</a>
        </div>
    </div>
</section>
@endsection
