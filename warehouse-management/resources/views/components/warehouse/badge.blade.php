@props(['type' => 'default', 'size' => 'md'])

@php
$baseClasses = 'inline-flex items-center font-semibold rounded-full';

$sizeClasses = match($size) {
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-sm',
    'lg' => 'px-4 py-1.5 text-base',
    default => 'px-3 py-1 text-sm',
};

$typeClasses = match($type) {
    'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'pending' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
};

$classes = $baseClasses . ' ' . $sizeClasses . ' ' . $typeClasses;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>