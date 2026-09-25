@extends('layouts.admin')

@section('title', __('Activaciones pendientes'))
@section('path', __('activaciones'))

@section('content')
<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">{{ __('Activaciones pendientes.') }}</span></h1>
        <span class="text-2">
            @if ($students->isEmpty())
                {{ __('Todos los alumnos han activado su cuenta.') }}
            @else
                {{ trans_choice(':count alumno no ha activado su cuenta.|:count alumnos no han activado su cuenta.', $students->count()) }} {{ __('Cada enlace caduca a los 7 días y solo se puede enviar un email por minuto a cada alumno.') }}
            @endif
        </span>
    </div>
    @if ($students->isNotEmpty())
        <form method="POST" action="{{ route('admin.activations.resend-all') }}" class="page-header-actions">
            @csrf
            <button type="submit" class="btn btn-primary" data-confirm="{{ trans_choice('¿Enviar un nuevo email de activación al alumno pendiente? Su enlace anterior dejará de funcionar.|¿Enviar un nuevo email de activación a los :count alumnos pendientes? Sus enlaces anteriores dejarán de funcionar.', $students->count()) }}">
                <x-icon name="mail-send" :size="18" />{{ __('Reenviar a todos (:count)', ['count' => $students->count()]) }}
            </button>
        </form>
    @endif
</div>

<div class="table-wrapper fx-fade" style="--d: 0.15s">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('alumno') }}</th>
                <th>{{ __('enlace de activación') }}</th>
                <th>{{ __('caduca') }}</th>
                <th>{{ __('alta') }}</th>
                <th><span class="sr-only">{{ __('acciones') }}</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                @php
                    $info = $status[$student->id];
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('admin.students.show', $student) }}" style="display: flex; align-items: center; gap: 0.875rem; color: var(--text); text-decoration: none;">
                            <span class="avatar avatar--muted avatar--warn">{{ $student->initials }}</span>
                            <span style="display: flex; flex-direction: column;"><span class="t-main">{{ $student->full_name }}</span><span class="t-sub">{{ $student->email }}</span></span>
                        </a>
                    </td>
                    <td>
                        @if (!$info['sentAt'])
                            <span class="status status--off">{{ __('sin enlace vigente') }}</span>
                        @elseif ($info['expired'])
                            <span class="status status--pending">{{ __('caducado') }}</span>
                        @else
                            <span class="status status--ok">{{ __('enviado :when', ['when' => $info['sentAt']->diffForHumans()]) }}</span>
                        @endif
                    </td>
                    <td class="t-sub">{{ $info['expiresAt'] && !$info['expired'] ? $info['expiresAt']->format('d.m.Y H:i') : '—' }}</td>
                    <td class="t-sub">{{ $student->created_at->format('d.m.Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <form method="POST" action="{{ route('admin.students.resend-activation', $student) }}">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm" @if ($info['cooldown'] > 0) data-countdown="{{ $info['cooldown'] }}" @endif><x-icon name="mail" :size="16" />{{ __('reenviar') }}</button>
                            </form>
                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-ghost btn-icon" aria-label="{{ __('Ver ficha de :name', ['name' => $student->full_name]) }}"><x-icon name="eye" :size="16" /></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty">
                            <span class="empty__cmd">ls {{ __('pendientes') }}/ <span class="muted">— {{ __('vacío') }}</span></span>
                            <span>{{ __('No hay nadie esperando a activar su cuenta.') }}</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
