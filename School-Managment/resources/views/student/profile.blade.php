@extends('layouts.public')

@section('title', __('Mi perfil - ZiberEibar'))

@section('content')
<section class="container page-top area">
    <div class="area__main">
        <div class="stack-sm">
            <span class="kicker fx-fade">{{ __('alumno') }} // {{ __('perfil') }}</span>
            <h1 class="h-page"><span class="fx-type">{{ __('Mi perfil.') }}</span></h1>
        </div>

        <div class="panel fx-fade" style="--d: 0.3s">
            <div class="panel__head"><h2 class="lbl">{{ __('datos personales') }}</h2><span class="status status--ok">{{ __('cuenta activada') }}</span></div>
            <dl class="dl panel__body" style="gap: 0; padding-block: 0.5rem 1rem;">
                <div><dt class="lbl">{{ __('nombre') }}</dt><dd>{{ $user->name }}</dd></div>
                <div><dt class="lbl">{{ __('apellidos') }}</dt><dd>{{ $user->surname ?? '—' }}</dd></div>
                <div><dt class="lbl">{{ __('email') }}</dt><dd>{{ $user->email }}</dd></div>
                <div><dt class="lbl">{{ __('dni') }}</dt><dd>{{ $user->dni ?? '—' }}</dd></div>
                <div><dt class="lbl">{{ __('teléfono') }}</dt><dd>{{ $user->phone ?? '—' }}</dd></div>
            </dl>
        </div>
        <p class="small muted">{!! __('Puedes cambiar tu email y tu contraseña en :link. Para cambiar el resto de tus datos personales, contacta con administración.', ['link' => '<a href="'.e(route('settings.index')).'#seccion-cuenta">'.e(__('ajustes')).'</a>']) !!}</p>
    </div>

    <aside class="panel panel__pad fx-fade" style="--d: 0.2s" aria-label="{{ __('Resumen') }}">
        <x-pads :count="2" />
        <span class="lbl">{{ __('resumen') }}</span>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span class="avatar avatar--md">{{ $user->initials }}</span>
            <span class="disp" style="font-size: 1.5rem; font-weight: 700; line-height: 1.1;">{{ $user->full_name }}</span>
        </div>
        <div style="display: flex; align-items: baseline; gap: 0.75rem; padding-block: 1rem; border-block: 1px solid var(--line);">
            <span class="big-number" style="font-size: 4rem;">{{ $enrollmentCount }}</span>
            <span class="text-2">{{ trans_choice('curso matriculado|cursos matriculados', $enrollmentCount) }}</span>
        </div>
        <div class="stack-sm" style="gap: 0;">
            <a href="{{ route('student.enrollments') }}" class="link-arrow">{{ __('mis matrículas') }}</a>
            <a href="{{ route('courses.index') }}" class="link-arrow">{{ __('ver cursos') }}</a>
        </div>
    </aside>
</section>
@endsection
