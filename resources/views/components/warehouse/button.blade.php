@props(['variant' => 'primary', 'href' => null])

@php
    $classes = [
        'primary'   => 'bg-indigo-600 hover:bg-indigo-700 text-white',
        'secondary' => 'border border-gray-300 text-gray-700 hover:bg-gray-100',
        'danger'    => 'bg-red-600 hover:bg-red-700 text-white',
        'soft'      => 'bg-gray-100 hover:bg-gray-200 text-gray-700',
    ];
@endphp

@if($href)
    <a href="{{ $href }}"
       class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium {{ $classes[$variant] }}">
       {{ $slot }}
    </a>
@else
    <button
        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium {{ $classes[$variant] }}">
        {{ $slot }}
    </button>
@endif
