@extends('layouts.admin')

@section('title', 'Editar alumno')
@section('header', 'Editar alumno')

@section('content')
<div class="page-header">
    <h1>Editar alumno</h1>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-body" style="padding:2rem;">
        <form method="POST" action="{{ route('admin.students.update', $student) }}">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Nombre *</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $student->name) }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="surname" class="form-label">Apellidos *</label>
                    <input type="text" id="surname" name="surname" class="form-input" value="{{ old('surname', $student->surname) }}" required>
                    @error('surname')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $student->email) }}" required>
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="dni" class="form-label">DNI / NIE *</label>
                    <input type="text" id="dni" name="dni" class="form-input" value="{{ old('dni', $student->dni) }}" required>
                    @error('dni')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Teléfono</label>
                <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone', $student->phone) }}">
                @error('phone')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div style="display:flex;gap:0.75rem;">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('admin.students.index') }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
