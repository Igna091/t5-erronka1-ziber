@extends('layouts.admin')

@section('title', __('Nuevo curso'))
@section('path', __('cursos').'/'.__('nuevo'))

@section('content')
<a href="{{ route('admin.courses.index') }}" class="back-link"><x-icon name="arrow-left" :size="16" />{{ __('volver a cursos') }}</a>

<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">{{ __('Nuevo curso.') }}</span></h1>
        <span class="text-2">{{ __('Los cursos activos aparecen en la web y admiten matrículas.') }}</span>
    </div>
</div>

<form method="POST" action="{{ route('admin.courses.store') }}" class="panel form-card fx-fade" style="--d: 0.2s" novalidate>
    @csrf
    <div class="panel__head"><h2 class="lbl">{{ __('datos del curso') }}</h2><span class="xs muted">* {{ __('obligatorio') }}</span></div>
    <div class="panel__body form">
        @include('admin.partials.course-fields')
        <div class="hr"></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ __('Crear curso') }}</button>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-ghost">{{ __('cancelar') }}</a>
        </div>
    </div>
</form>
@endsection
