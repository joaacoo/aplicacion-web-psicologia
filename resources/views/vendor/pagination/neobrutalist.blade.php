@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" style="display: flex; gap: 0.8rem; flex-wrap: wrap; align-items: center; justify-content: center;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="neobrutalist-btn" style="padding: 0.5rem 1rem; cursor: not-allowed; opacity: 0.3; background: #eee; box-shadow: none; border-color: #999; color: #999;">
                <i class="fa-solid fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="neobrutalist-btn bg-amarillo" style="padding: 0.5rem 1rem; text-decoration: none; color: #000;">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span style="font-weight: 800;">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="neobrutalist-btn bg-celeste" style="padding: 0.5rem 1rem; border: 3px solid #000; box-shadow: none; transform: translate(2px, 2px);">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="neobrutalist-btn bg-white" style="padding: 0.5rem 1rem; text-decoration: none; color: #000;">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="neobrutalist-btn bg-amarillo" style="padding: 0.5rem 1rem; text-decoration: none; color: #000;">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        @else
            <span class="neobrutalist-btn" style="padding: 0.5rem 1rem; cursor: not-allowed; opacity: 0.3; background: #eee; box-shadow: none; border-color: #999; color: #999;">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
        @endif
    </nav>
@endif
