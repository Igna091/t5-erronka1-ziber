@extends('layouts.public')

@section('title', 'Iniciar sesión - EduCenter')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">E</div>
            <h1>Iniciar sesión</h1>
            <p>Accede a tu cuenta de EduCenter</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus placeholder="tu@email.com">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••">
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="text-sm">Recordar sesión</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-full">Iniciar sesión</button>
        </form>

        <div class="auth-footer">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
        </div>
    </div>
</div>
@endsection
