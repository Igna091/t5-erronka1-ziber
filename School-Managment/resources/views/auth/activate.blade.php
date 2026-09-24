@extends('layouts.public')

@section('title', 'Activar cuenta - ZiberEibar')

@section('content')
<section class="container auth">
    <div class="auth__copy">
        <span class="kicker fx-fade">activación // cuenta de alumno</span>
        <h1 class="h-hero"><span class="fx-type">Hola, {{ $student->name }}.<span class="cursor" aria-hidden="true"></span></span></h1>
        <p class="lead fx-fade" style="--d: 0.9s">El centro te ha dado de alta. Elige tu contraseña para activar la cuenta; después podrás matricularte en los cursos.</p>
        <ol class="steps fx-fade" style="--d: 1.1s">
            <li class="is-done">el centro te da de alta</li>
            <li class="is-done">abres el enlace del email</li>
            <li class="is-now">eliges tu contraseña</li>
            <li>te matriculas en tus cursos</li>
        </ol>
    </div>

    <div class="panel panel--float auth__form fx-fade" style="--d: 0.3s">
        <x-pads />
        <div class="panel__row"><span class="lbl">zibereibar@eibar: ~/activar</span><span class="lbl">enlace de un solo uso</span></div>

        @if ($errors->any())
            <x-alert type="error" class="fx-shake">{{ $errors->first() }}</x-alert>
        @endif

        <form method="POST" action="{{ route('activation.store') }}" class="form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="email_display" class="form-label">email</label>
                <input type="email" id="email_display" class="form-input" value="{{ $email }}" readonly>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">contraseña</label>
                <div class="input-with-btn">
                    <input type="password" id="password" name="password" class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}" required autofocus autocomplete="new-password" placeholder="mínimo 8 caracteres" aria-describedby="password-rules" data-password>
                    <button type="button" class="input-btn" data-toggle-password="password" aria-pressed="false" aria-label="Mostrar u ocultar la contraseña">mostrar</button>
                </div>
            </div>
            <div class="form-group">
                <label for="password_confirmation" class="form-label">repite la contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required autocomplete="new-password" placeholder="la misma contraseña" data-password-confirm>
            </div>

            <div class="rules" id="password-rules" data-password-rules aria-live="polite">
                <div class="rules__head"><span class="lbl">requisitos</span><span class="ascii muted" data-rules-meter>[----------]</span></div>
                <span class="rule" data-rule="length">8 caracteres o más</span>
                <span class="rule" data-rule="letters">contiene letras</span>
                <span class="rule" data-rule="numbers">contiene números</span>
                <span class="rule" data-rule="match">las dos contraseñas coinciden</span>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Activar cuenta</button>
        </form>
    </div>
</section>
@endsection
