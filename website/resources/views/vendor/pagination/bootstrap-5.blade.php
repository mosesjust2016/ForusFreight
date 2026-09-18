@if ($paginator->hasPages())
    <nav style="display: flex; justify-content: center; align-items: center; width: 100%;">
        <ul style="display: flex; justify-content: center; align-items: center; gap: 0.25rem; list-style: none; padding: 0; margin: 0; flex-wrap: nowrap;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li style="display: inline-flex; margin: 0; padding: 0;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; min-width: 36px; border-radius: 8px; border: 1px solid #e2e8f0; color: #94a3b8; font-size: 0.9rem; font-weight: 600; opacity: 0.5; cursor: not-allowed;">&lsaquo;</span>
                </li>
            @else
                <li style="display: inline-flex; margin: 0; padding: 0;">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; min-width: 36px; border-radius: 8px; border: 1px solid #e2e8f0; color: #64748b; background-color: white; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.backgroundColor='#007f7f'; this.style.color='white'; this.style.borderColor='#007f7f';" onmouseout="this.style.backgroundColor='white'; this.style.color='#64748b'; this.style.borderColor='#e2e8f0';">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li style="display: inline-flex; margin: 0; padding: 0;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; min-width: 36px; border-radius: 8px; border: 1px solid transparent; color: #64748b; font-size: 0.9rem; font-weight: 600;">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li style="display: inline-flex; margin: 0; padding: 0;">
                                <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; min-width: 36px; border-radius: 8px; border: 1px solid #007f7f; background-color: #007f7f; color: white; font-size: 0.9rem; font-weight: 600;">{{ $page }}</span>
                            </li>
                        @else
                            <li style="display: inline-flex; margin: 0; padding: 0;">
                                <a href="{{ $url }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; min-width: 36px; border-radius: 8px; border: 1px solid #e2e8f0; color: #64748b; background-color: white; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.backgroundColor='#007f7f'; this.style.color='white'; this.style.borderColor='#007f7f';" onmouseout="this.style.backgroundColor='white'; this.style.color='#64748b'; this.style.borderColor='#e2e8f0';">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li style="display: inline-flex; margin: 0; padding: 0;">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; min-width: 36px; border-radius: 8px; border: 1px solid #e2e8f0; color: #64748b; background-color: white; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.backgroundColor='#007f7f'; this.style.color='white'; this.style.borderColor='#007f7f';" onmouseout="this.style.backgroundColor='white'; this.style.color='#64748b'; this.style.borderColor='#e2e8f0';">&rsaquo;</a>
                </li>
            @else
                <li style="display: inline-flex; margin: 0; padding: 0;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 0.75rem; min-width: 36px; border-radius: 8px; border: 1px solid #e2e8f0; color: #94a3b8; font-size: 0.9rem; font-weight: 600; opacity: 0.5; cursor: not-allowed;">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
