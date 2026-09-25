@extends('layouts.admin')

@section('title', $student->full_name)
@section('path', __('alumnos').'/'.$student->id)

@section('content')
<a href="{{ route('admin.students.index') }}" class="back-link"><x-icon name="arrow-left" :size="16" />{{ __('volver a alumnos') }}</a>

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 1.5rem; min-width: 0;">
        <span class="avatar avatar--lg {{ $student->is_registered ? '' : 'avatar--warn' }}">{{ $student->initials }}</span>
        <div class="page-header__title" style="min-width: 0;">
            <h1 class="h-admin" style="font-size: clamp(2rem, 3.6vw, 3.25rem);"><span class="fx-type">{{ $student->full_name }}</span></h1>
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem 1.125rem;" class="small text-2">
                @if ($student->is_registered)
                    <span class="status status--ok status--pill" style="color: var(--acc);">{{ __('cuenta activada') }}</span>
                @else
                    <span class="status status--pending status--pill">{{ __('pendiente de activación') }}</span>
                @endif
                <span>{{ __('alumno/a · alta el :date', ['date' => $student->created_at->format('d.m.Y')]) }}</span>
            </div>
        </div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-ghost"><x-icon name="edit" :size="16" />{{ __('editar') }}</a>
        <form method="POST" action="{{ route('admin.students.destroy', $student) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" data-confirm="{{ __('¿Eliminar a :name? Se borrarán también sus matrículas y notas. Esta acción no se puede deshacer.', ['name' => $student->full_name]) }}"><x-icon name="trash" :size="16" />{{ __('eliminar') }}</button>
        </form>
    </div>
</div>

<div class="grid-2 {{ $activationInfo ? 'grid-2--wide-right' : '' }}">
    <section class="panel fx-fade" style="--d: 0.2s">
        <div class="panel__head"><h2 class="lbl">{{ __('datos personales') }}</h2></div>
        <dl class="dl" style="padding: 0.5rem 1.375rem 1rem;">
            <div><dt class="lbl">{{ __('nombre') }}</dt><dd>{{ $student->name }}</dd></div>
            <div><dt class="lbl">{{ __('apellidos') }}</dt><dd>{{ $student->surname ?? '—' }}</dd></div>
            <div><dt class="lbl">{{ __('email') }}</dt><dd>{{ $student->email }}</dd></div>
            <div><dt class="lbl">{{ __('dni') }}</dt><dd>{{ $student->dni ?? '—' }}</dd></div>
            <div><dt class="lbl">{{ __('teléfono') }}</dt><dd>{{ $student->phone ?? '—' }}</dd></div>
        </dl>
    </section>

    @if ($activationInfo)
        @php
            $sentAt = $activationInfo['sentAt'];
            $expired = $activationInfo['expired'];
        @endphp
        <section class="panel panel--warn fx-fade" style="--d: 0.3s">
            <div class="panel__head"><h2 class="lbl">{{ __('activación de la cuenta') }}</h2><span class="xs warn">{{ __('pendiente') }}</span></div>
            <div class="panel__body">
                <ol class="steps">
                    <li class="is-done">{{ __('cuenta creada por administración · :date', ['date' => $student->created_at->format('d.m.Y')]) }}</li>
                    @if ($sentAt && !$expired)
                        <li class="is-done">{{ __('email de activación enviado · :when', ['when' => $sentAt->diffForHumans()]) }}</li>
                    @elseif ($sentAt)
                        <li class="is-wait">{{ __('el enlace caducó el :date', ['date' => $activationInfo['expiresAt']->format('d.m.Y')]) }}</li>
                    @else
                        <li class="is-wait">{{ __('no hay ningún enlace de activación vigente') }}</li>
                    @endif
                    <li class="is-wait">{{ __(':name elige su contraseña con el enlace', ['name' => $student->name]) }}</li>
                </ol>
                <p class="small text-2" style="line-height: 1.7;">{{ __('El enlace caduca a los 7 días y solo sirve una vez. Si lo ha perdido o ha caducado, envía uno nuevo: el anterior dejará de funcionar.') }}</p>
                <form method="POST" action="{{ route('admin.students.resend-activation', $student) }}" class="form-actions">
                    @csrf
                    <button type="submit" class="btn btn-primary" @if ($activationInfo['cooldown'] > 0) data-countdown="{{ $activationInfo['cooldown'] }}" @endif>{{ __('Reenviar email de activación') }}</button>
                    <span class="xs muted">{{ __('máximo un envío por minuto') }}</span>
                </form>
            </div>
        </section>
    @endif
</div>

<section class="panel fx-fade" style="--d: 0.4s">
    <div class="panel__head">
        <h2 class="lbl">{{ __('matrículas (:count)', ['count' => $student->enrollments->count()]) }}</h2>
        <a href="{{ route('admin.enrollments.create', ['student_id' => $student->id]) }}" class="btn btn-outline btn-sm"><x-icon name="plus" :size="14" />{{ __('matricular en un curso') }}</a>
    </div>
    @if ($student->enrollments->isNotEmpty())
        <div style="overflow-x: auto;">
            <table class="table">
                <thead><tr><th>{{ __('curso') }}</th><th>{{ __('matrícula') }}</th><th>{{ __('estado') }}</th></tr></thead>
                <tbody>
                    @foreach ($student->enrollments as $enrollment)
                        <tr>
                            <td><a href="{{ route('admin.courses.show', $enrollment->course) }}" style="color: var(--text); text-decoration: none;"><span class="t-code">{{ $enrollment->course->code }}</span>&nbsp;&nbsp;{{ $enrollment->course->name }}</a></td>
                            <td class="t-sub">{{ $enrollment->enrolled_at->format('d.m.Y') }}</td>
                            <td>
                                @if ($enrollment->status === 'active')
                                    <span class="status status--ok">{{ __('activa') }}</span>
                                @else
                                    <span class="status status--off">{{ __('cancelada') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty">
            <span class="empty__cmd">ls {{ __('matrículas') }}/ <span class="muted">— {{ __('vacío') }}</span></span>
            <span>{{ __(':name todavía no tiene matrículas.', ['name' => $student->name]) }}@unless ($student->is_registered) {{ __('Puedes matricularle aunque no haya activado la cuenta.') }}@endunless</span>
        </div>
    @endif
</section>
@endsection
