@extends('layouts.app')

@section('title', 'Restock Orders')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Restock Orders</h1>

    @if(auth()->user()->hasRole('Manager'))
        <a href="{{ route('restocks.create') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow">
            + Create Restock Order
        </a>
    @endif
</div>

{{-- TABLE --}}
<div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Supplier</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Items</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Qty</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Created At</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">

            @forelse($restocks as $restock)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-700">
                    #{{ $restock->id }}
                </td>

                {{-- Supplier --}}
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $restock->supplier->name ?? '-' }}
                </td>

                {{-- Items Count --}}
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $restock->items->count() }} item(s)
                </td>

                {{-- Total Qty --}}
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $restock->items->sum('quantity') }}
                </td>

                {{-- Status Badge --}}
                <td class="px-4 py-3">

                    @php
                        $status = $restock->status;
                        $badge = match($status) {
                            'Pending Approval' => 'bg-yellow-100 text-yellow-800',
                            'Supplier Confirmed' => 'bg-blue-100 text-blue-800',
                            'In Transit' => 'bg-indigo-100 text-indigo-800',
                            'Received' => 'bg-green-100 text-green-800',
                            'Rejected' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp

                    <span class="px-3 py-1 text-sm font-medium rounded-lg {{ $badge }}">
                        {{ $status }}
                    </span>
                </td>

                {{-- Date --}}
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $restock->created_at->format('d M Y') }}
                </td>

                {{-- Actions --}}
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('restocks.show', $restock->id) }}"
                       class="text-indigo-600 hover:underline text-sm">
                        View
                    </a>

                    {{-- Supplier confirmation CTA --}}
                    @if(auth()->user()->hasRole('Supplier') && $restock->status === 'Pending Approval')
                        <button class="ml-3 text-green-600 hover:underline text-sm">
                            Confirm
                        </button>
                    @endif
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                    No restock orders found.
                </td>
            </tr>
            @endforelse

        </tbody>

    </table>

</div>

@endsection
