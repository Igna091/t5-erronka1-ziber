<x-mail::message>
<x-mail::kicker>{{ $teacher ? __('activación // cuenta de profesor') : __('activación // cuenta de alumno') }}</x-mail::kicker>

# {{ __('¡Hola, :name!', ['name' => $name]) }}

{{ $teacher ? __('El centro te ha dado de alta como profesor/a en ZiberEibar.') : __('El centro te ha dado de alta como alumno/a en ZiberEibar.') }} {{ __('Para empezar, activa tu cuenta y elige tu contraseña:') }}

<x-mail::button :url="$actionUrl" align="left">
{{ $actionText }}
</x-mail::button>

<x-mail::panel :label="__('enlace de un solo uso')">
{{ __('El enlace caduca en 7 días y solo se puede usar una vez.') }}
</x-mail::panel>

<x-mail::steps :current="1" :items="[
    __('el centro te da de alta'),
    __('abres el enlace del email'),
    __('eliges tu contraseña'),
    $teacher ? __('entras en tu cuenta') : __('te matriculas en tus cursos'),
]" />

{{ __('Si no esperabas este correo, puedes ignorarlo.') }}

{{ __('Un saludo, el equipo de ZiberEibar') }}

<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    ['actionText' => $actionText]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
</x-mail::message>
