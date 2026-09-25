@extends('layouts.admin')

@section('title', __('Matricular alumno'))
@section('path', __('matrículas/nueva'))

@section('content')
<a href="{{ route('admin.enrollments.index') }}" class="back-link"><x-icon name="arrow-left" :size="16" />{{ __('volver a matrículas') }}</a>

<div class="page-header">
    <div class="page-header__title">
        <h1 class="h-admin"><span class="fx-type">{{ __('Matricular alumno.') }}</span></h1>
        <span class="text-2">{{ __('Solo aparecen los cursos activos que no han terminado. Se puede matricular a alumnos que aún no han activado su cuenta.') }}</span>
    </div>
</div>

<form method="POST" action="{{ route('admin.enrollments.store') }}" class="panel form-card fx-fade" style="--d: 0.2s" novalidate>
    @csrf
    <div class="panel__head"><h2 class="lbl">{{ __('nueva matrícula') }}</h2></div>
    <div class="panel__body form">
        <div class="form-group">
            <label for="student_id" class="form-label">{{ __('alumno') }} <span class="req">*</span></label>
            <select id="student_id" name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                <option value="">{{ __('selecciona un alumno…') }}</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected((int) old('student_id', $selectedStudentId) === $student->id)>
                        {{ $student->surname ? $student->surname.', '.$student->name : $student->name }} · {{ $student->email }}{{ $student->is_registered ? '' : ' · '.__('pendiente de activar') }}
                    </option>
                @endforeach
            </select>
            @error('student_id')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="course_id" class="form-label">{{ __('curso') }} <span class="req">*</span></label>
            <select id="course_id" name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                <option value="">{{ __('selecciona un curso…') }}</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected((int) old('course_id', $selectedCourseId) === $course->id) @disabled(!$course->hasAvailableSpots())>
                        {{ $course->code }} · {{ $course->name }} · {{ $course->capacity ? __(':available de :capacity plazas libres', ['available' => $course->available_spots, 'capacity' => $course->capacity]) : __('sin límite de plazas') }}
                    </option>
                @endforeach
            </select>
            <span class="form-help">{{ __('Los cursos sin plazas libres aparecen desactivados.') }}</span>
            @error('course_id')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="hr"></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ __('Matricular') }}</button>
            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-ghost">{{ __('cancelar') }}</a>
        </div>
    </div>
</form>
@endsection
