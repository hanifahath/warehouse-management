@props(['title', 'value', 'icon' => null])

<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 flex items-center gap-4">
    @if($icon)
        <div class="text-indigo-600 text-2xl">
            {!! $icon !!}
        </div>
    @endif

    <div>
        <div class="text-gray-500 text-sm">{{ $title }}</div>
        <div class="text-gray-900 text-2xl font-bold">{{ $value }}</div>
    </div>
</div>
