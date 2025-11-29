@props(['title' => null, 'noPadding' => false])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow']) }}>
    @if($title)
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
        </div>
    @endif
    
    <div class="{{ $noPadding ? '' : 'p-6' }}">
        {{ $slot }}
    </div>
</div>