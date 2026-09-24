<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EduCenter - Centro de formación profesional. Cursos de calidad para impulsar tu futuro.">
    <title>@yield('title', 'EduCenter - Centro de Formación')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="{{ route('home') }}" class="navbar-brand">
                <span class="navbar-brand-icon">E</span>
                EduCenter
            </a>

            <button class="navbar-toggle" aria-label="Menú">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>

            <ul class="navbar-links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a></li>
                <li><a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">Cursos</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">Información</a></li>

                @guest
                    {{-- Not authenticated --}}
                @else
                    @if(auth()->user()->isStudent())
                        <li><a href="{{ route('student.enrollments') }}" class="{{ request()->routeIs('student.enrollments') ? 'active' : '' }}">Mis matrículas</a></li>
                        <li><a href="{{ route('student.profile') }}" class="{{ request()->routeIs('student.profile') ? 'active' : '' }}">Mi perfil</a></li>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}">Panel de administración</a></li>
                    @endif
                @endguest
            </ul>

            <div class="navbar-actions">
                <button class="theme-toggle" aria-label="Cambiar tema">
                    <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 8.002-4.248Z"/></svg>
                    <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
                </button>

                @guest
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Registrarse</a>
                @else
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">Cerrar sesión</button>
                    </form>
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
    @yield('content')

    {{-- Footer --}}
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3>EduCenter</h3>
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
                    <li><a href="mailto:info@educenter.es">info@educenter.es</a></li>
                    <li><a href="tel:+34900000000">900 000 000</a></li>
                    <li><a href="#">Calle Educación, 1</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} EduCenter. Todos los derechos reservados.
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
