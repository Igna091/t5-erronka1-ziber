<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>@yield('code') · @yield('title') - ZiberEibar</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-oscuro.png') }}">
    <script src="{{ asset('js/theme-init.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <main class="error-page">
        <div class="error-page__box">
            <a href="{{ url('/') }}" class="brand" aria-label="{{ __('ZiberEibar, ir al inicio') }}">
                <img src="{{ asset('images/logo-oscuro.png') }}" alt="" class="logo-on-dark">
                <img src="{{ asset('images/logo-claro.png') }}" alt="" class="logo-on-light">
                <span>ziber_eibar</span>
            </a>
            <span class="error-code fx-type">@yield('code')<span class="cursor" aria-hidden="true"></span></span>
            <h1 class="h-section fx-fade" style="--d: 0.5s">@yield('title')</h1>
            <div class="terminal fx-fade" style="--d: 0.7s">
                <div class="terminal__bar"><span>zibereibar@eibar: ~/error</span><span>@yield('code')</span></div>
                <div class="terminal__body">
                    <div class="tl tl--dim" style="white-space: normal;">&gt; @yield('message')</div>
                </div>
            </div>
            <div class="form-actions fx-fade" style="--d: 0.9s">
                <a href="{{ url('/') }}" class="btn btn-primary">{{ __('Ir al inicio') }}</a>
                @yield('actions')
            </div>
        </div>
    </main>
</body>
</html>
