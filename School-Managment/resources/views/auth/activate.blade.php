@extends('layouts.public')

@section('title', __('Activar cuenta - ZiberEibar'))

@section('content')
<section class="container auth">
    <div class="auth__copy">
        <span class="kicker fx-fade">{{ __('activación // cuenta de alumno') }}</span>
        <h1 class="h-hero"><span class="fx-type">{{ __('Hola, :name.', ['name' => $student->name]) }}<span class="cursor" aria-hidden="true"></span></span></h1>
        <p class="lead fx-fade" style="--d: 0.9s">{{ __('El centro te ha dado de alta. Elige tu contraseña para activar la cuenta; después podrás matricularte en los cursos.') }}</p>
        <ol class="steps fx-fade" style="--d: 1.1s">
            <li class="is-done">{{ __('el centro te da de alta') }}</li>
            <li class="is-done">{{ __('abres el enlace del email') }}</li>
            <li class="is-now">{{ __('eliges tu contraseña') }}</li>
            <li>{{ __('te matriculas en tus cursos') }}</li>
        </ol>
    </div>

    <div class="panel panel--float auth__form fx-fade" style="--d: 0.3s">
        <x-pads />
        <div class="panel__row"><span class="lbl">{{ __('zibereibar@eibar: ~/activar') }}</span><span class="lbl">{{ __('enlace de un solo uso') }}</span></div>

        @if ($errors->any())
            <x-alert type="error" class="fx-shake">{{ $errors->first() }}</x-alert>
        @endif

        <form method="POST" action="{{ route('activation.store') }}" class="form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="email_display" class="form-label">{{ __('email') }}</label>
                <input type="email" id="email_display" class="form-input" value="{{ $email }}" readonly>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">{{ __('contraseña') }}</label>
                <div class="input-with-btn">
                    <input type="password" id="password" name="password" class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}" required autofocus autocomplete="new-password" placeholder="{{ __('mínimo 8 caracteres') }}" aria-describedby="password-rules" data-password>
                    <button type="button" class="input-btn" data-toggle-password="password" aria-pressed="false" aria-label="{{ __('Mostrar u ocultar la contraseña') }}">{{ __('mostrar') }}</button>
                </div>
            </div>
            <div class="form-group">
                <label for="password_confirmation" class="form-label">{{ __('repite la contraseña') }}</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required autocomplete="new-password" placeholder="{{ __('la misma contraseña') }}" data-password-confirm>
            </div>

            @include('partials.password-rules', ['id' => 'password-rules'])

            <button type="submit" class="btn btn-primary btn-block">{{ __('Activar cuenta') }}</button>

            {{-- New tab so the student doesn't lose what they typed --}}
            <p class="legal-note">
                {!! __('Al activar tu cuenta aceptas nuestra :privacy y los :terms del servicio.', [
                    'privacy' => '<a href="'.e(route('legal.privacy')).'" target="_blank" rel="noopener">'.e(__('política de privacidad')).'</a>',
                    'terms' => '<a href="'.e(route('legal.terms')).'" target="_blank" rel="noopener">'.e(__('términos y condiciones')).'</a>',
                ]) !!}
            </p>
        </form>
    </div>
</section>
@endsection
