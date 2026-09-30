@props(['items', 'current' => 0])
{{-- Checklist like the site's .steps: the steps before "current" are done --}}
<table class="steps" width="100%" cellpadding="0" cellspacing="0" role="presentation">
@foreach ($items as $i => $item)
@php($state = $i < $current ? 'done' : ($i === $current ? 'now' : 'next'))
<tr>
<td class="step-mark step-mark--{{ $state }}">{{ ['done' => '[x]', 'now' => '[>]', 'next' => '[ ]'][$state] }}</td>
<td class="step-text step-text--{{ $state }}">{{ $item }}</td>
</tr>
@endforeach
</table>
