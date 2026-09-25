@extends('layouts.admin')

@section('title', 'Matrículas')
@section('path', 'matrículas')

@section('content')
<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">Matrículas.</span></h1>
        <span class="text-2">{{ $enrollments->total() }} {{ $enrollments->total() === 1 ? 'matrícula' : 'matrículas' }}{{ request()->hasAny(['search', 'status']) ? ' con estos filtros' : '' }}</span>
    </div>
</div>

<form method="GET" action="{{ route('admin.enrollments.index') }}" class="search-bar" role="search">
    <div class="search-input-wrapper">
        <label for="search" class="sr-only">Buscar matrículas</label>
        <input type="search" id="search" name="search" class="form-input" placeholder="buscar por alumno o curso…" value="{{ request('search') }}">
    </div>
    <select name="status" class="form-select" data-auto-submit aria-label="Filtrar por estado">
        <option value="">todos los estados</option>
        <option value="active" @selected(request('status') === 'active')>activas</option>
        <option value="cancelled" @selected(request('status') === 'cancelled')>canceladas</option>
    </select>
    <button type="submit" class="btn btn-ghost">buscar</button>
</form>

<div class="table-wrapper fx-fade" style="--d: 0.15s">
    <table class="table">
        <thead>
            <tr>
                <th>alumno</th>
                <th>curso</th>
                <th>fecha</th>
                <th>estado</th>
                <th><span class="sr-only">acciones</span></th>
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
                            <span class="status status--ok">activa</span>
                        @else
                            <span class="status status--off">cancelada</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            @if ($enrollment->status === 'active')
                                <form method="POST" action="{{ route('admin.enrollments.cancel', $enrollment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="¿Cancelar la matrícula de {{ $enrollment->student->full_name ?? 'este alumno' }} en «{{ $enrollment->course->name ?? 'este curso' }}»? Sus notas se conservan.">cancelar</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.enrollments.reactivate', $enrollment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-ghost btn-sm">reactivar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty">
                            <span class="empty__cmd">ls matrículas/ <span class="muted">— sin resultados</span></span>
                            <span>{{ request()->hasAny(['search', 'status']) ? 'Prueba con otra búsqueda.' : 'Las matrículas aparecerán aquí.' }}</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $enrollments->links() }}
@endsection
