@extends('layouts.admin')

@section('title', __('Nuevo profesor'))
@section('path', __('profesores').'/'.__('nuevo'))

@section('content')
<a href="{{ route('admin.dashboard') }}" class="back-link"><x-icon name="arrow-left" :size="16" />{{ __('volver al panel') }}</a>

<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">{{ __('Nuevo profesor.') }}</span></h1>
        <span class="text-2">{{ __('Al crearlo le enviaremos un email para que active su cuenta y elija su contraseña.') }}</span>
    </div>
</div>

<form method="POST" action="{{ route('admin.teachers.store') }}" class="panel form-card fx-fade" style="--d: 0.2s" novalidate>
    @csrf
    <div class="panel__head"><h2 class="lbl">{{ __('datos del profesor') }}</h2><span class="xs muted">* {{ __('obligatorio') }}</span></div>
    <div class="panel__body form">
        @include('admin.partials.student-fields', ['emailPlaceholder' => __('profesor@email.com')])
    </div>

    <div class="panel__head"><h2 class="lbl">{{ __('qué imparte') }}</h2><span class="xs muted">{{ __('curso entero, asignaturas o ambas') }}</span></div>
    <div class="panel__body form">
        <div class="form-group">
            <label for="course_id" class="form-label">{{ __('curso') }} <span class="req">*</span></label>
            <select id="course_id" name="course_id" class="form-select @error('course_id') is-invalid @enderror" required data-course-select>
                <option value="">{{ __('selecciona un curso…') }}</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected((int) old('course_id', $selectedCourseId) === $course->id)>
                        {{ $course->code }} · {{ $course->name }}{{ $course->academic_year_label ? ' · '.$course->academic_year_label : '' }}
                    </option>
                @endforeach
            </select>
            @error('course_id')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <label class="check">
            <input type="checkbox" name="whole_course" value="1" @checked(old('whole_course'))>
            {{ __('Asignarle el curso entero') }}
        </label>

        <fieldset class="form-group">
            <legend class="form-label">{{ __('asignaturas del curso') }}</legend>
            <span class="form-help" data-course-subjects="" hidden>{{ __('Elige un curso para ver sus asignaturas.') }}</span>
            @foreach ($courses as $course)
                <div class="subject-picks" data-course-subjects="{{ $course->id }}">
                    <span class="xs muted">{{ $course->code }} · {{ $course->name }}</span>
                    @forelse ($course->courseSubjects as $item)
                        <label class="check">
                            <input type="checkbox" name="course_subjects[]" value="{{ $item->id }}" @checked(in_array($item->id, (array) old('course_subjects', [])))>
                            <span><span class="acc">{{ $item->subject->code }}</span>&nbsp;&nbsp;{{ $item->subject->name }} <span class="muted">· {{ $item->teacher ? __('ahora: :name', ['name' => $item->teacher->full_name]) : __('sin profesor/a') }}</span></span>
                        </label>
                    @empty
                        <span class="form-help">{{ __('Este curso todavía no tiene asignaturas. Puedes crear una abajo.') }}</span>
                    @endforelse
                </div>
            @endforeach
            <span class="form-help">{{ __('Las asignaturas que ya tienen profesor/a pasan al nuevo.') }}</span>
            @error('course_subjects.*')<span class="form-error">{{ $message }}</span>@enderror
        </fieldset>

        <fieldset class="form-group">
            <legend class="form-label">{{ __('o crea una asignatura nueva en el curso') }}</legend>
            <div class="form-row form-row--3">
                <div class="form-group">
                    <label for="subject_code" class="form-label">{{ __('código') }}</label>
                    <input type="text" id="subject_code" name="subject_code" class="form-input @error('subject_code') is-invalid @enderror" value="{{ old('subject_code') }}" maxlength="20" autocomplete="off" placeholder="PROG" style="text-transform: uppercase;">
                    @error('subject_code')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="subject_name" class="form-label">{{ __('nombre') }}</label>
                    <input type="text" id="subject_name" name="subject_name" class="form-input @error('subject_name') is-invalid @enderror" value="{{ old('subject_name') }}" maxlength="100" autocomplete="off" placeholder="{{ __('Programación') }}">
                    @error('subject_name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="subject_hours" class="form-label">{{ __('horas') }}</label>
                    <input type="number" id="subject_hours" name="subject_hours" class="form-input @error('subject_hours') is-invalid @enderror" value="{{ old('subject_hours') }}" min="1" max="10000" placeholder="120">
                    @error('subject_hours')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </fieldset>

        @error('assignment')<span class="form-error">{{ $message }}</span>@enderror

        <div class="hr"></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ __('Crear profesor') }}</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">{{ __('cancelar') }}</a>
        </div>
    </div>
</form>
@endsection
