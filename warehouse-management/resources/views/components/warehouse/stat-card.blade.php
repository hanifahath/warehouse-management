@props(['title', 'value', 'icon' => null, 'color' => 'indigo', 'href' => null])

@php
$colorClasses = match($color) {
    'indigo' => 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20',
    'red' => 'border-red-600 bg-red-50 dark:bg-red-900/20',
    'yellow' => 'border-yellow-600 bg-yellow-50 dark:bg-yellow-900/20',
    'green' => 'border-green-600 bg-green-50 dark:bg-green-900/20',
    'blue' => 'border-blue-600 bg-blue-50 dark:bg-blue-900/20',
    'purple' => 'border-purple-600 bg-purple-50 dark:bg-purple-900/20',
    default => 'border-gray-600 bg-gray-50 dark:bg-gray-900/20',
};

$iconColorClasses = match($color) {
    'indigo' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-800 dark:text-indigo-200',
    'red' => 'bg-red-100 text-red-600 dark:bg-red-800 dark:text-red-200',
    'yellow' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-800 dark:text-yellow-200',
    'green' => 'bg-green-100 text-green-600 dark:bg-green-800 dark:text-green-200',
    'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-800 dark:text-blue-200',
    'purple' => 'bg-purple-100 text-purple-600 dark:bg-purple-800 dark:text-purple-200',
    default => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-200',
};

$wrapper = $href ? 'a' : 'div';
@endphp

<{{ $wrapper }} {{ $href ? 'href=' . $href : '' }} class="block bg-white dark:bg-gray-800 rounded-lg shadow border-l-4 {{ $colorClasses }} p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="p-3 rounded-full {{ $iconColorClasses }}">
                {!! $icon !!}
            </div>
        @endif
    </div>
</{{ $wrapper }}>