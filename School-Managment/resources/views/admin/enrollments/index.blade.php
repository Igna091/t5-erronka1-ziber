@extends('layouts.admin')

@section('title', __('Matrículas'))
@section('path', __('matrículas'))

@section('content')
<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">{{ __('Matrículas.') }}</span></h1>
        <span class="text-2">{{ trans_choice(':count matrícula|:count matrículas', $enrollments->total()) }}{{ request()->hasAny(['search', 'status']) ? ' '.__('con estos filtros') : '' }}</span>
    </div>
</div>

<form method="GET" action="{{ route('admin.enrollments.index') }}" class="search-bar" role="search">
    <div class="search-input-wrapper">
        <label for="search" class="sr-only">{{ __('Buscar matrículas') }}</label>
        <input type="search" id="search" name="search" class="form-input" placeholder="{{ __('buscar por alumno o curso…') }}" value="{{ request('search') }}">
    </div>
    <select name="status" class="form-select" data-auto-submit aria-label="{{ __('Filtrar por estado') }}">
        <option value="">{{ __('todos los estados') }}</option>
        <option value="active" @selected(request('status') === 'active')>{{ __('activas') }}</option>
        <option value="cancelled" @selected(request('status') === 'cancelled')>{{ __('canceladas') }}</option>
    </select>
    <button type="submit" class="btn btn-ghost">{{ __('buscar') }}</button>
</form>

<div class="table-wrapper fx-fade" style="--d: 0.15s">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('alumno') }}</th>
                <th>{{ __('curso') }}</th>
                <th>{{ __('fecha') }}</th>
                <th>{{ __('estado') }}</th>
                <th><span class="sr-only">{{ __('acciones') }}</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($enrollments as $enrollment)
                <tr>
                    <td>
                        @if ($enrollment->student)
                            <a href="{{ route('admin.students.show', $enrollment->student) }}" style="display: flex; flex-direction: column; color: var(--text); text-decoration: none;">
                                <span class="t-main">{{ $enrollment->student->full_name }}</span>
                                <span class="t-sub">{{ $enrollment->student->email }}</span>
                            </a>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if ($enrollment->course)
                            <a href="{{ route('admin.courses.show', $enrollment->course) }}" style="color: var(--text); text-decoration: none;"><span class="t-code">{{ $enrollment->course->code }}</span>&nbsp;&nbsp;{{ $enrollment->course->name }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td class="t-sub">{{ $enrollment->enrolled_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if ($enrollment->status === 'active')
                            <span class="status status--ok">{{ __('activa') }}</span>
                        @else
                            <span class="status status--off">{{ __('cancelada') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            @if ($enrollment->status === 'active')
                                <form method="POST" action="{{ route('admin.enrollments.cancel', $enrollment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="{{ __('¿Cancelar la matrícula de :student en «:course»? Sus notas se conservan.', ['student' => $enrollment->student->full_name ?? __('este alumno'), 'course' => $enrollment->course->name ?? __('este curso')]) }}">{{ __('cancelar') }}</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.enrollments.reactivate', $enrollment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-ghost btn-sm">{{ __('reactivar') }}</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty">
                            <span class="empty__cmd">ls {{ __('matrículas') }}/ <span class="muted">— {{ __('sin resultados') }}</span></span>
                            <span>{{ request()->hasAny(['search', 'status']) ? __('Prueba con otra búsqueda.') : __('Las matrículas aparecerán aquí.') }}</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $enrollments->links() }}
@endsection
