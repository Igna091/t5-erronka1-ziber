{{--
    Live password requirements (app.js [data-password-rules]), same rules as the backend.
    Needs: $id for aria-describedby of the password input.
--}}
<div class="rules" id="{{ $id }}" data-password-rules aria-live="polite">
    <div class="rules__head"><span class="lbl">{{ __('requisitos') }}</span><span class="ascii muted" data-rules-meter>[----------]</span></div>
    <span class="rule" data-rule="length">{{ __('8 caracteres o más') }}</span>
    <span class="rule" data-rule="letters">{{ __('contiene letras') }}</span>
    <span class="rule" data-rule="numbers">{{ __('contiene números') }}</span>
    <span class="rule" data-rule="match">{{ __('las dos contraseñas coinciden') }}</span>
</div>
