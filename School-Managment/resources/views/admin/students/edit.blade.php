@extends('layouts.admin')

@section('title', __('Editar :name', ['name' => $student->full_name]))
@section('path', __('alumnos').'/'.$student->id.'/'.__('editar'))

@section('content')
<a href="{{ route('admin.students.show', $student) }}" class="back-link"><x-icon name="arrow-left" :size="16" />{{ __('volver a la ficha') }}</a>

<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">{{ __('Editar alumno.') }}</span></h1>
        <span class="text-2">{{ $student->full_name }}@unless ($student->is_registered) · {{ __('si cambias el email de un alumno pendiente, le enviaremos un enlace nuevo') }}@endunless</span>
    </div>
</div>

<form method="POST" action="{{ route('admin.students.update', $student) }}" class="panel form-card fx-fade" style="--d: 0.2s" novalidate>
    @csrf
    @method('PUT')
    <div class="panel__head"><h2 class="lbl">{{ __('datos del alumno') }}</h2><span class="xs muted">* {{ __('obligatorio') }}</span></div>
    <div class="panel__body form">
        @include('admin.partials.student-fields')
        <div class="hr"></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ __('Guardar cambios') }}</button>
            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-ghost">{{ __('cancelar') }}</a>
        </div>
    </div>
</form>
@endsection
