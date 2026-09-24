@extends('layouts.admin')

@section('title', 'Crear curso')
@section('header', 'Crear curso')

@section('content')
<div class="page-header">
    <h1>Crear nuevo curso</h1>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-body" style="padding:2rem;">
        <form method="POST" action="{{ route('admin.courses.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Nombre del curso *</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required placeholder="Desarrollo Web">
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="code" class="form-label">Código *</label>
                    <input type="text" id="code" name="code" class="form-input" value="{{ old('code') }}" required placeholder="DW-001">
                    @error('code')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Descripción</label>
                <textarea id="description" name="description" class="form-textarea" placeholder="Descripción del curso...">{{ old('description') }}</textarea>
                @error('description')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="duration_hours" class="form-label">Duración (horas)</label>
                    <input type="number" id="duration_hours" name="duration_hours" class="form-input" value="{{ old('duration_hours') }}" min="1" placeholder="120">
                    @error('duration_hours')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="capacity" class="form-label">Plazas</label>
                    <input type="number" id="capacity" name="capacity" class="form-input" value="{{ old('capacity') }}" min="1" placeholder="30">
                    @error('capacity')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_date" class="form-label">Fecha de inicio</label>
                    <input type="date" id="start_date" name="start_date" class="form-input" value="{{ old('start_date') }}">
                    @error('start_date')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="end_date" class="form-label">Fecha de finalización</label>
                    <input type="date" id="end_date" name="end_date" class="form-input" value="{{ old('end_date') }}">
                    @error('end_date')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Estado *</label>
                <select id="status" name="status" class="form-select">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('status')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div style="display:flex;gap:0.75rem;">
                <button type="submit" class="btn btn-primary">Crear curso</button>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
