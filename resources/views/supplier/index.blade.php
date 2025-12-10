@extends('layouts.app')

@section('title', 'My Restock Orders')

@section('content')
<div class="px-6 py-6">
    
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Restock Orders</h1>
            <p class="text-sm text-gray-600 mt-1">
                Restock orders assigned to {{ auth()->user()->name }}
            </p>
        </div>
        
        <div class="text-right">
            <div class="text-3xl font-bold text-indigo-600">{{ $totalOrders }}</div>
            <div class="text-sm text-gray-600">Total Orders</div>
        </div>
    </div>

    {{-- QUICK STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-white p-3 rounded-lg border">
            <div class="text-center">
                <div class="text-xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                <div class="text-xs text-gray-600 mt-1">Pending</div>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border">
            <div class="text-center">
                <div class="text-xl font-bold text-blue-600">{{ $confirmedCount }}</div>
                <div class="text-xs text-gray-600 mt-1">Confirmed</div>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border">
            <div class="text-center">
                <div class="text-xl font-bold text-purple-600">{{ $inTransitCount }}</div>
                <div class="text-xs text-gray-600 mt-1">In Transit</div>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border">
            <div class="text-center">
                <div class="text-xl font-bold text-green-600">{{ $receivedCount }}</div>
                <div class="text-xs text-gray-600 mt-1">Received</div>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border">
            <div class="text-center">
                <div class="text-xl font-bold text-red-600">{{ $cancelledCount }}</div>
                <div class="text-xs text-gray-600 mt-1">Cancelled</div>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white p-4 rounded-xl shadow border mb-6">
        <form method="GET" action="{{ route('supplier.restocks.index') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-500 mb-1">Search PO Number</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Enter PO number..."
                       class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div class="min-w-[150px]">
                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Confirmed" {{ request('status') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="Received" {{ request('status') == 'Received' ? 'selected' : '' }}>Received</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                    Filter
                </button>
                <a href="{{ route('supplier.restocks.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 text-sm">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- ORDERS TABLE --}}
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
        @if($restocks->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">PO Number</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Order Date</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Expected Delivery</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Items</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Total Amount</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($restocks as $restock)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $restock->po_number }}</div>
                                    <div class="text-xs text-gray-500">
                                        by {{ $restock->manager->name ?? 'Manager' }}
                                    </div>
                                </td>
                                
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $restock->order_date->format('d M Y') }}
                                </td>
                                
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $restock->expected_delivery_date->format('d M Y') }}
                                    @if($restock->isOverdue())
                                        <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Overdue</span>
                                    @endif
                                </td>
                                
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <div class="font-medium">{{ $restock->items->count() }} items</div>
                                    <div class="text-gray-500">{{ $restock->items->sum('quantity') }} units</div>
                                </td>
                                
                                <td class="px-4 py-3 text-sm text-gray-700 font-medium">
                                    Rp {{ number_format($restock->total_amount, 0, ',', '.') }}
                                </td>
                                
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if($restock->status === 'Pending') bg-yellow-100 text-yellow-800
                                        @elseif($restock->status === 'Confirmed') bg-blue-100 text-blue-800
                                        @elseif($restock->status === 'In Transit') bg-purple-100 text-purple-800
                                        @elseif($restock->status === 'Received') bg-green-100 text-green-800
                                        @elseif($restock->status === 'Cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $restock->status }}
                                    </span>
                                </td>
                                
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('restocks.show', $restock) }}"
                                           class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 text-sm">
                                            View
                                        </a>
                                        
                                        @can('confirm', $restock)
                                            <form action="{{ route('restocks.confirm', $restock) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Confirm this order?')"
                                                        class="px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 text-sm">
                                                    Confirm
                                                </button>
                                            </form>
                                        @endcan
                                        
                                        @can('ship', $restock)
                                            <form action="{{ route('restocks.ship', $restock) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Mark as shipped?')"
                                                        class="px-3 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 text-sm">
                                                    Ship
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            @if($restocks->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    {{ $restocks->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500">No restock orders found.</p>
                <p class="text-sm text-gray-400 mt-1">
                    @if(request()->filled('search') || request()->filled('status'))
                        Try changing your filters
                    @else
                        No orders have been assigned to you yet.
                    @endif
                </p>
            </div>
        @endif
    </div>

</div>
@endsection