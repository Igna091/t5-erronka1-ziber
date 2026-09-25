@extends('layouts.admin')

@section('title', $course->name)
@section('path', __('cursos').'/'.$course->id)

@section('content')
<a href="{{ route('admin.courses.index') }}" class="back-link"><x-icon name="arrow-left" :size="16" />{{ __('volver a cursos') }}</a>

<div class="page-header">
    <div class="page-header__title">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem 1.125rem;" class="small">
            <span class="code-chip">{{ $course->code }}</span>
            @if ($course->isActive())
                <span class="status status--ok">{{ __('activo · admite matrículas') }}</span>
            @else
                <span class="status status--off">{{ __('inactivo · oculto al público') }}</span>
            @endif
            @if ($course->academic_year_label)
                <span class="muted">{{ __('curso :year', ['year' => $course->academic_year_label]) }}</span>
            @endif
        </div>
        <h1 class="h-admin" style="font-size: clamp(2rem, 3.6vw, 3.25rem);"><span class="fx-type">{{ $course->name }}</span></h1>
    </div>
    <div class="page-header-actions">
        @if ($course->isActive())
            <a href="{{ route('courses.show', $course) }}" class="btn btn-ghost"><x-icon name="external" :size="16" />{{ __('ver en la web') }}</a>
        @endif
        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-ghost"><x-icon name="edit" :size="16" />{{ __('editar') }}</a>
        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" data-confirm="{{ __('¿Eliminar el curso «:course»? No se puede eliminar si tiene matrículas activas.', ['course' => $course->name]) }}"><x-icon name="trash" :size="16" />{{ __('eliminar') }}</button>
        </form>
    </div>
</div>

<div class="specs fx-fade" style="--d: 0.2s; background: var(--panel);">
    <div class="spec"><span class="lbl">{{ __('duración') }}</span><span class="spec__value">{{ $course->duration_hours ? $course->duration_hours.' h' : '—' }}</span></div>
    <div class="spec"><span class="lbl">{{ __('ocupación') }}</span><span class="spec__value">{{ $course->enrollments_count }} / {{ $course->capacity ?? '∞' }}</span></div>
    <div class="spec"><span class="lbl">{{ __('inicio') }}</span><span class="spec__value">{{ $course->start_date?->format('d.m.Y') ?? '—' }}</span></div>
    <div class="spec"><span class="lbl">{{ __('fin') }}</span><span class="spec__value">{{ $course->end_date?->format('d.m.Y') ?? '—' }}</span></div>
</div>

<div class="grid-2 grid-2--wide-right">
    <section class="panel fx-fade" style="--d: 0.3s">
        <div class="panel__head"><h2 class="lbl">{{ __('descripción') }}</h2></div>
        <div class="panel__body">
            <p class="text-2" style="line-height: 1.75;">{{ $course->description ?? __('Sin descripción.') }}</p>
            @if ($course->capacity && $course->capacity <= 60)
                <div class="stack-sm">
                    <span class="lbl">{{ __('plazas libres: :count', ['count' => $course->available_spots]) }}</span>
                    <div class="seats seats--sm" aria-hidden="true">
                        @for ($i = 0; $i < $course->capacity; $i++)
                            <i class="seat {{ $i < $course->available_spots ? 'is-free' : '' }}" style="--d: {{ number_format(0.4 + 0.015 * $i, 3) }}s"></i>
                        @endfor
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="panel fx-fade" style="--d: 0.4s">
        <div class="panel__head">
            <h2 class="lbl">{{ __('alumnos matriculados (:count)', ['count' => $course->enrollments->count()]) }}</h2>
            @if ($course->isActive())
                <a href="{{ route('admin.enrollments.create', ['course_id' => $course->id]) }}" class="btn btn-outline btn-sm"><x-icon name="plus" :size="14" />{{ __('matricular alumno') }}</a>
            @endif
        </div>
        @if ($course->enrollments->isNotEmpty())
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead><tr><th>{{ __('alumno') }}</th><th>{{ __('estado') }}</th><th><span class="sr-only">{{ __('acciones') }}</span></th></tr></thead>
                    <tbody>
                        @foreach ($course->enrollments as $enrollment)
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
                                                <button type="submit" class="btn btn-danger btn-sm" data-confirm="{{ __('¿Cancelar la matrícula de :name? Sus notas se conservan.', ['name' => $enrollment->student->full_name ?? __('este alumno')]) }}">{{ __('cancelar') }}</button>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty">
                <span class="empty__cmd">ls {{ __('matriculados') }}/ <span class="muted">— {{ __('vacío') }}</span></span>
                <span>{{ __('Nadie se ha matriculado todavía.') }}</span>
            </div>
        @endif
    </section>
</div>
@endsection
