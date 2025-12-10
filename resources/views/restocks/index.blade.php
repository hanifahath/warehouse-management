@extends('layouts.app')

@section('title', 'Restock Orders')

@section('content')
<div class="px-6 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Restock Orders</h1>
        @can('create', App\Models\RestockOrder::class)
            <a href="{{ route('restocks.create') }}"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">
                + Create Order
            </a>
        @endcan
    </div>

    {{-- ROLE BADGE --}}
    @if(auth()->user()->isSupplier())
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
            <p class="text-sm text-blue-700">
                👋 Viewing as <strong>Supplier</strong>: {{ auth()->user()->name }}
                @if(!auth()->user()->is_approved)
                    <span class="ml-2 text-orange-600">(Pending Approval)</span>
                @endif
            </p>
        </div>
    @endif

    {{-- TAB NAVIGATION --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-6">
            <a href="{{ auth()->user()->isSupplier() ? route('restocks.index') : route('restocks.index') }}"
                class="pb-3 text-sm font-medium
                    {{ !request('status') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                All ({{ $totalOrders ?? 0 }})
            </a>
            <a href="{{ auth()->user()->isSupplier() ? route('restocks.index', ['status' => 'Pending']) : route('restocks.index', ['status' => 'Pending']) }}"
                class="pb-3 text-sm font-medium
                    {{ request('status') === 'Pending' ? 'text-yellow-600 border-b-2 border-yellow-600' : 'text-gray-500 hover:text-gray-700' }}">
                Pending ({{ $pendingCount ?? 0 }})
            </a>
            <a href="{{ auth()->user()->isSupplier() ? route('restocks.index', ['status' => 'Confirmed']) : route('restocks.index', ['status' => 'Confirmed']) }}"
                class="pb-3 text-sm font-medium
                    {{ request('status') === 'Confirmed' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                Confirmed ({{ $confirmedCount ?? 0 }})
            </a>
            <a href="{{ auth()->user()->isSupplier() ? route('restocks.index', ['status' => 'In Transit']) : route('restocks.index', ['status' => 'In Transit']) }}"
                class="pb-3 text-sm font-medium
                    {{ request('status') === 'In Transit' ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500 hover:text-gray-700' }}">
                In Transit ({{ $inTransitCount ?? 0 }})
            </a>
            <a href="{{ auth()->user()->isSupplier() ? route('restocks.index', ['status' => 'Received']) : route('restocks.index', ['status' => 'Received']) }}"
                class="pb-3 text-sm font-medium
                    {{ request('status') === 'Received' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-gray-700' }}">
                Received ({{ $receivedCount ?? 0 }})
            </a>
            <a href="{{ auth()->user()->isSupplier() ? route('restocks.index', ['status' => 'Cancelled']) : route('restocks.index', ['status' => 'Cancelled']) }}"
                class="pb-3 text-sm font-medium
                    {{ request('status') === 'Cancelled' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">
                Cancelled ({{ $cancelledCount ?? 0 }})
            </a>
        </nav>
    </div>

    {{-- FILTERS --}}
    <form method="GET" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            {{-- SEARCH --}}
            <div>
                <input type="text" name="search" placeholder="Search PO number..."
                       value="{{ request('search') }}"
                       class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- SUPPLIER --}}
            <div>
                <select name="supplier_id" 
                        class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- DATE FROM --}}
            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="From date">
            </div>

            {{-- DATE TO --}}
            <div>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="To date">
            </div>

            {{-- ACTIONS --}}
            <div class="flex gap-2">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                    Apply Filter
                </button>
                <a href="{{ auth()->user()->isSupplier() ? route('supplier.restocks.index') : route('restocks.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- ACTIVE FILTERS --}}
    @if(request()->anyFilled(['search', 'date_from', 'date_to', 'supplier_id']))
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm">
        <strong>Active Filters:</strong>
        @if(request('search')) 
            <span class="ml-2 px-2 py-1 bg-blue-100 rounded">Search: "{{ request('search') }}"</span> 
        @endif
        @if(request('supplier_id') && isset($suppliers))
            @php $selectedSupplier = $suppliers->where('id', request('supplier_id'))->first(); @endphp
            @if($selectedSupplier)
                <span class="ml-2 px-2 py-1 bg-blue-100 rounded">Supplier: {{ $selectedSupplier->name }}</span>
            @endif
        @endif
        @if(request('date_from')) 
            <span class="ml-2 px-2 py-1 bg-blue-100 rounded">From: {{ request('date_from') }}</span> 
        @endif
        @if(request('date_to')) 
            <span class="ml-2 px-2 py-1 bg-blue-100 rounded">To: {{ request('date_to') }}</span> 
        @endif
    </div>
    @endif

    {{-- SUCCESS/ERROR MESSAGES --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="bg-white border border-gray-200 shadow rounded-lg overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-4 font-medium text-gray-600">PO Number</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Supplier</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Order Date</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Expected Delivery</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Items</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Total Amount</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Status</th>
                    <th class="py-3 px-4 font-medium text-gray-600 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($restocks as $restock)
                @php
                    $statusColors = [
                        'Pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                        'Confirmed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                        'In Transit' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                        'Received' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                        'Cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                    ];
                    $statusColor = $statusColors[$restock->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
                @endphp
                
                <tr class="hover:bg-gray-50">
                    {{-- PO Number --}}
                    <td class="py-3 px-4 font-mono text-gray-800 text-xs">
                        {{ $restock->po_number }}
                        <div class="text-gray-500 text-xs mt-1">
                            by {{ $restock->manager->name ?? 'N/A' }}
                        </div>
                    </td>

                    {{-- Supplier --}}
                    <td class="py-3 px-4 text-gray-800">
                        <div class="font-medium">{{ $restock->supplier->name ?? 'N/A' }}</div>
                        @if($restock->supplier && $restock->supplier->company_name)
                            <div class="text-gray-500 text-xs">{{ $restock->supplier->company_name }}</div>
                        @endif
                    </td>

                    {{-- Order Date --}}
                    <td class="py-3 px-4 text-gray-800">
                        <div>{{ $restock->order_date->format('d M Y') }}</div>
                        <div class="text-gray-500 text-xs">{{ $restock->order_date->format('H:i') }}</div>
                    </td>

                    {{-- Expected Delivery --}}
                    <td class="py-3 px-4 text-gray-800">
                        <div>{{ $restock->expected_delivery_date->format('d M Y') }}</div>
                    </td>

                    {{-- Items --}}
                    <td class="py-3 px-4 text-gray-800">
                        <div class="font-medium">{{ $restock->items->count() }} items</div>
                        <div class="text-gray-500 text-xs">{{ $restock->items->sum('quantity') }} units</div>
                    </td>

                    {{-- Total Amount --}}
                    <td class="py-3 px-4 text-gray-800 font-medium">
                        Rp {{ number_format($restock->total_amount, 0, ',', '.') }}
                    </td>

                    {{-- Status --}}
                    <td class="py-3 px-4">
                        <span class="{{ $statusColor['bg'] }} {{ $statusColor['text'] }} px-2 py-1 rounded text-xs">
                            {{ $restock->status }}
                        </span>
                        @if($restock->is_editable)
                            <div class="text-gray-500 text-xs mt-1">Editable</div>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="py-3 px-4 text-right">
                        <div class="flex justify-end gap-2">
                            {{-- View --}}
                            <a href="{{ route('restocks.show', $restock) }}"
                               class="px-3 py-1 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded text-sm">
                                View
                            </a>
                            
                            {{-- Edit --}}
                            @can('update', $restock)
                                @if($restock->is_editable)
                                    <a href="{{ route('restocks.edit', $restock) }}"
                                       class="px-3 py-1 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded text-sm">
                                        Edit
                                    </a>
                                @endif
                            @endcan
                            
                            {{-- Quick Actions --}}
                            @if($restock->is_confirmable && auth()->user()->isSupplier())
                                <form action="{{ route('restocks.confirm', $restock) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Confirm this order?')"
                                            class="px-3 py-1 bg-green-50 text-green-600 hover:bg-green-100 rounded text-sm">
                                        Confirm
                                    </button>
                                </form>
                            @endif
                            
                            @if($restock->is_shippable && auth()->user()->isSupplier())
                                <form action="{{ route('restocks.deliver', $restock) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Mark as shipped?')"
                                            class="px-3 py-1 bg-purple-50 text-purple-600 hover:bg-purple-100 rounded text-sm">
                                        Ship
                                    </button>
                                </form>
                            @endif
                            
                            {{-- Cancel --}}
                            @can('cancel', $restock)
                                @if($restock->is_cancellable)
                                    <button type="button" 
                                            onclick="showCancelModal('{{ $restock->id }}', '{{ $restock->po_number }}')"
                                            class="px-3 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded text-sm">
                                        Cancel
                                    </button>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8" class="py-8 text-center text-gray-500">
                        <div class="mb-2">
                            <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        No restock orders found.
                        @if(request()->anyFilled(['search', 'date_from', 'date_to', 'supplier_id']))
                            <div class="mt-2">
                                <a href="{{ auth()->user()->isSupplier() ? route('supplier.restocks.index') : route('restocks.index') }}" 
                                   class="text-indigo-600 hover:underline text-sm">
                                    Clear filters
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($restocks->hasPages())
    <div class="mt-6">
        {{ $restocks->withQueryString()->links() }}
    </div>
    @endif

</div>

{{-- CANCEL MODAL --}}
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Cancel Order</h3>
            <button type="button" onclick="hideCancelModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <p class="text-sm text-gray-600 mb-4">
            Cancelling order: <span id="cancel-po-number" class="font-semibold"></span>
        </p>
        
        <form id="cancelForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 text-gray-700">Reason for cancellation <span class="text-red-500">*</span></label>
                <textarea name="cancellation_reason" required rows="3"
                          class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500" 
                          placeholder="Enter reason for cancellation..."
                          maxlength="500"></textarea>
                <p class="text-xs text-gray-500 mt-1">Maximum 500 characters</p>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="hideCancelModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                    Confirm Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showCancelModal(orderId, poNumber) {
    document.getElementById('cancel-po-number').textContent = poNumber;
    document.getElementById('cancelForm').action = `/restocks/${orderId}/cancel`;
    document.getElementById('cancelModal').classList.remove('hidden');
}

function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelForm').reset();
}

// Close modal when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideCancelModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('cancelModal').classList.contains('hidden')) {
        hideCancelModal();
    }
});
</script>
@endpush