@extends('layouts.admin')

@section('title', 'Editar curso')
@section('header', 'Editar curso')

@section('content')
<div class="page-header">
    <h1>Editar curso</h1>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-body" style="padding:2rem;">
        <form method="POST" action="{{ route('admin.courses.update', $course) }}">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Nombre del curso *</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $course->name) }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="code" class="form-label">Código *</label>
                    <input type="text" id="code" name="code" class="form-input" value="{{ old('code', $course->code) }}" required>
                    @error('code')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Descripción</label>
                <textarea id="description" name="description" class="form-textarea">{{ old('description', $course->description) }}</textarea>
                @error('description')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="duration_hours" class="form-label">Duración (horas)</label>
                    <input type="number" id="duration_hours" name="duration_hours" class="form-input" value="{{ old('duration_hours', $course->duration_hours) }}" min="1">
                    @error('duration_hours')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="capacity" class="form-label">Plazas</label>
                    <input type="number" id="capacity" name="capacity" class="form-input" value="{{ old('capacity', $course->capacity) }}" min="1">
                    @error('capacity')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_date" class="form-label">Fecha de inicio</label>
                    <input type="date" id="start_date" name="start_date" class="form-input" value="{{ old('start_date', $course->start_date?->format('Y-m-d')) }}">
                    @error('start_date')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="end_date" class="form-label">Fecha de finalización</label>
                    <input type="date" id="end_date" name="end_date" class="form-input" value="{{ old('end_date', $course->end_date?->format('Y-m-d')) }}">
                    @error('end_date')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Estado *</label>
                <select id="status" name="status" class="form-select">
                    <option value="active" {{ old('status', $course->status) === 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ old('status', $course->status) === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('status')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div style="display:flex;gap:0.75rem;">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
