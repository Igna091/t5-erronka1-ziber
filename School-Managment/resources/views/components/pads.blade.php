@props(['count' => 3])

<div class="panel__pads" aria-hidden="true">
    @for ($i = 0; $i < $count; $i++)
        <span class="pad"></span>
    @endfor
</div>
