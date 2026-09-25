@extends('layouts.admin')

@section('title', 'Cursos')
@section('path', 'cursos')

@section('content')
<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">Cursos.</span></h1>
        <span class="text-2">{{ $courses->total() }} {{ $courses->total() === 1 ? 'curso' : 'cursos' }}{{ request()->hasAny(['search', 'status']) ? ' con estos filtros' : '' }}</span>
    </div>
</div>

<form method="GET" action="{{ route('admin.courses.index') }}" class="search-bar" role="search">
    <div class="search-input-wrapper">
        <label for="search" class="sr-only">Buscar cursos</label>
        <input type="search" id="search" name="search" class="form-input" placeholder="buscar por nombre o código…" value="{{ request('search') }}">
    </div>
    <select name="status" class="form-select" data-auto-submit aria-label="Filtrar por estado">
        <option value="">todos los estados</option>
        <option value="active" @selected(request('status') === 'active')>activos</option>
        <option value="inactive" @selected(request('status') === 'inactive')>inactivos</option>
    </select>
    <button type="submit" class="btn btn-ghost">buscar</button>
</form>

<div class="table-wrapper fx-fade" style="--d: 0.15s">
    <table class="table">
        <thead>
            <tr>
                <th>código</th>
                <th>curso</th>
                <th>duración</th>
                <th>ocupación</th>
                <th>estado</th>
                <th><span class="sr-only">acciones</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($courses as $course)
                @php
                    $used = $course->enrollments_count;
                    $lit = $course->capacity ? ($used > 0 ? max(1, (int) round($used / $course->capacity * 12)) : 0) : 0;
                @endphp
                <tr>
                    <td class="t-code">{{ $course->code }}</td>
                    <td>
                        <a href="{{ route('admin.courses.show', $course) }}" style="display: flex; flex-direction: column; color: var(--text); text-decoration: none;">
                            <span class="t-main">{{ $course->name }}</span>
                            <span class="t-sub">{{ $course->academic_year_label ? 'curso '.$course->academic_year_label : 'sin fecha de inicio' }}</span>
                        </a>
                    </td>
                    <td>{{ $course->duration_hours ? $course->duration_hours.' h' : '—' }}</td>
                    <td>
                        <span style="display: flex; align-items: center; gap: 0.75rem;">
                            <span class="segbar" aria-hidden="true">
                                @for ($i = 0; $i < 12; $i++)
                                    <i class="seg {{ $i < $lit ? 'is-on' : '' }}" style="width: 7px; height: 14px;"></i>
                                @endfor
                            </span>
                            <span class="t-sub" style="color: var(--text-2);">{{ $used }}/{{ $course->capacity ?? '∞' }}</span>
                        </span>
                    </td>
                    <td>
                        @if ($course->isActive())
                            <span class="status status--ok">activo</span>
                        @else
                            <span class="status status--off">inactivo</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-ghost btn-icon" aria-label="Ver {{ $course->name }}"><x-icon name="eye" :size="16" /></a>
                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-ghost btn-icon" aria-label="Editar {{ $course->name }}"><x-icon name="edit" :size="16" /></a>
                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-icon" aria-label="Eliminar {{ $course->name }}" data-confirm="¿Eliminar el curso «{{ $course->name }}»? No se puede eliminar si tiene matrículas activas."><x-icon name="trash" :size="16" /></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty">
                            <span class="empty__cmd">ls cursos/ <span class="muted">— sin resultados</span></span>
                            <span>{{ request()->hasAny(['search', 'status']) ? 'Prueba con otra búsqueda.' : 'Crea el primer curso para empezar.' }}</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $courses->links() }}
@endsection
