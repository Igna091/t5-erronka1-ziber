@extends('layouts.public')

@section('title', __('Ajustes - ZiberEibar'))

@section('content')
<div class="settings-page">
    <div class="container settings-container">
        {{-- Page Header --}}
        <div class="settings-header">
            <div class="settings-breadcrumb">
                <a href="{{ route('home') }}">{{ __('Inicio') }}</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">{{ __('Ajustes') }}</span>
            </div>
            <div class="settings-title-row">
                <div>
                    <h1 class="settings-title">{{ __('Ajustes de la plataforma') }}</h1>
                    <p class="settings-subtitle">@auth {{ __('Gestiona tu cuenta y tus preferencias de apariencia, tamaño de texto e idioma de navegación.') }} @else {{ __('Gestiona tus preferencias de apariencia, tamaño de texto e idioma de navegación.') }} @endauth</p>
                </div>
                @auth
                    <div class="user-badge-pill">
                        <span class="user-avatar-sm">{{ auth()->user()->initials ?? 'U' }}</span>
                        <div class="user-badge-info">
                            <span class="user-badge-name">{{ auth()->user()->name }}</span>
                            <span class="user-badge-role">
                                @if(auth()->user()->isAdmin())
                                    {{ __('Administrador') }}
                                @elseif(auth()->user()->isTeacher())
                                    {{ __('Profesor') }}
                                @else
                                    {{ __('Estudiante') }}
                                @endif
                            </span>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        {{-- Settings Feedback Toast --}}
        <div id="settingsToast" class="settings-toast" role="alert" aria-live="polite">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span id="settingsToastMessage">{{ __('Preferencia guardada con éxito') }}</span>
        </div>

        <div class="settings-grid">
            @auth
                {{-- Account & security: login email and password (AccountController) --}}
                <section class="card settings-card" id="seccion-cuenta">
                    <div class="settings-card-header">
                        <div class="settings-card-icon"><x-icon name="lock" :size="22" /></div>
                        <div>
                            <h2>{{ __('Cuenta y seguridad') }}</h2>
                            <p>{{ __('Cambia el email con el que inicias sesión y tu contraseña. Para confirmar cualquier cambio te pediremos tu contraseña actual.') }}</p>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <div class="setting-item">
                            <div class="setting-item-label">
                                <h3>{{ __('Email de acceso') }}</h3>
                                <p>{!! __('Ahora es :email. Te enviaremos un aviso a este email si se cambia.', ['email' => '<strong>'.e(auth()->user()->email).'</strong>']) !!}</p>
                            </div>
                            <form method="POST" action="{{ route('settings.email') }}" class="form">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="new_email" class="form-label">{{ __('nuevo email') }}</label>
                                    <input type="email" id="new_email" name="email" class="form-input @error('email', 'emailUpdate') is-invalid @enderror" value="{{ old('email') }}" required maxlength="255" autocomplete="email" placeholder="{{ __('tu@email.com') }}">
                                    @error('email', 'emailUpdate')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="email_current_password" class="form-label">{{ __('contraseña actual') }}</label>
                                    <div class="input-with-btn">
                                        <input type="password" id="email_current_password" name="current_password" class="form-input @error('current_password', 'emailUpdate') is-invalid @enderror" required autocomplete="current-password" placeholder="{{ __('para confirmar el cambio') }}">
                                        <button type="button" class="input-btn" data-toggle-password="email_current_password" aria-pressed="false" aria-label="{{ __('Mostrar u ocultar la contraseña') }}">{{ __('mostrar') }}</button>
                                    </div>
                                    @error('current_password', 'emailUpdate')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary btn-sm">{{ __('Cambiar email') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="setting-divider"></div>

                        <div class="setting-item">
                            <div class="setting-item-label">
                                <h3>{{ __('Contraseña') }}</h3>
                                <p>{{ __('Al cambiarla cerraremos tu sesión en los demás dispositivos y te avisaremos por email.') }}</p>
                            </div>
                            <form method="POST" action="{{ route('settings.password') }}" class="form">
                                @csrf
                                @method('PUT')
                                {{-- Lets password managers know which account this is --}}
                                <input type="email" class="sr-only" value="{{ auth()->user()->email }}" autocomplete="username" tabindex="-1" aria-hidden="true" readonly>
                                <div class="form-group">
                                    <label for="current_password" class="form-label">{{ __('contraseña actual') }}</label>
                                    <div class="input-with-btn">
                                        <input type="password" id="current_password" name="current_password" class="form-input @error('current_password', 'passwordUpdate') is-invalid @enderror" required autocomplete="current-password" placeholder="{{ __('tu contraseña actual') }}">
                                        <button type="button" class="input-btn" data-toggle-password="current_password" aria-pressed="false" aria-label="{{ __('Mostrar u ocultar la contraseña actual') }}">{{ __('mostrar') }}</button>
                                    </div>
                                    @error('current_password', 'passwordUpdate')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="new_password" class="form-label">{{ __('nueva contraseña') }}</label>
                                        <input type="password" id="new_password" name="password" class="form-input @error('password', 'passwordUpdate') is-invalid @enderror" required autocomplete="new-password" placeholder="{{ __('mínimo 8 caracteres') }}" aria-describedby="account-password-rules" data-password>
                                    </div>
                                    <div class="form-group">
                                        <label for="new_password_confirmation" class="form-label">{{ __('repite la nueva contraseña') }}</label>
                                        <input type="password" id="new_password_confirmation" name="password_confirmation" class="form-input" required autocomplete="new-password" placeholder="{{ __('la misma contraseña') }}" data-password-confirm>
                                    </div>
                                </div>
                                @error('password', 'passwordUpdate')<span class="form-error">{{ $message }}</span>@enderror
                                <div class="rules" id="account-password-rules" data-password-rules aria-live="polite">
                                    <div class="rules__head"><span class="lbl">{{ __('requisitos') }}</span><span class="ascii muted" data-rules-meter>[----------]</span></div>
                                    <span class="rule" data-rule="length">{{ __('8 caracteres o más') }}</span>
                                    <span class="rule" data-rule="letters">{{ __('contiene letras') }}</span>
                                    <span class="rule" data-rule="numbers">{{ __('contiene números') }}</span>
                                    <span class="rule" data-rule="match">{{ __('las dos contraseñas coinciden') }}</span>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary btn-sm">{{ __('Cambiar contraseña') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            @endauth

            {{-- Section 1: Apariencia & Visualización --}}
            <section class="card settings-card" id="seccion-apariencia">
                <div class="settings-card-header">
                    <div class="settings-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                        </svg>
                    </div>
                    <div>
                        <h2>{{ __('Apariencia y visualización') }}</h2>
                        <p>{{ __('Personaliza el aspecto de ZiberEibar para adaptar la plataforma a tus preferencias visuales.') }}</p>
                    </div>
                </div>

                <div class="settings-card-body">
                    {{-- Tema --}}
                    <div class="setting-item">
                        <div class="setting-item-label">
                            <h3>{{ __('Tema de la interfaz') }}</h3>
                            <p>{{ __('Elige entre modo claro u oscuro para la interfaz visual.') }}</p>
                        </div>
                        <div class="theme-picker-group">
                            <button type="button" class="theme-choice-card" data-theme-target="light" id="themeLightBtn">
                                <div class="theme-choice-preview theme-preview-light">
                                    <div class="preview-navbar"></div>
                                    <div class="preview-body">
                                        <div class="preview-line"></div>
                                        <div class="preview-line sm"></div>
                                    </div>
                                </div>
                                <div class="theme-choice-info">
                                    <span class="theme-choice-name">{{ __('Modo claro') }}</span>
                                    <span class="theme-choice-desc">{{ __('Fondo blanco y contrastes nítidos') }}</span>
                                </div>
                                <div class="theme-choice-check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                            </button>

                            <button type="button" class="theme-choice-card" data-theme-target="dark" id="themeDarkBtn">
                                <div class="theme-choice-preview theme-preview-dark">
                                    <div class="preview-navbar"></div>
                                    <div class="preview-body">
                                        <div class="preview-line"></div>
                                        <div class="preview-line sm"></div>
                                    </div>
                                </div>
                                <div class="theme-choice-info">
                                    <span class="theme-choice-name">{{ __('Modo oscuro') }}</span>
                                    <span class="theme-choice-desc">{{ __('Tonos oscuros que descansan la vista') }}</span>
                                </div>
                                <div class="theme-choice-check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div class="setting-divider"></div>

                    {{-- Tamaño del texto --}}
                    <div class="setting-item">
                        <div class="setting-item-label">
                            <h3>{{ __('Tamaño del texto') }}</h3>
                            <p>{{ __('Modifica el tamaño de la tipografía para adaptar la legibilidad en tus dispositivos.') }}</p>
                        </div>

                        <div class="font-size-options" role="radiogroup" aria-label="{{ __('Tamaño del texto') }}">
                            <label class="font-size-option" for="fontSizeSmall">
                                <input type="radio" name="fontSize" id="fontSizeSmall" value="small">
                                <div class="font-size-card">
                                    <span class="font-size-indicator" style="font-size: 0.8125rem; font-weight: 700;">Aa</span>
                                    <div class="font-size-meta">
                                        <span class="font-size-title">{{ __('Pequeño') }}</span>
                                        <span class="font-size-sub">{{ __('14px base') }}</span>
                                    </div>
                                </div>
                            </label>

                            <label class="font-size-option" for="fontSizeNormal">
                                <input type="radio" name="fontSize" id="fontSizeNormal" value="normal">
                                <div class="font-size-card">
                                    <span class="font-size-indicator" style="font-size: 1rem; font-weight: 700;">Aa</span>
                                    <div class="font-size-meta">
                                        <span class="font-size-title">{{ __('Normal') }}</span>
                                        <span class="font-size-sub">{{ __('16px (estándar)') }}</span>
                                    </div>
                                </div>
                            </label>

                            <label class="font-size-option" for="fontSizeLarge">
                                <input type="radio" name="fontSize" id="fontSizeLarge" value="large">
                                <div class="font-size-card">
                                    <span class="font-size-indicator" style="font-size: 1.25rem; font-weight: 700;">Aa</span>
                                    <div class="font-size-meta">
                                        <span class="font-size-title">{{ __('Grande') }}</span>
                                        <span class="font-size-sub">{{ __('18px base') }}</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        {{-- Live Preview Box --}}
                        <div class="font-size-preview-box">
                            <span class="preview-box-tag">{{ __('Vista previa interactiva') }}</span>
                            <p class="preview-box-text">
                                {{ __('ZiberEibar ofrece formación profesional y especializada diseñada para impulsar tu futuro laboral con las mejores competencias del sector.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section 2: Idioma y Región --}}
            <section class="card settings-card" id="seccion-idioma">
                <div class="settings-card-header">
                    <div class="settings-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
                        </svg>
                    </div>
                    <div>
                        <h2>{{ __('Idioma') }}</h2>
                        <p>{{ __('Selecciona el idioma en el que deseas navegar por la aplicación.') }}</p>
                    </div>
                </div>

                <div class="settings-card-body">
                    <div class="setting-item">
                        <div class="setting-item-label">
                            <h3>{{ __('Idioma principal') }}</h3>
                            <p>{{ __('Elige entre los idiomas disponibles en ZiberEibar.') }}</p>
                        </div>

                        <form method="POST" action="{{ route('settings.locale') }}" class="language-selector-wrap">
                            @csrf
                            <div class="language-select-wrapper">
                                <select id="languageSelect" name="locale" class="form-select language-select" aria-label="{{ __('Seleccionar idioma') }}" data-auto-submit>
                                    @foreach(config('app.available_locales') as $code => $language)
                                        <option value="{{ $code }}" lang="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $language }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" data-js-hide>{{ __('Guardar idioma') }}</button>
                            <span class="language-status-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                {{ __('Activo') }}
                            </span>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
