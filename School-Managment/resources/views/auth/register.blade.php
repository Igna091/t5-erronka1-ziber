@extends('layouts.public')

@section('title', 'Reenviar activación - ZiberEibar')
@section('no_flash', true)

@section('content')
<section class="container auth" style="align-items: center;">
    <div class="auth__copy">
        @if (session('error'))
            <x-alert type="warning" class="fx-fade">{{ session('error') }}</x-alert>
        @endif

        <span class="kicker fx-fade">activación // reenviar enlace</span>
        <h1 class="h-page">
            <span class="fx-type">¿No te llegó</span>
            <span class="fx-type acc" style="--d: 0.7s">el email?<span class="cursor" aria-hidden="true"></span></span>
        </h1>
        <p class="lead fx-fade" style="--d: 1.2s">Escribe el email con el que te dio de alta el centro. Si tu cuenta está pendiente de activar, te enviaremos un enlace nuevo y el anterior dejará de funcionar.</p>

        @if (session('success'))
            <div class="panel panel--acc" role="status" style="padding: 1.125rem 1.25rem; display: flex; flex-direction: column; gap: 0.5rem;">
                <span class="acc fx-tl" style="font-weight: 700;">[ok] solicitud recibida</span>
                <span class="fx-fade" style="--d: 0.4s;">{{ session('success') }}</span>
                <a href="{{ route('login') }}" class="link-arrow">ir a iniciar sesión</a>
            </div>
        @else
            <form method="POST" action="{{ route('register') }}" class="fx-fade" style="--d: 1.3s; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end;">
                @csrf
                <div class="form-group" style="flex: 1 1 18rem;">
                    <label for="email" class="form-label">email</label>
                    <input type="email" id="email" name="email" class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" required autocomplete="email" placeholder="tu@email.com">
                </div>
                <button type="submit" class="btn btn-primary">Reenviar enlace</button>
                @error('email')
                    <span class="form-error" style="flex-basis: 100%;">{{ $message }}</span>
                @enderror
            </form>
        @endif

        <div class="fx-fade small muted" style="--d: 1.5s; display: flex; flex-wrap: wrap; gap: 0.5rem 2rem;">
            <span><span class="acc">›</span> el enlace caduca en 7 días</span>
            <span><span class="acc">›</span> un envío por minuto</span>
            <span><span class="acc">›</span> mira en spam</span>
        </div>
    </div>

    <div aria-hidden="true" style="display: grid; place-items: center; min-height: 20rem;">
        <div class="envelope fx-float">
            <svg viewBox="0 0 240 168" preserveAspectRatio="none" fill="none"><path d="M2 2 L120 92 L238 2" style="stroke: var(--acc);" stroke-width="2"/></svg>
            <span>activar_cuenta.lnk</span>
        </div>
    </div>
</section>
@endsection
