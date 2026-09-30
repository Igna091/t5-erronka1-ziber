@props(['label' => null])
@if ($label)
{{ $label }}
@endif
{{ $slot }}
