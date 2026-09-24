@props(['type' => 'success'])

@php
    $tag = ['success' => '[ok]', 'error' => '[err]', 'warning' => '[warn]'][$type] ?? '[i]';
@endphp

<div {{ $attributes->merge(['class' => "alert alert-{$type}", 'role' => $type === 'success' ? 'status' : 'alert']) }}>
    <span class="alert__tag">{{ $tag }}</span>
    <span class="alert__text">{{ $slot }}</span>
</div>
