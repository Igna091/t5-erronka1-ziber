@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-icon primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['total_students'] }}</div>
        <div class="stat-label">Total alumnos</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['registered_students'] }}</div>
        <div class="stat-label">Alumnos registrados</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon tertiary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a23.838 23.838 0 0 0-1.012 5.434c0 .043.016.086.044.124a23.77 23.77 0 0 0 4.454-1.601M4.26 10.147A23.96 23.96 0 0 1 12 8.443a23.96 23.96 0 0 1 7.74 1.704M4.26 10.147 12 6l7.74 4.147M12 6V3"/></svg>
        </div>
        <div class="stat-value">{{ $stats['total_courses'] }}</div>
        <div class="stat-label">Total cursos</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon neutral">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg>
        </div>
        <div class="stat-value">{{ $stats['total_enrollments'] }}</div>
        <div class="stat-label">Total matrículas</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    {{-- Recent Students --}}
    <div class="card">
        <div class="card-header">
            <h3>Últimos alumnos</h3>
            <a href="{{ route('admin.students.index') }}" class="btn btn-ghost btn-sm">Ver todos</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentStudents as $student)
                        <tr>
                            <td>
                                <div style="font-weight:500;">{{ $student->full_name }}</div>
                                <div class="text-xs text-muted">{{ $student->email }}</div>
                            </td>
                            <td>
                                @if($student->is_registered)
                                    <span class="badge badge-success">Registrado</span>
                                @else
                                    <span class="badge badge-warning">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted" style="padding:2rem;">Sin alumnos</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Enrollments --}}
    <div class="card">
        <div class="card-header">
            <h3>Últimas matrículas</h3>
            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-ghost btn-sm">Ver todas</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Curso</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentEnrollments as $enrollment)
                        <tr>
                            <td style="font-weight:500;">{{ $enrollment->student->full_name ?? '—' }}</td>
                            <td>
                                <span class="course-card-code">{{ $enrollment->course->code ?? '' }}</span>
                                {{ $enrollment->course->name ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted" style="padding:2rem;">Sin matrículas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Courses Overview --}}
<div class="card mt-3">
    <div class="card-header">
        <h3>Cursos disponibles</h3>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-ghost btn-sm">Ver todos</a>
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Plazas</th>
                    <th>Matriculados</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr>
                        <td><span class="course-card-code">{{ $course->code }}</span></td>
                        <td style="font-weight:500;">{{ $course->name }}</td>
                        <td>{{ $course->capacity ?? '∞' }}</td>
                        <td>{{ $course->enrollments_count }}</td>
                        <td>
                            @if($course->status === 'active')
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-neutral">Inactivo</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted" style="padding:2rem;">Sin cursos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
