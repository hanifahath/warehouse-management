@props([
    'title',
    'value',
    'icon' => null,
    'color' => 'text-gray-900',
    'link' => null,
    'linkText' => 'Lihat Detail'
])

<div class="bg-white rounded-xl shadow border border-gray-200 p-6">
    <div class="flex justify-between items-start">
        <div>
            @if($icon)
                <div class="text-2xl mb-2">{{ $icon }}</div>
            @endif
            <p class="text-sm text-gray-500 mb-1">{{ $title }}</p>
            <p class="text-2xl font-bold {{ $color }}">{{ $value }}</p>
        </div>
        @if($link)
            <a href="{{ $link }}" 
               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap">
                {{ $linkText }}
            </a>
        @endif
    </div>
</div>