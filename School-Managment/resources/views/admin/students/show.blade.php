@extends('layouts.admin')

@section('title', $student->full_name)
@section('header', 'Detalle del alumno')

@section('content')
<div class="page-header">
    <h1>{{ $student->full_name }}</h1>
    <div class="page-header-actions">
        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
            Editar
        </a>
        <a href="{{ route('admin.students.index') }}" class="btn btn-ghost btn-sm">Volver</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;max-width:900px;">
    {{-- Student Info --}}
    <div class="card">
        <div class="card-header">
            <h3>Información del alumno</h3>
        </div>
        <div class="card-body">
            <div class="profile-header" style="margin-bottom:1.5rem;">
                <div class="profile-avatar">{{ $student->initials }}</div>
                <div class="profile-info">
                    <h2 style="font-size:1.25rem;">{{ $student->full_name }}</h2>
                    <p>{{ $student->email }}</p>
                </div>
            </div>

            <div style="display:grid;gap:0.75rem;">
                <div class="profile-detail-item">
                    <div class="profile-detail-label">DNI</div>
                    <div class="profile-detail-value">{{ $student->dni ?? '—' }}</div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Teléfono</div>
                    <div class="profile-detail-value">{{ $student->phone ?? '—' }}</div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Estado</div>
                    <div class="profile-detail-value">
                        @if($student->is_registered)
                            <span class="badge badge-success">Registrado</span>
                        @else
                            <span class="badge badge-warning">Pendiente de registro</span>
                        @endif
                    </div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Fecha de alta</div>
                    <div class="profile-detail-value">{{ $student->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Enrollments --}}
    <div class="card">
        <div class="card-header">
            <h3>Matrículas ({{ $student->enrollments->count() }})</h3>
        </div>
        @if($student->enrollments->count() > 0)
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->enrollments as $enrollment)
                            <tr>
                                <td>
                                    <span class="course-card-code">{{ $enrollment->course->code }}</span>
                                    <span style="font-weight:500;">{{ $enrollment->course->name }}</span>
                                </td>
                                <td>
                                    @if($enrollment->status === 'active')
                                        <span class="badge badge-success">Activa</span>
                                    @else
                                        <span class="badge badge-danger">Cancelada</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-body">
                <p class="text-muted text-sm">Este alumno no tiene matrículas.</p>
            </div>
        @endif
    </div>
</div>
@endsection
