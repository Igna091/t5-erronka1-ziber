@extends('layouts.admin')

@section('title', __('Editar :name', ['name' => $course->name]))
@section('path', __('cursos').'/'.$course->id.'/'.__('editar'))

@section('content')
<a href="{{ route('admin.courses.show', $course) }}" class="back-link"><x-icon name="arrow-left" :size="16" />{{ __('volver a la ficha') }}</a>

<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">{{ __('Editar curso.') }}</span></h1>
        <span class="text-2">{{ $course->code }} · {{ $course->name }}</span>
    </div>
</div>

<form method="POST" action="{{ route('admin.courses.update', $course) }}" class="panel form-card fx-fade" style="--d: 0.2s" novalidate>
    @csrf
    @method('PUT')
    <div class="panel__head"><h2 class="lbl">{{ __('datos del curso') }}</h2><span class="xs muted">* {{ __('obligatorio') }}</span></div>
    <div class="panel__body form">
        @include('admin.partials.course-fields')
        <div class="hr"></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ __('Guardar cambios') }}</button>
            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-ghost">{{ __('cancelar') }}</a>
        </div>
    </div>
</form>
@endsection
