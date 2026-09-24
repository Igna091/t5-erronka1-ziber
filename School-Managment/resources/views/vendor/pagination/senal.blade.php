@if ($paginator->hasPages())
    <nav class="pager" aria-label="Paginación">
        <span>
            @if ($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} de {{ $paginator->total() }}
            @else
                página {{ $paginator->currentPage() }}
            @endif
        </span>

        <div class="pager__pages">
            @if ($paginator->onFirstPage())
                <span class="pager__item is-disabled" aria-disabled="true" aria-label="Anterior">&lt;</span>
            @else
                <a class="pager__item" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior">&lt;</a>
            @endif

            @isset($elements)
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="pager__item is-disabled" aria-disabled="true">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="pager__item is-current" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="pager__item" href="{{ $url }}" aria-label="Página {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endisset

            @if ($paginator->hasMorePages())
                <a class="pager__item" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Siguiente">&gt;</a>
            @else
                <span class="pager__item is-disabled" aria-disabled="true" aria-label="Siguiente">&gt;</span>
            @endif
        </div>
    </nav>
@endif
