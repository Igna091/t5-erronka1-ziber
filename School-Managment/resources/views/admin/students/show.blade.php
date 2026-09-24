@extends('layouts.admin')

@section('title', $student->full_name)
@section('path', 'alumnos/'.$student->id)

@section('content')
<a href="{{ route('admin.students.index') }}" class="back-link"><x-icon name="arrow-left" :size="16" />volver a alumnos</a>

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 1.5rem; min-width: 0;">
        <span class="avatar avatar--lg {{ $student->is_registered ? '' : 'avatar--warn' }}">{{ $student->initials }}</span>
        <div class="page-header__title" style="min-width: 0;">
            <h1 class="h-admin" style="font-size: clamp(2rem, 3.6vw, 3.25rem);"><span class="fx-type">{{ $student->full_name }}</span></h1>
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem 1.125rem;" class="small text-2">
                @if ($student->is_registered)
                    <span class="status status--ok status--pill" style="color: var(--acc);">cuenta activada</span>
                @else
                    <span class="status status--pending status--pill">pendiente de activación</span>
                @endif
                <span>alumno/a · alta el {{ $student->created_at->format('d.m.Y') }}</span>
            </div>
        </div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-ghost"><x-icon name="edit" :size="16" />editar</a>
        <form method="POST" action="{{ route('admin.students.destroy', $student) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" data-confirm="¿Eliminar a {{ $student->full_name }}? Se borrarán también sus matrículas y notas. Esta acción no se puede deshacer."><x-icon name="trash" :size="16" />eliminar</button>
        </form>
    </div>
</div>

<div class="grid-2 {{ $activationInfo ? 'grid-2--wide-right' : '' }}">
    <section class="panel fx-fade" style="--d: 0.2s">
        <div class="panel__head"><h2 class="lbl">datos personales</h2></div>
        <dl class="dl" style="padding: 0.5rem 1.375rem 1rem;">
            <div><dt class="lbl">nombre</dt><dd>{{ $student->name }}</dd></div>
            <div><dt class="lbl">apellidos</dt><dd>{{ $student->surname ?? '—' }}</dd></div>
            <div><dt class="lbl">email</dt><dd>{{ $student->email }}</dd></div>
            <div><dt class="lbl">dni</dt><dd>{{ $student->dni ?? '—' }}</dd></div>
            <div><dt class="lbl">teléfono</dt><dd>{{ $student->phone ?? '—' }}</dd></div>
        </dl>
    </section>

    @if ($activationInfo)
        @php
            $sentAt = $activationInfo['sentAt'];
            $expired = $activationInfo['expiresAt']?->isPast();
        @endphp
        <section class="panel panel--warn fx-fade" style="--d: 0.3s">
            <div class="panel__head"><h2 class="lbl">activación de la cuenta</h2><span class="xs warn">pendiente</span></div>
            <div class="panel__body">
                <ol class="steps">
                    <li class="is-done">cuenta creada por administración · {{ $student->created_at->format('d.m.Y') }}</li>
                    @if ($sentAt && !$expired)
                        <li class="is-done">email de activación enviado · {{ $sentAt->locale('es')->diffForHumans() }}</li>
                    @elseif ($sentAt)
                        <li class="is-wait">el enlace caducó el {{ $activationInfo['expiresAt']->format('d.m.Y') }}</li>
                    @else
                        <li class="is-wait">no hay ningún enlace de activación vigente</li>
                    @endif
                    <li class="is-wait">{{ $student->name }} elige su contraseña con el enlace</li>
                </ol>
                <p class="small text-2" style="line-height: 1.7;">El enlace caduca a los 7 días y solo sirve una vez. Si lo ha perdido o ha caducado, envía uno nuevo: el anterior dejará de funcionar.</p>
                <form method="POST" action="{{ route('admin.students.resend-activation', $student) }}" class="form-actions">
                    @csrf
                    <button type="submit" class="btn btn-primary" @if ($activationInfo['cooldown'] > 0) data-countdown="{{ $activationInfo['cooldown'] }}" @endif>Reenviar email de activación</button>
                    <span class="xs muted">máximo un envío por minuto</span>
                </form>
            </div>
        </section>
    @endif
</div>

<section class="panel fx-fade" style="--d: 0.4s">
    <div class="panel__head">
        <h2 class="lbl">matrículas ({{ $student->enrollments->count() }})</h2>
        <a href="{{ route('admin.enrollments.create', ['student_id' => $student->id]) }}" class="btn btn-outline btn-sm"><x-icon name="plus" :size="14" />matricular en un curso</a>
    </div>
    @if ($student->enrollments->isNotEmpty())
        <div style="overflow-x: auto;">
            <table class="table">
                <thead><tr><th>curso</th><th>matrícula</th><th>estado</th></tr></thead>
                <tbody>
                    @foreach ($student->enrollments as $enrollment)
                        <tr>
                            <td><a href="{{ route('admin.courses.show', $enrollment->course) }}" style="color: var(--text); text-decoration: none;"><span class="t-code">{{ $enrollment->course->code }}</span>&nbsp;&nbsp;{{ $enrollment->course->name }}</a></td>
                            <td class="t-sub">{{ $enrollment->enrolled_at->format('d.m.Y') }}</td>
                            <td>
                                @if ($enrollment->status === 'active')
                                    <span class="status status--ok">activa</span>
                                @else
                                    <span class="status status--off">cancelada</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty">
            <span class="empty__cmd">ls matrículas/ <span class="muted">— vacío</span></span>
            <span>{{ $student->name }} todavía no tiene matrículas.@unless ($student->is_registered) Puedes matricularle aunque no haya activado la cuenta.@endunless</span>
        </div>
    @endif
</section>
@endsection
