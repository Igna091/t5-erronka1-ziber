@extends('layouts.admin')

@section('title', $course->name)
@section('header', 'Detalle del curso')

@section('content')
<div class="page-header">
    <h1>{{ $course->name }}</h1>
    <div class="page-header-actions">
        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-secondary btn-sm">Editar</a>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-ghost btn-sm">Volver</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;max-width:900px;">
    <div class="card">
        <div class="card-header">
            <h3>Información del curso</h3>
            @if($course->status === 'active')
                <span class="badge badge-success">Activo</span>
            @else
                <span class="badge badge-neutral">Inactivo</span>
            @endif
        </div>
        <div class="card-body">
            <div style="display:grid;gap:0.75rem;">
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Código</div>
                    <div class="profile-detail-value">{{ $course->code }}</div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Descripción</div>
                    <div class="profile-detail-value">{{ $course->description ?? '—' }}</div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Duración</div>
                    <div class="profile-detail-value">{{ $course->duration_hours ? $course->duration_hours . ' horas' : '—' }}</div>
                </div>
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Plazas</div>
                    <div class="profile-detail-value">{{ $course->enrollments_count }} / {{ $course->capacity ?? '∞' }}</div>
                </div>
                @if($course->start_date)
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Fecha de inicio</div>
                    <div class="profile-detail-value">{{ $course->start_date->format('d/m/Y') }}</div>
                </div>
                @endif
                @if($course->end_date)
                <div class="profile-detail-item">
                    <div class="profile-detail-label">Fecha de fin</div>
                    <div class="profile-detail-value">{{ $course->end_date->format('d/m/Y') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Alumnos matriculados</h3>
        </div>
        @if($course->enrollments->count() > 0)
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($course->enrollments as $enrollment)
                            <tr>
                                <td>
                                    <div style="font-weight:500;">{{ $enrollment->student->full_name ?? '—' }}</div>
                                    <div class="text-xs text-muted">{{ $enrollment->student->email ?? '' }}</div>
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
                <p class="text-muted text-sm">No hay alumnos matriculados en este curso.</p>
            </div>
        @endif
    </div>
</div>
@endsection
