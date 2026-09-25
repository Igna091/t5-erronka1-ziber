@extends('layouts.admin')

@section('title', __('Nuevo alumno'))
@section('path', __('alumnos').'/'.__('nuevo'))

@section('content')
<a href="{{ route('admin.students.index') }}" class="back-link"><x-icon name="arrow-left" :size="16" />{{ __('volver a alumnos') }}</a>

<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">{{ __('Nuevo alumno.') }}</span></h1>
        <span class="text-2">{{ __('Al crearlo le enviaremos un email para que active su cuenta y elija su contraseña.') }}</span>
    </div>
</div>

<form method="POST" action="{{ route('admin.students.store') }}" class="panel form-card fx-fade" style="--d: 0.2s" novalidate>
    @csrf
    <div class="panel__head"><h2 class="lbl">{{ __('datos del alumno') }}</h2><span class="xs muted">* {{ __('obligatorio') }}</span></div>
    <div class="panel__body form">
        @include('admin.partials.student-fields')
        <div class="hr"></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ __('Crear alumno') }}</button>
            <a href="{{ route('admin.students.index') }}" class="btn btn-ghost">{{ __('cancelar') }}</a>
        </div>
    </div>
</form>
@endsection
