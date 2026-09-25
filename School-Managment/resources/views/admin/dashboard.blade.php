@extends('layouts.admin')

@section('title', __('Panel'))
@section('path', __('panel'))

@section('content')
<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">{{ __('Panel.') }}<span class="cursor" aria-hidden="true"></span></span></h1>
        <span class="text-2">{{ __('Resumen del centro') }}</span>
    </div>
</div>

<div class="stats-grid">
    <div class="stat fx-fade" style="--d: 0.1s">
        <span class="lbl">{{ __('alumnos') }}</span>
        <span class="stat__value">{{ $stats['total_students'] }}</span>
        <span class="stat__sub"><span class="acc">{{ $stats['registered_students'] }}</span> {{ trans_choice('activado|activados', $stats['registered_students']) }} · <span class="warn">{{ $stats['pending_students'] }}</span> {{ trans_choice('pendiente|pendientes', $stats['pending_students']) }}</span>
    </div>
    <div class="stat fx-fade" style="--d: 0.18s">
        <span class="lbl">{{ __('cursos') }}</span>
        <span class="stat__value">{{ $stats['total_courses'] }}</span>
        @php
            $inactiveCourses = $stats['total_courses'] - $stats['active_courses'];
        @endphp
        <span class="stat__sub">{{ $stats['active_courses'] }} {{ trans_choice('activo|activos', $stats['active_courses']) }} · {{ $inactiveCourses }} {{ trans_choice('inactivo|inactivos', $inactiveCourses) }}</span>
    </div>
    <div class="stat fx-fade" style="--d: 0.26s">
        <span class="lbl">{{ __('matrículas activas') }}</span>
        <span class="stat__value">{{ $stats['total_enrollments'] }}</span>
        <span class="stat__sub">{{ $stats['cancelled_enrollments'] }} {{ trans_choice('cancelada|canceladas', $stats['cancelled_enrollments']) }}</span>
    </div>
    <div class="stat stat--acc fx-fade" style="--d: 0.34s">
        <span class="lbl">{{ __('plazas libres') }}</span>
        <span class="stat__value">{{ $stats['free_seats'] }}</span>
        <span class="stat__sub">{{ __('de :total en cursos activos', ['total' => $stats['total_seats']]) }}</span>
    </div>
</div>

<div class="grid-2">
    <section class="panel fx-fade" style="--d: 0.4s">
        <div class="panel__head"><h2 class="lbl">{{ __('alumnos recientes') }}</h2><a href="{{ route('admin.students.index') }}" class="link-arrow small">{{ __('ver todos') }}</a></div>
        @forelse ($recentStudents as $student)
            <a href="{{ route('admin.students.show', $student) }}" class="row-link" style="grid-template-columns: 2.5rem minmax(0, 1fr) auto;">
                <span class="avatar avatar--muted">{{ $student->initials }}</span>
                <span class="row-link__who"><span>{{ $student->full_name }}</span><span>{{ $student->email }}</span></span>
                @if ($student->is_registered)
                    <span class="status status--ok">{{ __('activado') }}</span>
                @else
                    <span class="status status--pending">{{ __('pendiente') }}</span>
                @endif
            </a>
        @empty
            <div class="empty"><span class="empty__cmd">ls {{ __('alumnos') }}/ <span class="muted">— {{ __('vacío') }}</span></span></div>
        @endforelse
    </section>

    <section class="panel fx-fade" style="--d: 0.48s">
        <div class="panel__head"><h2 class="lbl">{{ __('matrículas recientes') }}</h2><a href="{{ route('admin.enrollments.index') }}" class="link-arrow small">{{ __('ver todas') }}</a></div>
        @forelse ($recentEnrollments as $enrollment)
            <a href="{{ $enrollment->student ? route('admin.students.show', $enrollment->student) : '#' }}" class="row-link" style="grid-template-columns: minmax(0, 1fr) 5.5rem 7rem;">
                <span class="row-link__who"><span>{{ $enrollment->student->full_name ?? '—' }}</span><span>{{ $enrollment->course->name ?? '—' }}</span></span>
                <span class="acc small">{{ $enrollment->course->code ?? '' }}</span>
                <span class="xs muted col-when" style="text-align: right;">{{ $enrollment->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <div class="empty"><span class="empty__cmd">ls {{ __('matrículas') }}/ <span class="muted">— {{ __('vacío') }}</span></span></div>
        @endforelse
    </section>
</div>

<section class="panel fx-fade" style="--d: 0.56s">
    <div class="panel__head"><h2 class="lbl">{{ __('ocupación de cursos') }}</h2><a href="{{ route('admin.courses.index') }}" class="link-arrow small">{{ __('gestionar cursos') }}</a></div>
    <div class="occupancy">
        @forelse ($courses as $course)
            @php
                $used = $course->enrollments_count;
                $lit = $course->capacity ? ($used > 0 ? max(1, (int) round($used / $course->capacity * 20)) : 0) : 0;
            @endphp
            <div class="ls__row {{ $course->isActive() ? '' : 'is-off' }}">
                <span class="{{ $course->isActive() ? 'acc' : '' }}">{{ $course->code }}</span>
                <a href="{{ route('admin.courses.show', $course) }}" style="color: inherit; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $course->name }}</a>
                <span class="segbar col-bar" aria-hidden="true">
                    @for ($i = 0; $i < 20; $i++)
                        <i class="seg {{ $i < $lit ? 'is-on' : '' }}" style="--d: {{ number_format(0.7 + $loop->index * 0.06 + $i * 0.02, 2) }}s"></i>
                    @endfor
                </span>
                <span style="text-align: right;">{{ $used }}/{{ $course->capacity ?? '∞' }}</span>
                <span class="xs col-status {{ $course->isActive() ? 'text-2' : 'muted' }}" style="text-align: right;">{{ $course->isActive() ? __('activo') : __('inactivo') }}</span>
            </div>
        @empty
            <div class="empty"><span class="empty__cmd">ls {{ __('cursos') }}/ <span class="muted">— {{ __('vacío') }}</span></span></div>
        @endforelse
    </div>
</section>
@endsection
