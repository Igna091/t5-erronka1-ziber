@extends('layouts.public')

@section('title', 'Registrarse - ZiberEibar')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <a href="{{ route('home') }}" class="auth-brand" title="ZiberEibar">
                <img src="{{ asset('images/logo-claro.png') }}" alt="ZiberEibar Logo" class="auth-logo auth-logo-light">
                <img src="{{ asset('images/logo-oscuro.png') }}" alt="ZiberEibar Logo" class="auth-logo auth-logo-dark">
            </a>
            <h1>Registrarse</h1>
            <p>Activa tu cuenta de alumno en ZiberEibar</p>
        </div>

        <div class="alert alert-info" style="margin-bottom:1.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
            <span>Para registrarte, el administrador debe haberte dado de alta previamente. Introduce tu email y DNI registrados.</span>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus placeholder="tu@email.com">
                <span class="form-help">Debe coincidir con el email registrado por el administrador.</span>
            </div>

            <div class="form-group">
                <label for="dni" class="form-label">DNI / NIE</label>
                <input type="text" id="dni" name="dni" class="form-input" value="{{ old('dni') }}" required placeholder="12345678A">
                <span class="form-help">Debe coincidir con el DNI registrado por el administrador.</span>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-input" required placeholder="Mínimo 8 caracteres">
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required placeholder="Repite la contraseña">
            </div>

            <button type="submit" class="btn btn-primary w-full">Activar cuenta</button>
        </form>

        <div class="auth-footer">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Iniciar sesión</a>
        </div>
    </div>
</div>
@endsection
