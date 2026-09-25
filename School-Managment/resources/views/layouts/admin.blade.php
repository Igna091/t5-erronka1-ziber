<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('Panel')) · Admin · ZiberEibar</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-oscuro.png') }}">
    <script src="{{ asset('js/theme-init.js') }}?v={{ filemtime(public_path('js/theme-init.js')) }}"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <a class="skip-link" href="#adminContent">{{ __('Saltar al contenido') }}</a>

    <div class="admin-layout">
        <div class="sidebar-overlay"></div>

        <aside class="sidebar" aria-label="{{ __('Menú de administración') }}">
            <button type="button" class="icon-btn sidebar-close" aria-label="{{ __('Cerrar menú') }}"><x-icon name="close" /></button>

            <a href="{{ route('admin.dashboard') }}" class="brand">
                <img src="{{ asset('images/logo-oscuro.png') }}" alt="" class="logo-on-dark">
                <img src="{{ asset('images/logo-claro.png') }}" alt="" class="logo-on-light">
                <span>ziber_eibar<span class="brand__suffix">/admin</span></span>
            </a>

            <nav class="sidebar-nav" aria-label="{{ __('Secciones') }}">
                <span class="sidebar-section-title">{{ __('sistema') }}</span>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif><x-icon name="dashboard" class="sidebar-link__icon" /><span class="sidebar-link__label">{{ __('panel') }}</span></a>
                <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" @if(request()->routeIs('admin.students.*')) aria-current="page" @endif><x-icon name="users" class="sidebar-link__icon" /><span class="sidebar-link__label">{{ __('alumnos') }}</span><span class="count">{{ $adminCounts['students'] ?? '' }}</span></a>
                <a href="{{ route('admin.courses.index') }}" class="sidebar-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" @if(request()->routeIs('admin.courses.*')) aria-current="page" @endif><x-icon name="book" class="sidebar-link__icon" /><span class="sidebar-link__label">{{ __('cursos') }}</span><span class="count">{{ $adminCounts['courses'] ?? '' }}</span></a>
                <a href="{{ route('admin.enrollments.index') }}" class="sidebar-link {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}" @if(request()->routeIs('admin.enrollments.*')) aria-current="page" @endif><x-icon name="clipboard" class="sidebar-link__icon" /><span class="sidebar-link__label">{{ __('matrículas') }}</span><span class="count">{{ $adminCounts['enrollments'] ?? '' }}</span></a>
            </nav>

            <nav class="sidebar-nav" aria-label="{{ __('Atajos') }}">
                <span class="sidebar-section-title">{{ __('atajos') }}</span>
                <a href="{{ route('admin.students.create') }}" class="sidebar-link sidebar-link--plus"><x-icon name="user-plus" class="sidebar-link__icon" /><span class="sidebar-link__label">{{ __('nuevo alumno') }}</span></a>
                <a href="{{ route('admin.courses.create') }}" class="sidebar-link sidebar-link--plus"><x-icon name="book-plus" class="sidebar-link__icon" /><span class="sidebar-link__label">{{ __('nuevo curso') }}</span></a>
                <a href="{{ route('admin.enrollments.create') }}" class="sidebar-link sidebar-link--plus"><x-icon name="clipboard-plus" class="sidebar-link__icon" /><span class="sidebar-link__label">{{ __('matricular alumno') }}</span></a>
                <a href="{{ route('admin.activations.index') }}" class="sidebar-link sidebar-link--plus {{ request()->routeIs('admin.activations.*') ? 'active' : '' }}" @if(request()->routeIs('admin.activations.*')) aria-current="page" @endif><x-icon name="mail-send" class="sidebar-link__icon" /><span class="sidebar-link__label">{{ __('reenviar activación') }}</span>@if (!empty($adminCounts['pending']))<span class="count count--warn" title="{{ __('alumnos pendientes de activar') }}">{{ $adminCounts['pending'] }}</span>@endif</a>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('home') }}" class="sidebar-link"><x-icon name="globe" class="sidebar-link__icon" /><span class="sidebar-link__label">{{ __('ver web pública') }}</span><x-icon name="external" :size="14" /></a>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-header">
                <div class="admin-header__left">
                    <button type="button" class="icon-btn sidebar-toggle" aria-label="{{ __('Abrir menú') }}"><x-icon name="menu" :size="20" /></button>
                    <span class="admin-path">admin <span class="crumbs__sep">/</span> <strong>{{ str_replace('/', ' / ', $__env->yieldContent('path', __('panel'))) }}</strong></span>
                </div>
                <div class="admin-header__right">
                    <button type="button" class="icon-btn theme-toggle" aria-label="{{ __('Cambiar entre tema oscuro y claro') }}">
                        <x-icon name="moon" class="icon-moon" />
                        <x-icon name="sun" class="icon-sun" />
                    </button>
                    @include('partials.user-menu')
                </div>
            </header>

            <main class="admin-content" id="adminContent" tabindex="-1">
                @if(session('success'))
                    <x-alert type="success" class="fx-fade" data-auto-hide>{{ session('success') }}</x-alert>
                @endif
                @if(session('error'))
                    <x-alert type="error" class="fx-fade">{{ session('error') }}</x-alert>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <div class="modal-overlay" id="confirmModal">
        <div class="modal" role="alertdialog" aria-modal="true" aria-labelledby="confirmTitle" aria-describedby="confirmMessage">
            <h3 id="confirmTitle">{{ __('Confirmar') }}</h3>
            <p id="confirmMessage">{{ __('¿Seguro que quieres continuar?') }}</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="confirmCancel">{{ __('cancelar') }}</button>
                <button type="button" class="btn btn-danger" id="confirmAccept">{{ __('confirmar') }}</button>
            </div>
        </div>
    </div>

    @include('partials.js-i18n')
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
