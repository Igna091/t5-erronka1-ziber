{{-- Student form fields (create + edit). $student is optional. --}}
<div class="form-row">
    <div class="form-group">
        <label for="name" class="form-label">nombre <span class="req">*</span></label>
        <input type="text" id="name" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name', $student->name ?? '') }}" required maxlength="100" autocomplete="off" placeholder="Nombre">
        @error('name')<span class="form-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="surname" class="form-label">apellidos <span class="req">*</span></label>
        <input type="text" id="surname" name="surname" class="form-input @error('surname') is-invalid @enderror" value="{{ old('surname', $student->surname ?? '') }}" required maxlength="150" autocomplete="off" placeholder="Apellidos">
        @error('surname')<span class="form-error">{{ $message }}</span>@enderror
    </div>
</div>
<div class="form-row">
    <div class="form-group">
        <label for="email" class="form-label">email <span class="req">*</span></label>
        <input type="email" id="email" name="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email', $student->email ?? '') }}" required maxlength="255" autocomplete="off" placeholder="alumno@email.com">
        @error('email')<span class="form-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="dni" class="form-label">dni / nie <span class="req">*</span></label>
        <input type="text" id="dni" name="dni" class="form-input @error('dni') is-invalid @enderror" value="{{ old('dni', $student->dni ?? '') }}" required maxlength="20" autocomplete="off" placeholder="12345678A">
        @error('dni')<span class="form-error">{{ $message }}</span>@enderror
    </div>
</div>
<div class="form-group" style="max-width: calc(50% - 0.625rem); min-width: min(100%, 16rem);">
    <label for="phone" class="form-label">teléfono</label>
    <input type="tel" id="phone" name="phone" class="form-input @error('phone') is-invalid @enderror" value="{{ old('phone', $student->phone ?? '') }}" maxlength="20" autocomplete="off" placeholder="600000000">
    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
</div>
