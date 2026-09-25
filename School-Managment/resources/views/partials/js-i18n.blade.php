{{--
    Texts used by app.js, in the current language. It's JSON, not a script:
    the browser doesn't run it, so the CSP (script-src 'self') allows it.
--}}
@php
    $jsTexts = [
        'show' => __('mostrar'),
        'hide' => __('ocultar'),
        'wait' => __('Espera :seconds s'),
        'confirm' => __('¿Seguro que quieres continuar?'),
        'themeDark' => __('Tema cambiado a modo oscuro'),
        'themeLight' => __('Tema cambiado a modo claro'),
        'fontSmall' => __('Tamaño de texto: pequeño'),
        'fontNormal' => __('Tamaño de texto: normal'),
        'fontLarge' => __('Tamaño de texto: grande'),
    ];
@endphp
<script type="application/json" id="i18n">@json($jsTexts)</script>
