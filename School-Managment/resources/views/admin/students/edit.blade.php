@extends('layouts.admin')

@section('title', 'Editar '.$student->full_name)
@section('path', 'alumnos/'.$student->id.'/editar')

@section('content')
<a href="{{ route('admin.students.show', $student) }}" class="back-link"><x-icon name="arrow-left" :size="16" />volver a la ficha</a>

<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">Editar alumno.</span></h1>
        <span class="text-2">{{ $student->full_name }}@unless ($student->is_registered) · si cambias el email de un alumno pendiente, le enviaremos un enlace nuevo @endunless</span>
    </div>
</div>

<form method="POST" action="{{ route('admin.students.update', $student) }}" class="panel form-card fx-fade" style="--d: 0.2s" novalidate>
    @csrf
    @method('PUT')
    <div class="panel__head"><h2 class="lbl">datos del alumno</h2><span class="xs muted">* obligatorio</span></div>
    <div class="panel__body form">
        @include('admin.partials.student-fields')
        <div class="hr"></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-ghost">cancelar</a>
        </div>
    </div>
</form>
@endsection
