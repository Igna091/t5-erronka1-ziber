{{--
    Texts used by app.js, in the current language, as JSON in a data attribute
    (no inline <script>, so nothing clashes with the CSP).
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
<div id="i18n" data-texts="{{ json_encode($jsTexts) }}" hidden></div>
