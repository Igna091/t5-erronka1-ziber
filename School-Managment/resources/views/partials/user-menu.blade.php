{{--
    User menu (top right) shared by the public and admin layouts.
    Hooks for app.js: .user-dropdown-wrapper / .user-dropdown-btn / .user-dropdown-menu
--}}
@php
    $user = auth()->user();
@endphp

<div class="user-dropdown-wrapper">
    <button type="button" class="user-dropdown-btn" id="userMenuBtn" aria-expanded="false" aria-haspopup="true" aria-controls="userMenuDropdown">
        <span class="avatar">{{ $user->initials ?: 'U' }}</span>
        <span class="user-dropdown-btn__who">
            <span class="user-dropdown-btn__name">{{ $user->name }}</span>
            <span class="lbl">
                @if ($user->isAdmin()) administrador
                @elseif ($user->isTeacher()) profesor/a
                @else estudiante
                @endif
            </span>
        </span>
        <x-icon name="chevron-down" :size="16" class="dropdown-chevron" />
    </button>

    <div class="user-dropdown-menu" id="userMenuDropdown" role="menu" aria-labelledby="userMenuBtn">
        <div class="dropdown-header">
            <span class="dropdown-user-name">{{ $user->full_name }}</span>
            <span class="dropdown-user-email">{{ $user->email }}</span>
        </div>
        <div class="dropdown-divider"></div>

        @if ($user->isStudent())
            <a href="{{ route('student.profile') }}" class="dropdown-item {{ request()->routeIs('student.profile') ? 'active' : '' }}" role="menuitem"><x-icon name="users" :size="16" />mi perfil</a>
            <a href="{{ route('student.enrollments') }}" class="dropdown-item {{ request()->routeIs('student.enrollments') ? 'active' : '' }}" role="menuitem"><x-icon name="clipboard" :size="16" />mis matrículas</a>
        @endif
        <a href="{{ route('settings.index') }}" class="dropdown-item {{ request()->routeIs('settings.index') ? 'active' : '' }}" role="menuitem"><x-icon name="settings" :size="16" />ajustes</a>

        <div class="dropdown-divider"></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item dropdown-item--danger" role="menuitem"><x-icon name="logout" :size="16" />cerrar sesión</button>
        </form>
    </div>
</div>
