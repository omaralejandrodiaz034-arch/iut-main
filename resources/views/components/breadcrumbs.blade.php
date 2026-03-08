@props(['items' => []])

@if(count($items) > 0)
<nav aria-label="Breadcrumb" class="mb-5">
    <ol class="flex flex-wrap items-center gap-1 text-sm text-gray-500">
        <li class="flex items-center gap-1">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-gray-400 hover:text-[#800020] transition-colors font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Inicio
            </a>
        </li>

        @foreach($items as $index => $item)
            <li class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                @if(isset($item['url']) && $index < count($items) - 1)
                    <a href="{{ $item['url'] }}" class="text-gray-500 hover:text-[#800020] transition-colors font-medium truncate max-w-[180px]">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-gray-800 font-semibold truncate max-w-[180px]">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
