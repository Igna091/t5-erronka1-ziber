@extends('layouts.admin')

@section('title', __('Alumnos'))
@section('path', __('alumnos'))

@section('content')
<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">{{ __('Alumnos.') }}</span></h1>
        <span class="text-2">{{ trans_choice(':count alumno|:count alumnos', $students->total()) }}{{ request()->hasAny(['search', 'status']) ? ' '.__('con estos filtros') : '' }}</span>
    </div>
</div>

<form method="GET" action="{{ route('admin.students.index') }}" class="search-bar" role="search">
    <div class="search-input-wrapper">
        <label for="search" class="sr-only">{{ __('Buscar alumnos') }}</label>
        <input type="search" id="search" name="search" class="form-input" placeholder="{{ __('buscar por nombre, email o DNI…') }}" value="{{ request('search') }}">
    </div>
    <select name="status" class="form-select" data-auto-submit aria-label="{{ __('Filtrar por estado') }}">
        <option value="">{{ __('todos los estados') }}</option>
        <option value="registered" @selected(request('status') === 'registered')>{{ __('activados') }}</option>
        <option value="pending" @selected(request('status') === 'pending')>{{ __('pendientes') }}</option>
    </select>
    <button type="submit" class="btn btn-ghost">{{ __('buscar') }}</button>
</form>

<div class="table-wrapper fx-fade" style="--d: 0.15s">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('alumno') }}</th>
                <th>{{ __('dni') }}</th>
                <th>{{ __('teléfono') }}</th>
                <th>{{ __('estado') }}</th>
                <th>{{ __('alta') }}</th>
                <th><span class="sr-only">{{ __('acciones') }}</span></th>
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
                            <span class="status status--ok">{{ __('activado') }}</span>
                        @else
                            <span class="status status--pending">{{ __('pendiente') }}</span>
                        @endif
                    </td>
                    <td class="t-sub">{{ $student->created_at->format('d.m.Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-ghost btn-icon" aria-label="{{ __('Ver :name', ['name' => $student->full_name]) }}"><x-icon name="eye" :size="16" /></a>
                            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-ghost btn-icon" aria-label="{{ __('Editar :name', ['name' => $student->full_name]) }}"><x-icon name="edit" :size="16" /></a>
                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-icon" aria-label="{{ __('Eliminar :name', ['name' => $student->full_name]) }}" data-confirm="{{ __('¿Eliminar a :name? Se borrarán también sus matrículas y notas. Esta acción no se puede deshacer.', ['name' => $student->full_name]) }}"><x-icon name="trash" :size="16" /></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty">
                            <span class="empty__cmd">ls {{ __('alumnos') }}/ <span class="muted">— {{ __('sin resultados') }}</span></span>
                            <span>{{ request()->hasAny(['search', 'status']) ? __('Prueba con otra búsqueda.') : __('Crea el primer alumno para empezar.') }}</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $students->links() }}
@endsection
