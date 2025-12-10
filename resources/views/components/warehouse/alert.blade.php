@props(['type' => 'info'])

@php
    $styles = [
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'danger'  => 'bg-red-50 border-red-200 text-red-800',
        'info'    => 'bg-blue-50 border-blue-200 text-blue-800',
    ];
@endphp

<div class="p-4 rounded border {{ $styles[$type] }}">
    {{ $slot }}
</div>
