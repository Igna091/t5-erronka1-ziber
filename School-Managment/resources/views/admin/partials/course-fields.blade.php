{{-- Course form fields (create + edit). $course is optional. --}}
<div class="form-row">
    <div class="form-group">
        <label for="name" class="form-label">{{ __('nombre') }} <span class="req">*</span></label>
        <input type="text" id="name" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name', $course->name ?? '') }}" required maxlength="100" placeholder="{{ __('Desarrollo Web Full Stack') }}">
        @error('name')<span class="form-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="code" class="form-label">{{ __('código') }} <span class="req">*</span></label>
        <input type="text" id="code" name="code" class="form-input @error('code') is-invalid @enderror" value="{{ old('code', $course->code ?? '') }}" required maxlength="20" placeholder="DW-001" style="text-transform: uppercase;">
        @error('code')<span class="form-error">{{ $message }}</span>@enderror
    </div>
</div>
<div class="form-group">
    <label for="description" class="form-label">{{ __('descripción') }}</label>
    <textarea id="description" name="description" class="form-textarea @error('description') is-invalid @enderror" maxlength="5000" placeholder="{{ __('Qué se aprende en el curso…') }}">{{ old('description', $course->description ?? '') }}</textarea>
    @error('description')<span class="form-error">{{ $message }}</span>@enderror
</div>
<div class="form-row">
    <div class="form-group">
        <label for="duration_hours" class="form-label">{{ __('duración (horas)') }}</label>
        <input type="number" id="duration_hours" name="duration_hours" class="form-input @error('duration_hours') is-invalid @enderror" value="{{ old('duration_hours', $course->duration_hours ?? '') }}" min="1" max="10000" placeholder="600">
        @error('duration_hours')<span class="form-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="capacity" class="form-label">{{ __('plazas') }}</label>
        <input type="number" id="capacity" name="capacity" class="form-input @error('capacity') is-invalid @enderror" value="{{ old('capacity', $course->capacity ?? '') }}" min="1" max="1000" placeholder="{{ __('vacío = sin límite') }}" aria-describedby="capacity-help">
        <span class="form-help" id="capacity-help">{{ __('Déjalo vacío si no hay límite de plazas.') }}</span>
        @error('capacity')<span class="form-error">{{ $message }}</span>@enderror
    </div>
</div>
<div class="form-row">
    <div class="form-group">
        <label for="start_date" class="form-label">{{ __('inicio') }}</label>
        <input type="date" id="start_date" name="start_date" class="form-input @error('start_date') is-invalid @enderror" value="{{ old('start_date', isset($course) ? $course->start_date?->format('Y-m-d') : '') }}" aria-describedby="start-help">
        <span class="form-help" id="start-help">{{ __('El año académico se calcula con esta fecha (empieza en septiembre).') }}</span>
        @error('start_date')<span class="form-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="end_date" class="form-label">{{ __('fin') }}</label>
        <input type="date" id="end_date" name="end_date" class="form-input @error('end_date') is-invalid @enderror" value="{{ old('end_date', isset($course) ? $course->end_date?->format('Y-m-d') : '') }}">
        @error('end_date')<span class="form-error">{{ $message }}</span>@enderror
    </div>
</div>
<div class="form-group" style="max-width: calc(50% - 0.625rem); min-width: min(100%, 16rem);">
    <label for="status" class="form-label">{{ __('estado') }} <span class="req">*</span></label>
    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
        <option value="active" @selected(old('status', $course->status ?? 'active') === 'active')>{{ __('activo · admite matrículas') }}</option>
        <option value="inactive" @selected(old('status', $course->status ?? 'active') === 'inactive')>{{ __('inactivo · oculto al público') }}</option>
    </select>
    @error('status')<span class="form-error">{{ $message }}</span>@enderror
</div>
