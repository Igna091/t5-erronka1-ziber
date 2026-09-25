<?php

/*
| Validation messages in Spanish, only for the rules the app uses.
| Anything missing falls back to Laravel's English messages.
*/

return [
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña no es correcta.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'different' => 'Los campos :attribute y :other deben ser diferentes.',
    'email' => 'El campo :attribute debe ser un email válido.',
    'exists' => 'El :attribute seleccionado no es válido.',
    'in' => 'El :attribute seleccionado no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'numeric' => 'El campo :attribute no puede ser mayor que :max.',
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser como mínimo :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'password' => [
        'letters' => 'El campo :attribute debe contener al menos una letra.',
        'mixed' => 'El campo :attribute debe contener al menos una mayúscula y una minúscula.',
        'numbers' => 'El campo :attribute debe contener al menos un número.',
        'symbols' => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'El :attribute indicado ha aparecido en una filtración de datos. Elige otro.',
    ],
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser un texto.',
    'unique' => 'Este :attribute ya está en uso.',

    'attributes' => [
        'capacity' => 'plazas',
        'code' => 'código',
        'course_id' => 'curso',
        'current_password' => 'contraseña actual',
        'description' => 'descripción',
        'dni' => 'DNI',
        'duration_hours' => 'duración',
        'email' => 'email',
        'end_date' => 'fecha de fin',
        'locale' => 'idioma',
        'name' => 'nombre',
        'password' => 'contraseña',
        'phone' => 'teléfono',
        'search' => 'búsqueda',
        'start_date' => 'fecha de inicio',
        'status' => 'estado',
        'student_id' => 'alumno',
        'surname' => 'apellidos',
        'token' => 'enlace',
    ],
];
