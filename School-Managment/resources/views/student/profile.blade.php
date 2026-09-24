@extends('layouts.public')

@section('title', 'Mi perfil - ZiberEibar')

@section('content')
<div class="student-page">
    <div class="student-page-header">
        <h1>Mi perfil</h1>
    </div>

    <div class="card profile-card">
        <div class="card-body" style="padding:2rem;">
            <div class="profile-header">
                <div class="profile-avatar">{{ $user->initials }}</div>
                <div class="profile-info">
                    <h2>{{ $user->full_name }}</h2>
                    <p>{{ $user->email }}</p>
                </div>
            </div>

            <div class="profile-details">
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Nombre</div>
                    <div class="profile-detail-value">{{ $user->name }}</div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Apellidos</div>
                    <div class="profile-detail-value">{{ $user->surname ?? '—' }}</div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Email</div>
                    <div class="profile-detail-value">{{ $user->email }}</div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">DNI</div>
                    <div class="profile-detail-value">{{ $user->dni ?? '—' }}</div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Teléfono</div>
                    <div class="profile-detail-value">{{ $user->phone ?? '—' }}</div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Cursos matriculados</div>
                    <div class="profile-detail-value">{{ $enrollmentCount }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
