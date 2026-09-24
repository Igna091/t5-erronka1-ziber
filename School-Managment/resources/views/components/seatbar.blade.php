{{--
    Free spots as a progress bar: the fill is the share of free spots, so it gets
    shorter (from right to left) as spots are taken. Orange when 20% or less is left.
--}}
@props(['course'])

@php
    $ratio = $course->freeRatio();
    $low = $course->capacity && $ratio <= 0.2;
@endphp

<span {{ $attributes->merge(['class' => 'seatbar'.($low ? ' is-low' : '')]) }} aria-hidden="true">
    <span class="seatbar__fill" style="--w: {{ round($ratio * 100, 1) }}%"></span>
</span>
