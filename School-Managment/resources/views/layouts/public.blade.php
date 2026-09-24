<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ZiberEibar - Centro de formación profesional. Cursos de calidad para impulsar tu futuro.">
    <title>@yield('title', 'ZiberEibar - Centro de Formación')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="public-layout">
    {{-- Navbar --}}
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="{{ route('home') }}" class="navbar-brand">
                <img src="{{ asset('images/logo-claro.png') }}" alt="ZiberEibar Logo" class="navbar-logo navbar-logo-light">
                <img src="{{ asset('images/logo-oscuro.png') }}" alt="ZiberEibar Logo" class="navbar-logo navbar-logo-dark">
                <span class="navbar-brand-text">ZiberEibar</span>
            </a>

            <button class="navbar-toggle" aria-label="Abrir menú" aria-expanded="false" id="mobileNavToggle">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>

            <ul class="navbar-links" id="navbarLinks">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a></li>
                <li><a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">Cursos</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">Información</a></li>

                @guest
                    <li class="mobile-only-auth"><a href="{{ route('settings.index') }}">Ajustes</a></li>
                    <li class="mobile-only-auth"><a href="{{ route('login') }}">Iniciar sesión</a></li>
                    <li class="mobile-only-auth"><a href="{{ route('register') }}">Registrarse</a></li>
                @else
                    @if(auth()->user()->isStudent())
                        <li><a href="{{ route('student.enrollments') }}" class="{{ request()->routeIs('student.enrollments') ? 'active' : '' }}">Mis matrículas</a></li>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Panel de administración</a></li>
                    @endif
                @endguest
            </ul>

            <div class="navbar-actions">
                <button class="theme-toggle" aria-label="Cambiar tema" title="Cambiar tema">
                    <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 8.002-4.248Z"/></svg>
                    <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
                </button>

                @guest
                    <a href="{{ route('settings.index') }}" class="navbar-icon-btn" title="Ajustes" aria-label="Ajustes">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm navbar-auth-btn">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm navbar-auth-btn">Registrarse</a>
                @else
                    {{-- User Dropdown with Name & Role for All Authenticated Users --}}
                    <div class="user-dropdown-wrapper">
                        <button class="user-dropdown-btn admin-user-btn" id="userMenuBtn" aria-expanded="false" aria-haspopup="true">
                            <span class="user-avatar">{{ auth()->user()->initials ?? 'U' }}</span>
                            <div class="admin-header-info">
                                <div class="admin-header-name">{{ auth()->user()->name }}</div>
                                <div class="admin-header-role">
                                    @if(auth()->user()->isAdmin())
                                        Administrador
                                    @elseif(auth()->user()->isTeacher())
                                        Profesor
                                    @else
                                        Estudiante
                                    @endif
                                </div>
                            </div>
                            <svg class="dropdown-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div class="user-dropdown-menu" id="userMenuDropdown" role="menu" aria-labelledby="userMenuBtn">
                            <div class="dropdown-header">
                                <div class="dropdown-user-name">{{ auth()->user()->full_name }}</div>
                                <div class="dropdown-user-role">
                                    @if(auth()->user()->isAdmin())
                                        Administrador
                                    @elseif(auth()->user()->isTeacher())
                                        Profesor
                                    @else
                                        Estudiante
                                    @endif
                                </div>
                                <div class="dropdown-user-email">{{ auth()->user()->email }}</div>
                            </div>

                            <div class="dropdown-divider"></div>

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item" role="menuitem">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                                    Panel de administración
                                </a>
                            @else
                                <a href="{{ route('student.profile') }}" class="dropdown-item {{ request()->routeIs('student.profile') ? 'active' : '' }}" role="menuitem">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                    Mi perfil
                                </a>
                                <a href="{{ route('student.enrollments') }}" class="dropdown-item {{ request()->routeIs('student.enrollments') ? 'active' : '' }}" role="menuitem">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                                    Mis matrículas
                                </a>
                            @endif

                            <a href="{{ route('settings.index') }}" class="dropdown-item {{ request()->routeIs('settings.index') ? 'active' : '' }}" role="menuitem">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                Ajustes
                            </a>

                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}" class="dropdown-logout-form">
                                @csrf
                                <button type="submit" class="dropdown-item dropdown-logout-btn" role="menuitem">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="max-width:1200px;margin:1rem auto;padding:0 1.5rem;">
            <div class="alert alert-success" data-auto-hide>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div style="max-width:1200px;margin:1rem auto;padding:0 1.5rem;">
            <div class="alert alert-error" data-auto-hide>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="main-content" id="mainContent">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-logo-row">
                    <img src="{{ asset('images/logo-claro.png') }}" alt="ZiberEibar Logo" class="navbar-logo navbar-logo-light footer-logo">
                    <img src="{{ asset('images/logo-oscuro.png') }}" alt="ZiberEibar Logo" class="navbar-logo navbar-logo-dark footer-logo">
                    <h3>ZiberEibar</h3>
                </div>
                <p>Centro de formación profesional comprometido con la excelencia educativa y el desarrollo integral de nuestros estudiantes.</p>
            </div>
            <div class="footer-section">
                <h4>Navegación</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Inicio</a></li>
                    <li><a href="{{ route('courses.index') }}">Cursos</a></li>
                    <li><a href="{{ route('about') }}">Información</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contacto</h4>
                <ul class="footer-links">
                    <li><a href="mailto:info@zibereibar.es">info@zibereibar.es</a></li>
                    <li><a href="tel:+34900000000">900 000 000</a></li>
                    <li><a href="#">Calle Educación, 1</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} ZiberEibar. Todos los derechos reservados.
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
