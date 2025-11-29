@if ($paginator->hasPages())
    <nav class="flex justify-between items-center mt-4">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 bg-gray-300 text-gray-600 rounded">Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" 
               class="px-3 py-1 bg-blue-600 text-white rounded">Prev</a>
        @endif

        {{-- Page Numbers --}}
        <div class="space-x-1">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-1">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1 bg-blue-600 text-white rounded">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" 
                               class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" 
               class="px-3 py-1 bg-blue-600 text-white rounded">Next</a>
        @else
            <span class="px-3 py-1 bg-gray-300 text-gray-600 rounded">Next</span>
        @endif
    </nav>
@endif