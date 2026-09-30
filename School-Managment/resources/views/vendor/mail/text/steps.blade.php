@props(['items', 'current' => 0])
@foreach ($items as $i => $item)
{{ $i < $current ? '[x]' : ($i === $current ? '[>]' : '[ ]') }} {{ $item }}
@endforeach
