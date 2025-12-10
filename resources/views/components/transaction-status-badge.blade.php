{{-- resources/views/components/transaction-status-badge.blade.php --}}
@props(['transaction'])

@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'approved' => 'bg-green-100 text-green-800', 
        'rejected' => 'bg-red-100 text-red-800',
        'shipped' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-indigo-100 text-indigo-800',
    ];
    
    $statusIcons = [
        'pending' => '⏳',
        'approved' => '✅',
        'rejected' => '❌',
        'shipped' => '🚚',
        'completed' => '📦',
    ];
    
    $status = strtolower($transaction->status);
    $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
    $icon = $statusIcons[$status] ?? '•';
    $label = ucfirst($status);
@endphp

<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
    {{ $icon }} {{ $label }}
</span>