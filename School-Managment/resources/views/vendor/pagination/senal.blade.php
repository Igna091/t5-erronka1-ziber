@if ($paginator->hasPages())
    <nav class="pager" aria-label="{{ __('Paginación') }}">
        <span>
            @if ($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                {{ __(':from–:to de :total', ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem(), 'total' => $paginator->total()]) }}
            @else
                {{ __('página :page', ['page' => $paginator->currentPage()]) }}
            @endif
        </span>

        <div class="pager__pages">
            @if ($paginator->onFirstPage())
                <span class="pager__item is-disabled" aria-disabled="true" aria-label="{{ __('Anterior') }}">&lt;</span>
            @else
                <a class="pager__item" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('Anterior') }}">&lt;</a>
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
                                <a class="pager__item" href="{{ $url }}" aria-label="{{ __('Página :page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endisset

            @if ($paginator->hasMorePages())
                <a class="pager__item" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('Siguiente') }}">&gt;</a>
            @else
                <span class="pager__item is-disabled" aria-disabled="true" aria-label="{{ __('Siguiente') }}">&gt;</span>
            @endif
        </div>
    </nav>
@endif
