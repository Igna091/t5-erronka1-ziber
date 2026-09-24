@extends('layouts.admin')

@section('title', 'Crear alumno')
@section('header', 'Crear alumno')

@section('content')
<div class="page-header">
    <h1>Crear nuevo alumno</h1>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-body" style="padding:2rem;">
        <form method="POST" action="{{ route('admin.students.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Nombre *</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required placeholder="Nombre del alumno">
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="surname" class="form-label">Apellidos *</label>
                    <input type="text" id="surname" name="surname" class="form-input" value="{{ old('surname') }}" required placeholder="Apellidos del alumno">
                    @error('surname')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required placeholder="email@ejemplo.com">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="dni" class="form-label">DNI / NIE *</label>
                    <input type="text" id="dni" name="dni" class="form-input" value="{{ old('dni') }}" required placeholder="12345678A">
                    @error('dni')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Teléfono</label>
                <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="600000000">
                @error('phone')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-help mb-3">
                <strong>Nota:</strong> Una vez creado, el alumno podrá registrarse en la plataforma utilizando su email y DNI.
            </div>

            <div style="display:flex;gap:0.75rem;">
                <button type="submit" class="btn btn-primary">Crear alumno</button>
                <a href="{{ route('admin.students.index') }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
