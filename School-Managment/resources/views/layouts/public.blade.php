<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', __('ZiberEibar, centro de formación profesional en Eibar: desarrollo web, diseño UX/UI, sistemas, ciberseguridad e inteligencia artificial.'))">
    <title>@yield('title', __('ZiberEibar - Centro de formación profesional'))</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-oscuro.png') }}">
    <script src="{{ asset('js/theme-init.js') }}?v={{ filemtime(public_path('js/theme-init.js')) }}"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <a class="skip-link" href="#mainContent">{{ __('Saltar al contenido') }}</a>
    <div class="scanline" aria-hidden="true"></div>

    <div class="site">
        <header class="topnav">
            <div class="topnav__inner">
                <a href="{{ route('home') }}" class="brand" aria-label="{{ __('ZiberEibar, ir al inicio') }}">
                    <img src="{{ asset('images/logo-oscuro.png') }}" alt="" class="logo-on-dark">
                    <img src="{{ asset('images/logo-claro.png') }}" alt="" class="logo-on-light">
                    <span>ziber_eibar<span class="cursor" aria-hidden="true"></span></span>
                </a>

                <nav aria-label="{{ __('Principal') }}">
                    <div class="navbar-links" id="navbarLinks">
                        <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif><x-icon name="home" :size="16" />{{ __('inicio') }}</a>
                        <a href="{{ route('courses.index') }}" @if(request()->routeIs('courses.*')) aria-current="page" @endif><x-icon name="book" :size="16" />{{ __('cursos') }}</a>
                        <a href="{{ route('about') }}" @if(request()->routeIs('about')) aria-current="page" @endif><x-icon name="info" :size="16" />{{ __('información') }}</a>
                        @auth
                            @if(auth()->user()->isStudent())
                                <a href="{{ route('student.enrollments') }}" @if(request()->routeIs('student.enrollments')) aria-current="page" @endif><x-icon name="clipboard" :size="16" />{{ __('mis matrículas') }}</a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"><x-icon name="dashboard" :size="16" />{{ __('administración') }}</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="mobile-only"><x-icon name="login" :size="16" />{{ __('iniciar sesión') }}</a>
                            <a href="{{ route('register') }}" class="mobile-only"><x-icon name="mail" :size="16" />{{ __('activar cuenta') }}</a>
                            <a href="{{ route('settings.index') }}" class="mobile-only"><x-icon name="settings" :size="16" />{{ __('ajustes') }}</a>
                        @endauth
                    </div>
                </nav>

                <div class="topnav__actions">
                    <button type="button" class="icon-btn theme-toggle" aria-label="{{ __('Cambiar entre tema oscuro y claro') }}">
                        <x-icon name="moon" class="icon-moon" />
                        <x-icon name="sun" class="icon-sun" />
                    </button>

                    @guest
                        <a href="{{ route('settings.index') }}" class="icon-btn hide-mobile" aria-label="{{ __('Ajustes') }}"><x-icon name="settings" /></a>
                        <a href="{{ route('register') }}" class="link-quiet hide-mobile">{{ __('activar cuenta') }}</a>
                        <a href="{{ route('login') }}" class="btn btn-outline btn-bracket btn-sm hide-sm" @if(request()->routeIs('login')) aria-current="page" @endif>{{ __('iniciar sesión') }}</a>
                    @else
                        @include('partials.user-menu')
                    @endguest

                    <button type="button" class="icon-btn navbar-toggle" aria-label="{{ __('Abrir menú') }}" aria-expanded="false" aria-controls="navbarLinks">
                        <x-icon name="menu" :size="22" />
                    </button>
                </div>
            </div>
        </header>

        @hasSection('no_flash')
        @else
            @if(session('success') || session('error'))
                <div class="container flash-stack">
                    @if(session('success'))
                        <x-alert type="success" class="fx-fade" data-auto-hide>{{ session('success') }}</x-alert>
                    @endif
                    @if(session('error'))
                        <x-alert type="error" class="fx-fade">{{ session('error') }}</x-alert>
                    @endif
                </div>
            @endif
        @endif

        <main id="mainContent" tabindex="-1">
            @yield('content')
        </main>

        <footer class="footer">
            <div class="footer__inner">
                <div class="footer__col">
                    <a href="{{ route('home') }}" class="brand">
                        <img src="{{ asset('images/logo-oscuro.png') }}" alt="" class="logo-on-dark">
                        <img src="{{ asset('images/logo-claro.png') }}" alt="" class="logo-on-light">
                        <span>ziber_eibar</span>
                    </a>
                    <span>{{ __('Centro de formación profesional · Eibar, Gipuzkoa') }}</span>
                </div>
                <div class="footer__col">
                    <span class="lbl">{{ __('contacto') }}</span>
                    <a href="mailto:info@zibereibar.eus">info@zibereibar.eus</a>
                    <a href="tel:+34900000000">900 000 000</a>
                    <span>{{ __('Calle Educación, 1') }}</span>
                </div>
                <div class="footer__col">
                    <span class="lbl">{{ __('navegación') }}</span>
                    <a href="{{ route('courses.index') }}">{{ __('cursos') }}</a>
                    <a href="{{ route('about') }}">{{ __('información') }}</a>
                    <a href="{{ route('settings.index') }}">{{ __('ajustes') }}</a>
                </div>
                <div class="footer__col">
                    <span class="lbl">{{ __('legal') }}</span>
                    <a href="{{ route('legal.privacy') }}">{{ __('privacidad') }}</a>
                    <a href="{{ route('legal.terms') }}">{{ __('términos') }}</a>
                    <span>&copy; {{ date('Y') }} ZiberEibar</span>
                </div>
            </div>
        </footer>
    </div>

    @include('partials.js-i18n')
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
