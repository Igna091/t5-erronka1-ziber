@extends('layouts.admin')

@section('title', 'Alumnos')
@section('path', 'alumnos')

@section('content')
<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">Alumnos.</span></h1>
        <span class="text-2">{{ $students->total() }} {{ $students->total() === 1 ? 'alumno' : 'alumnos' }}{{ request()->hasAny(['search', 'status']) ? ' con estos filtros' : '' }}</span>
    </div>
</div>

<form method="GET" action="{{ route('admin.students.index') }}" class="search-bar" role="search">
    <div class="search-input-wrapper">
        <label for="search" class="sr-only">Buscar alumnos</label>
        <input type="search" id="search" name="search" class="form-input" placeholder="buscar por nombre, email o DNI…" value="{{ request('search') }}">
    </div>
    <select name="status" class="form-select" data-auto-submit aria-label="Filtrar por estado">
        <option value="">todos los estados</option>
        <option value="registered" @selected(request('status') === 'registered')>activados</option>
        <option value="pending" @selected(request('status') === 'pending')>pendientes</option>
    </select>
    <button type="submit" class="btn btn-ghost">buscar</button>
</form>

<div class="table-wrapper fx-fade" style="--d: 0.15s">
    <table class="table">
        <thead>
            <tr>
                <th>alumno</th>
                <th>dni</th>
                <th>teléfono</th>
                <th>estado</th>
                <th>alta</th>
                <th><span class="sr-only">acciones</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr>
                    <td>
                        <a href="{{ route('admin.students.show', $student) }}" style="display: flex; align-items: center; gap: 0.875rem; color: var(--text); text-decoration: none;">
                            <span class="avatar avatar--muted {{ $student->is_registered ? '' : 'avatar--warn' }}">{{ $student->initials }}</span>
                            <span style="display: flex; flex-direction: column;"><span class="t-main">{{ $student->full_name }}</span><span class="t-sub">{{ $student->email }}</span></span>
                        </a>
                    </td>
                    <td>{{ $student->dni ?? '—' }}</td>
                    <td>{{ $student->phone ?? '—' }}</td>
                    <td>
                        @if ($student->is_registered)
                            <span class="status status--ok">activado</span>
                        @else
                            <span class="status status--pending">pendiente</span>
                        @endif
                    </td>
                    <td class="t-sub">{{ $student->created_at->format('d.m.Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-ghost btn-icon" aria-label="Ver {{ $student->full_name }}"><x-icon name="eye" :size="16" /></a>
                            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-ghost btn-icon" aria-label="Editar {{ $student->full_name }}"><x-icon name="edit" :size="16" /></a>
                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-icon" aria-label="Eliminar {{ $student->full_name }}" data-confirm="¿Eliminar a {{ $student->full_name }}? Se borrarán también sus matrículas y notas. Esta acción no se puede deshacer."><x-icon name="trash" :size="16" /></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty">
                            <span class="empty__cmd">ls alumnos/ <span class="muted">— sin resultados</span></span>
                            <span>{{ request()->hasAny(['search', 'status']) ? 'Prueba con otra búsqueda.' : 'Crea el primer alumno para empezar.' }}</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $students->links() }}
@endsection
