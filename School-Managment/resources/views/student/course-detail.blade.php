@extends('layouts.public')

@section('title', $course->name.' - ZiberEibar')
@section('description', \Illuminate\Support\Str::limit($course->description ?? $course->name, 155))

@section('content')
@php
    $words = explode(' ', $course->name);
    $split = (int) ceil(count($words) / 2);
    $line1 = implode(' ', array_slice($words, 0, $split));
    $line2 = implode(' ', array_slice($words, $split));
    $isStudent = auth()->check() && auth()->user()->isStudent();
    $free = $course->available_spots;
@endphp

<div class="crumbs">
    <a href="{{ route('courses.index') }}"><x-icon name="arrow-left" :size="16" />{{ __('volver a cursos') }}</a>
    <span class="crumbs__sep" aria-hidden="true">/</span>
    <span>{{ __('cursos') }} <span class="crumbs__sep">/</span> <span class="crumbs__here">{{ $course->slug }}</span></span>
</div>

<section class="container detail">
    <div class="detail__copy">
        <div class="detail__meta fx-fade">
            <span class="code-chip">{{ $course->code }}</span>
            @if ($course->isActive())
                <span style="display: inline-flex; align-items: center; gap: 0.625rem;"><span class="led" aria-hidden="true"></span>{{ __('matrícula abierta') }}</span>
            @else
                <span class="status status--off">{{ __('curso inactivo') }}</span>
            @endif
            @if ($course->academic_year_label)
                <span class="muted">{{ __('curso :year', ['year' => $course->academic_year_label]) }}</span>
            @endif
        </div>

        <h1 class="h-page">
            <span class="fx-type">{{ $line1 }}</span>
            @if ($line2 !== '')
                <span class="fx-type acc" style="--d: 0.7s">{{ $line2 }}<span class="cursor" aria-hidden="true"></span></span>
            @endif
        </h1>

        <p class="lead fx-fade" style="--d: 1.2s">{{ $course->description ?? __('No hay descripción disponible para este curso.') }}</p>

        <div class="specs fx-fade" style="--d: 1.4s">
            <div class="spec"><span class="lbl">{{ __('duración') }}</span><span class="spec__value">{{ $course->duration_hours ? $course->duration_hours.' h' : '—' }}</span></div>
            <div class="spec"><span class="lbl">{{ __('inicio') }}</span><span class="spec__value">{{ $course->start_date?->format('d.m.Y') ?? '—' }}</span></div>
            <div class="spec"><span class="lbl">{{ __('fin') }}</span><span class="spec__value">{{ $course->end_date?->format('d.m.Y') ?? '—' }}</span></div>
            <div class="spec"><span class="lbl">{{ __('plazas') }}</span><span class="spec__value">{{ $course->capacity ?? __('sin límite') }}</span></div>
        </div>
    </div>

    <aside class="panel panel--float panel__pad enroll-panel fx-fade" style="--d: 0.4s" aria-label="{{ __('Matrícula') }}">
        <x-pads />
        <div class="panel__row"><span class="lbl">{{ __('matrícula') }}</span><span class="lbl acc">{{ $course->code }}</span></div>

        @if ($course->capacity)
            <div style="display: flex; align-items: baseline; gap: 0.75rem;">
                <span class="big-number">{{ $free }}</span>
                <span class="text-2">{{ __('de :total plazas libres', ['total' => $course->capacity]) }}</span>
            </div>
            @if ($course->capacity <= 40)
                <div class="seats" aria-hidden="true">
                    @for ($i = 0; $i < $course->capacity; $i++)
                        <i class="seat {{ $i < $free ? 'is-free' : '' }}" style="--d: {{ number_format(0.6 + 0.02 * $i, 2) }}s"></i>
                    @endfor
                </div>
            @endif
        @else
            <span class="h-panel">{{ __('Plazas sin límite') }}</span>
        @endif

        @if ($isEnrolled)
            <div class="panel panel--acc" style="padding: 1.125rem; display: flex; flex-direction: column; gap: 0.5rem;">
                <span class="acc fx-tl" style="font-weight: 700;">[ok] {{ __('matrícula activa') }}</span>
                <span class="small text-2">{{ __('Ya estás matriculado/a en este curso.') }}</span>
                <a href="{{ route('student.enrollments') }}" class="link-arrow">{{ __('ver mis matrículas') }}</a>
            </div>
        @elseif ($isStudent && $course->isActive() && $course->hasAvailableSpots())
            <form method="POST" action="{{ route('courses.enroll', $course) }}" class="stack-sm">
                @csrf
                <button type="submit" class="btn btn-primary btn-block">{{ __('Matricularme') }}</button>
                <span class="xs muted" style="line-height: 1.6;">{{ __('Si necesitas cancelar la matrícula, contacta con administración.') }}</span>
            </form>
        @elseif ($isStudent && !$course->hasAvailableSpots())
            <x-alert type="warning">{{ __('No quedan plazas disponibles en este curso.') }}</x-alert>
        @elseif ($isStudent)
            <x-alert type="warning">{{ __('Este curso no admite matrículas ahora mismo.') }}</x-alert>
        @elseif (!auth()->check())
            <a href="{{ route('login') }}" class="btn btn-primary btn-block">{{ __('Inicia sesión para matricularte') }}</a>
            <span class="xs muted">{{ __('¿Aún no has activado tu cuenta?') }} <a href="{{ route('register') }}">{{ __('Pide el enlace de activación') }}</a>.</span>
        @endif
    </aside>
</section>

@if ($course->courseSubjects->isNotEmpty())
    <section class="container section" style="padding-top: 1.5rem;">
        <div class="section-head">
            <div class="section-head__title">
                <span class="kicker">// {{ __('asignaturas') }}</span>
                <h2 class="h-section">{{ __('Qué vas a estudiar.') }}</h2>
            </div>
            <p>{{ trans_choice(':count asignatura|:count asignaturas', $course->courseSubjects->count()) }}</p>
        </div>

        <div class="ls subjects">
            <div class="ls__head lbl" aria-hidden="true"><span>{{ __('código') }}</span><span>{{ __('asignatura') }}</span><span>{{ __('horas') }}</span><span class="col-teacher">{{ __('profesor/a') }}</span></div>
            @foreach ($course->courseSubjects as $item)
                <div class="ls__row">
                    <span class="acc">{{ $item->subject->code }}</span>
                    <span class="subj-name">{{ $item->subject->name }}</span>
                    <span>{{ $item->subject->hours }} h</span>
                    <span class="col-teacher">{{ $item->teacher?->full_name ?? __('por asignar') }}</span>
                </div>
            @endforeach
        </div>
    </section>
@endif
@endsection
