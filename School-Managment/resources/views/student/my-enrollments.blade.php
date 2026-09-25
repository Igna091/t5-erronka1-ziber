@extends('layouts.public')

@section('title', __('Mis matrículas - ZiberEibar'))

@section('content')
@php
    $user = auth()->user();
    $active = $enrollments->where('status', 'active');
@endphp

<section class="container page-top area">
    <div class="area__main">
        <div class="area__head">
            <div class="stack-sm">
                <span class="kicker fx-fade">{{ __('alumno') }} // {{ mb_strtolower($user->full_name) }}</span>
                <h1 class="h-page"><span class="fx-type">{{ __('Mis matrículas.') }}</span></h1>
            </div>
            <span class="text-2" style="display: inline-flex; align-items: center; gap: 0.625rem; padding-bottom: 0.5rem;">
                <span class="led" aria-hidden="true"></span>{{ trans_choice(':count activa|:count activas', $active->count()) }}
            </span>
        </div>

        @if ($enrollments->isNotEmpty())
            <div class="stack">
                @foreach ($enrollments as $enrollment)
                    @php
                        $course = $enrollment->course;
                    @endphp
                    @if ($enrollment->status === 'active')
                        <a href="{{ route('courses.show', $course) }}" class="enrol-card fx-fade" style="--d: {{ number_format(0.2 + $loop->index * 0.1, 2) }}s">
                            <div class="stack-sm">
                                <span class="acc">{{ $course->code }}</span>
                                <span class="status status--ok">{{ __('activa') }}</span>
                            </div>
                            <div class="stack-sm">
                                <span class="enrol-card__name">{{ $course->name }}</span>
                                <span class="small muted">
                                    {{ $course->duration_hours ? $course->duration_hours.' h · ' : '' }}{{ $course->start_date?->format('d.m.Y') ?? '—' }} → {{ $course->end_date?->format('d.m.Y') ?? '—' }}
                                </span>
                            </div>
                            <div class="stack-sm">
                                <span class="lbl">{{ __('matriculado/a el') }}</span>
                                <span>{{ $enrollment->enrolled_at->format('d.m.Y') }}</span>
                            </div>
                            <span class="enrol-card__go" aria-hidden="true"><x-icon name="arrow-right" /></span>
                        </a>
                    @else
                        <div class="enrol-card enrol-card--off fx-fade" style="--d: {{ number_format(0.2 + $loop->index * 0.1, 2) }}s">
                            <div class="stack-sm">
                                <span>{{ $course->code }}</span>
                                <span class="status status--off">{{ __('cancelada') }}</span>
                            </div>
                            <div class="stack-sm">
                                <span class="enrol-card__name">{{ $course->name }}</span>
                                <span class="small">{{ __('cancelada por administración') }}</span>
                            </div>
                            <div class="stack-sm">
                                <span class="lbl">{{ __('matriculado/a el') }}</span>
                                <span>{{ $enrollment->enrolled_at->format('d.m.Y') }}</span>
                            </div>
                            <span></span>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="panel empty">
                <span class="empty__cmd">ls {{ __('matrículas') }}/ <span class="muted">— {{ __('vacío') }}</span></span>
                <span>{{ __('Aún no te has matriculado en ningún curso.') }}</span>
            </div>
        @endif

        <a href="{{ route('courses.index') }}" class="btn btn-ghost" style="align-self: flex-start;"><span class="acc">$</span>{{ __('explorar cursos') }}</a>
    </div>

    <aside class="panel panel__pad fx-fade" style="--d: 0.2s" aria-label="{{ __('Tu cuenta') }}">
        <x-pads :count="2" />
        <span class="lbl">{{ __('tu cuenta') }}</span>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span class="avatar avatar--md">{{ $user->initials }}</span>
            <div class="stack-sm" style="gap: 0.25rem;">
                <span class="disp" style="font-size: 1.5rem; font-weight: 700; line-height: 1.1;">{{ $user->full_name }}</span>
                <span class="status status--ok">{{ __('cuenta activada') }}</span>
            </div>
        </div>
        <dl class="dl" style="border-block: 1px solid var(--line);">
            <div><dt class="lbl">{{ __('email') }}</dt><dd>{{ $user->email }}</dd></div>
            <div><dt class="lbl">{{ __('dni') }}</dt><dd>{{ $user->dni ?? '—' }}</dd></div>
            <div><dt class="lbl">{{ __('teléfono') }}</dt><dd>{{ $user->phone ?? '—' }}</dd></div>
        </dl>
        <div class="stack-sm" style="gap: 0;">
            <a href="{{ route('student.profile') }}" class="link-arrow">{{ __('ver mi perfil') }}</a>
            <a href="{{ route('settings.index') }}" class="link-arrow">{{ __('ajustes') }}</a>
        </div>
    </aside>
</section>
@endsection
