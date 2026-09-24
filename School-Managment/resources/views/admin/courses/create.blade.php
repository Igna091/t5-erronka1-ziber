@extends('layouts.admin')

@section('title', 'Nuevo curso')
@section('path', 'cursos/nuevo')

@section('content')
<a href="{{ route('admin.courses.index') }}" class="back-link"><x-icon name="arrow-left" :size="16" />volver a cursos</a>

<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">Nuevo curso.</span></h1>
        <span class="text-2">Los cursos activos aparecen en la web y admiten matrículas.</span>
    </div>
</div>

<form method="POST" action="{{ route('admin.courses.store') }}" class="panel form-card fx-fade" style="--d: 0.2s" novalidate>
    @csrf
    <div class="panel__head"><h2 class="lbl">datos del curso</h2><span class="xs muted">* obligatorio</span></div>
    <div class="panel__body form">
        @include('admin.partials.course-fields')
        <div class="hr"></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Crear curso</button>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-ghost">cancelar</a>
        </div>
    </div>
</form>
@endsection
