@props(['type' => 'info'])

@php
$classes = match($type) {
    'success' => 'bg-green-50 border-green-400 text-green-800 dark:bg-green-900/20 dark:border-green-600 dark:text-green-200',
    'error' => 'bg-red-50 border-red-400 text-red-800 dark:bg-red-900/20 dark:border-red-600 dark:text-red-200',
    'warning' => 'bg-yellow-50 border-yellow-400 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-600 dark:text-yellow-200',
    'info' => 'bg-blue-50 border-blue-400 text-blue-800 dark:bg-blue-900/20 dark:border-blue-600 dark:text-blue-200',
    default => 'bg-gray-50 border-gray-400 text-gray-800 dark:bg-gray-900/20 dark:border-gray-600 dark:text-gray-200',
};
@endphp

<div {{ $attributes->merge(['class' => "border-l-4 p-4 mb-4 rounded $classes"]) }} role="alert">
    {{ $slot }}
</div>