@extends('layouts.admin')

@section('title', 'Alumnos')
@section('header', 'Alumnos')

@section('content')
<div class="page-header">
    <h1>Gestión de alumnos</h1>
    <div class="page-header-actions">
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Nuevo alumno
        </a>
    </div>
</div>

{{-- Search & Filters --}}
<form method="GET" action="{{ route('admin.students.index') }}" class="search-bar">
    <div class="search-input-wrapper">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        <input type="text" name="search" class="form-input" placeholder="Buscar por nombre, email o DNI..." value="{{ request('search') }}">
    </div>
    <select name="status" class="form-select" data-auto-submit aria-label="Filtrar por estado">
        <option value="">Todos los estados</option>
        <option value="registered" {{ request('status') === 'registered' ? 'selected' : '' }}>Registrados</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendientes</option>
    </select>
    <button type="submit" class="btn btn-secondary">Buscar</button>
</form>

{{-- Students Table --}}
<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Alumno</th>
                <th>DNI</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Fecha de alta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr>
                    <td>
                        <div style="font-weight:500;">{{ $student->full_name }}</div>
                        <div class="text-xs text-muted">{{ $student->email }}</div>
                    </td>
                    <td>{{ $student->dni ?? '—' }}</td>
                    <td>{{ $student->phone ?? '—' }}</td>
                    <td>
                        @if($student->is_registered)
                            <span class="badge badge-success">Registrado</span>
                        @else
                            <span class="badge badge-warning">Pendiente</span>
                        @endif
                    </td>
                    <td>{{ $student->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-ghost btn-icon" title="Ver">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            </a>
                            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-ghost btn-icon" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-icon" title="Eliminar" data-confirm="¿Estás seguro de que deseas eliminar a {{ $student->full_name }}? Esta acción no se puede deshacer." style="color:var(--danger);">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <h3>No se encontraron alumnos</h3>
                            <p>Crea un nuevo alumno para comenzar.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($students->hasPages())
    <div class="pagination">
        {{ $students->links() }}
    </div>
@endif
@endsection
