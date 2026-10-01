@extends('layouts.admin')

@section('title', $teacher->full_name)
@section('path', __('profesores').'/'.$teacher->id)

@section('content')
<a href="{{ route('admin.dashboard') }}" class="back-link"><x-icon name="arrow-left" :size="16" />{{ __('volver al panel') }}</a>

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 1.5rem; min-width: 0;">
        <span class="avatar avatar--lg {{ $teacher->is_registered ? '' : 'avatar--warn' }}">{{ $teacher->initials }}</span>
        <div class="page-header__title" style="min-width: 0;">
            <h1 class="h-admin" style="font-size: clamp(2rem, 3.6vw, 3.25rem);"><span class="fx-type">{{ $teacher->full_name }}</span></h1>
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem 1.125rem;" class="small text-2">
                @if ($teacher->is_registered)
                    <span class="status status--ok status--pill" style="color: var(--acc);">{{ __('cuenta activada') }}</span>
                @else
                    <span class="status status--pending status--pill">{{ __('pendiente de activación') }}</span>
                @endif
                <span>{{ __('profesor/a · alta el :date', ['date' => $teacher->created_at->format('d.m.Y')]) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="grid-2 {{ $activationInfo ? 'grid-2--wide-right' : '' }}">
    <section class="panel fx-fade" style="--d: 0.2s">
        <div class="panel__head"><h2 class="lbl">{{ __('datos personales') }}</h2></div>
        <dl class="dl" style="padding: 0.5rem 1.375rem 1rem;">
            <div><dt class="lbl">{{ __('nombre') }}</dt><dd>{{ $teacher->name }}</dd></div>
            <div><dt class="lbl">{{ __('apellidos') }}</dt><dd>{{ $teacher->surname ?? '—' }}</dd></div>
            <div><dt class="lbl">{{ __('email') }}</dt><dd>{{ $teacher->email }}</dd></div>
            <div><dt class="lbl">{{ __('dni') }}</dt><dd>{{ $teacher->dni ?? '—' }}</dd></div>
            <div><dt class="lbl">{{ __('teléfono') }}</dt><dd>{{ $teacher->phone ?? '—' }}</dd></div>
        </dl>
    </section>

    @if ($activationInfo)
        @include('admin.partials.activation-panel', ['user' => $teacher, 'resendUrl' => route('admin.teachers.resend-activation', $teacher)])
    @endif
</div>

<section class="panel fx-fade" style="--d: 0.4s">
    <div class="panel__head"><h2 class="lbl">{{ __('qué imparte') }}</h2></div>
    @if ($teacher->taughtCourses->isNotEmpty() || $teacher->taughtSubjects->isNotEmpty())
        <div style="overflow-x: auto;">
            <table class="table">
                <thead><tr><th>{{ __('curso') }}</th><th>{{ __('asignatura') }}</th></tr></thead>
                <tbody>
                    @foreach ($teacher->taughtCourses as $course)
                        <tr>
                            <td><a href="{{ route('admin.courses.show', $course) }}" style="color: var(--text); text-decoration: none;"><span class="t-code">{{ $course->code }}</span>&nbsp;&nbsp;{{ $course->name }}</a></td>
                            <td class="t-sub">{{ __('curso entero') }}</td>
                        </tr>
                    @endforeach
                    @foreach ($teacher->taughtSubjects as $item)
                        <tr>
                            <td><a href="{{ route('admin.courses.show', $item->course) }}" style="color: var(--text); text-decoration: none;"><span class="t-code">{{ $item->course->code }}</span>&nbsp;&nbsp;{{ $item->course->name }}</a></td>
                            <td><span class="t-code">{{ $item->subject->code }}</span>&nbsp;&nbsp;{{ $item->subject->name }} <span class="t-sub">· {{ $item->subject->hours }} h</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty">
            <span class="empty__cmd">ls {{ __('asignaturas') }}/ <span class="muted">— {{ __('vacío') }}</span></span>
            <span>{{ __(':name todavía no tiene cursos ni asignaturas.', ['name' => $teacher->name]) }}</span>
        </div>
    @endif
</section>
@endsection
