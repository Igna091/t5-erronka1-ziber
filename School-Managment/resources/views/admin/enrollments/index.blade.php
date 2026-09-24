@extends('layouts.admin')

@section('title', 'Matrículas')
@section('header', 'Matrículas')

@section('content')
<div class="page-header">
    <h1>Gestión de matrículas</h1>
</div>

<form method="GET" action="{{ route('admin.enrollments.index') }}" class="search-bar">
    <div class="search-input-wrapper">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        <input type="text" name="search" class="form-input" placeholder="Buscar por alumno o curso..." value="{{ request('search') }}">
    </div>
    <select name="status" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
        <option value="">Todos los estados</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activas</option>
        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Canceladas</option>
    </select>
    <button type="submit" class="btn btn-secondary">Buscar</button>
</form>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Alumno</th>
                <th>Curso</th>
                <th>Fecha de matrícula</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $enrollment)
                <tr>
                    <td>
                        <div style="font-weight:500;">{{ $enrollment->student->full_name ?? '—' }}</div>
                        <div class="text-xs text-muted">{{ $enrollment->student->email ?? '' }}</div>
                    </td>
                    <td>
                        <span class="course-card-code">{{ $enrollment->course->code ?? '' }}</span>
                        {{ $enrollment->course->name ?? '—' }}
                    </td>
                    <td>{{ $enrollment->enrolled_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($enrollment->status === 'active')
                            <span class="badge badge-success">Activa</span>
                        @else
                            <span class="badge badge-danger">Cancelada</span>
                        @endif
                    </td>
                    <td>
                        @if($enrollment->status === 'active')
                            <form method="POST" action="{{ route('admin.enrollments.cancel', $enrollment) }}" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-sm" data-confirm="¿Estás seguro de que deseas cancelar esta matrícula?">Cancelar</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.enrollments.reactivate', $enrollment) }}" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm">Reactivar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <h3>No se encontraron matrículas</h3>
                            <p>Las matrículas aparecerán aquí cuando los alumnos se matriculen en cursos.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($enrollments->hasPages())
    <div class="pagination">
        {{ $enrollments->links() }}
    </div>
@endif
@endsection
