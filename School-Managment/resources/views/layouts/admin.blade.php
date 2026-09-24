<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel') · Admin · ZiberEibar</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-oscuro.png') }}">
    <script src="{{ asset('js/theme-init.js') }}?v={{ filemtime(public_path('js/theme-init.js')) }}"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <a class="skip-link" href="#adminContent">Saltar al contenido</a>

    <div class="admin-layout">
        <div class="sidebar-overlay"></div>

        <aside class="sidebar" aria-label="Menú de administración">
            <button type="button" class="icon-btn sidebar-close" aria-label="Cerrar menú"><x-icon name="close" /></button>

            <a href="{{ route('admin.dashboard') }}" class="brand">
                <img src="{{ asset('images/logo-oscuro.png') }}" alt="" class="logo-on-dark">
                <img src="{{ asset('images/logo-claro.png') }}" alt="" class="logo-on-light">
                <span>ziber_eibar<span class="brand__suffix">/admin</span></span>
            </a>

            <nav class="sidebar-nav" aria-label="Secciones">
                <span class="sidebar-section-title">sistema</span>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif><span>~/panel</span></a>
                <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" @if(request()->routeIs('admin.students.*')) aria-current="page" @endif><span>~/alumnos</span><span class="count">{{ $adminCounts['students'] ?? '' }}</span></a>
                <a href="{{ route('admin.courses.index') }}" class="sidebar-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" @if(request()->routeIs('admin.courses.*')) aria-current="page" @endif><span>~/cursos</span><span class="count">{{ $adminCounts['courses'] ?? '' }}</span></a>
                <a href="{{ route('admin.enrollments.index') }}" class="sidebar-link {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}" @if(request()->routeIs('admin.enrollments.*')) aria-current="page" @endif><span>~/matrículas</span><span class="count">{{ $adminCounts['enrollments'] ?? '' }}</span></a>
            </nav>

            <nav class="sidebar-nav" aria-label="Atajos">
                <span class="sidebar-section-title">atajos</span>
                <a href="{{ route('admin.students.create') }}" class="sidebar-link sidebar-link--plus"><span>nuevo alumno</span></a>
                <a href="{{ route('admin.courses.create') }}" class="sidebar-link sidebar-link--plus"><span>nuevo curso</span></a>
                <a href="{{ route('admin.enrollments.create') }}" class="sidebar-link sidebar-link--plus"><span>matricular alumno</span></a>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('settings.index') }}" class="sidebar-link"><span>~/ajustes</span></a>
                <a href="{{ route('home') }}" class="sidebar-link"><span>ver web pública</span><x-icon name="external" :size="14" /></a>
                <div class="sidebar-user">
                    <span class="avatar">{{ auth()->user()->initials ?: 'A' }}</span>
                    <div class="sidebar-user__who">
                        <span>{{ auth()->user()->name }}</span>
                        <span class="lbl">administrador</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="icon-btn" aria-label="Cerrar sesión" style="border: 0;"><x-icon name="logout" /></button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-header">
                <div class="admin-header__left">
                    <button type="button" class="icon-btn sidebar-toggle" aria-label="Abrir menú"><x-icon name="menu" :size="20" /></button>
                    <span class="admin-path">admin@zibereibar:<strong>~/@yield('path', 'panel')</strong>$</span>
                </div>
                <div class="admin-header__right">
                    @yield('header_actions')
                    <button type="button" class="icon-btn theme-toggle" aria-label="Cambiar entre tema oscuro y claro">
                        <x-icon name="moon" class="icon-moon" />
                        <x-icon name="sun" class="icon-sun" />
                    </button>
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
            <h3 id="confirmTitle">Confirmar</h3>
            <p id="confirmMessage">¿Seguro que quieres continuar?</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="confirmCancel">cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmAccept">confirmar</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
