@extends('layouts.app')

@section('title', 'Restock Order Detail')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        
        {{-- HEADER --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Restock Order {{ $restock->po_number }}</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Created {{ $restock->created_at->format('d M Y H:i') }} 
                        by {{ $restock->manager->name ?? 'System' }}
                    </p>
                </div>

                <div class="flex items-center space-x-3">
                    {{-- EDIT BUTTON --}}
                    @can('update', $restock)
                        @if($restock->status === 'Pending')
                            <a href="{{ route('restocks.edit', $restock) }}"
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">
                                Edit Order
                            </a>
                        @endif
                    @endcan
                    
                    {{-- BACK BUTTON --}}
                    <a href="{{ route('restocks.index') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 shadow-sm">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            {{-- STATUS & TIMELINE --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    @php
                        $statusColors = [
                            'Pending' => 'bg-yellow-100 text-yellow-800 border border-yellow-300',
                            'Confirmed' => 'bg-blue-100 text-blue-800 border border-blue-300',
                            'In Transit' => 'bg-purple-100 text-purple-800 border border-purple-300',
                            'Received' => 'bg-green-100 text-green-800 border border-green-300',
                            'Cancelled' => 'bg-red-100 text-red-800 border border-red-300',
                            'Completed' => 'bg-indigo-100 text-indigo-800 border border-indigo-300',
                        ];
                        
                        $statusLabels = [
                            'Pending' => 'Order Created',
                            'Confirmed' => 'Supplier Confirmed',
                            'In Transit' => 'In Transit',
                            'Received' => 'Received',
                            'Cancelled' => 'Cancelled',
                            'Completed' => 'Completed',
                        ];
                        
                        $statusSteps = ['Pending', 'Confirmed', 'In Transit', 'Received', 'Completed'];
                        $currentIndex = array_search($restock->status, $statusSteps);
                        $currentIndex = $currentIndex !== false ? $currentIndex : 0;
                    @endphp
                    
                    <div>
                        <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $statusColors[$restock->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$restock->status] ?? $restock->status }}
                        </span>
                    </div>
                    
                    <div class="text-sm text-gray-500">
                        @if($restock->status === 'Cancelled' && $restock->cancelled_at)
                            Cancelled on {{ $restock->cancelled_at->format('d M Y') }}
                        @elseif($restock->status === 'Received' && $restock->received_at)
                            Received on {{ $restock->received_at->format('d M Y') }}
                        @endif
                    </div>
                </div>

                {{-- TIMELINE --}}
                <div class="relative">
                    <div class="flex justify-between mb-2">
                        @foreach($statusSteps as $index => $status)
                            @php
                                $stepDate = match($status) {
                                    'Pending' => $restock->created_at,
                                    'Confirmed' => $restock->confirmed_at,
                                    'In Transit' => $restock->in_transit_at,
                                    'Received' => $restock->received_at,
                                    'Completed' => $restock->completed_at,
                                    default => null,
                                };
                            @endphp
                            
                            <div class="text-center w-1/{{ count($statusSteps) }}">
                                <div class="relative">
                                    <div class="w-8 h-8 mx-auto rounded-full border-2 flex items-center justify-center 
                                        {{ $currentIndex >= $index 
                                            ? 'bg-indigo-600 border-indigo-600 text-white' 
                                            : 'bg-white border-gray-300 text-gray-400' }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="mt-2 text-xs font-medium {{ $currentIndex >= $index ? 'text-indigo-600' : 'text-gray-500' }}">
                                        {{ $statusLabels[$status] ?? $status }}
                                    </div>
                                    @if($stepDate)
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $stepDate->format('d M') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Timeline lines --}}
                    @for($i = 1; $i < count($statusSteps); $i++)
                        <div class="absolute top-4 left-{{ $i * 25 }}% right-0 h-0.5 bg-gray-200 -z-10" 
                             style="left: {{ ($i * (100 / count($statusSteps))) - (100 / (count($statusSteps) * 2)) }}%; 
                                    width: {{ 100 / count($statusSteps) }}%"></div>
                    @endfor
                </div>
            </div>

            {{-- ORDER INFORMATION CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- SUPPLIER CARD --}}
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Supplier Information
                    </h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Supplier Name</dt>
                            <dd class="text-sm text-gray-900">{{ $restock->supplier->name ?? 'N/A' }}</dd>
                        </div>
                        @if($restock->supplier?->company_name)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Company</dt>
                                <dd class="text-sm text-gray-900">{{ $restock->supplier->company_name }}</dd>
                            </div>
                        @endif
                        @if($restock->supplier?->email)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900">{{ $restock->supplier->email }}</dd>
                            </div>
                        @endif
                        @if($restock->supplier?->phone)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="text-sm text-gray-900">{{ $restock->supplier->phone }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- ORDER DETAILS CARD --}}
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Order Details
                    </h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">PO Number</dt>
                            <dd class="text-sm text-gray-900 font-mono">{{ $restock->po_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                            <dd class="text-sm text-gray-900">{{ $restock->order_date->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Expected Delivery</dt>
                            <dd class="text-sm text-gray-900">{{ $restock->expected_delivery_date->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created By</dt>
                            <dd class="text-sm text-gray-900">{{ $restock->manager->name ?? 'System' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- NOTES --}}
            @if($restock->notes)
                <div class="mb-8">
                    <h3 class="font-semibold text-gray-800 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        Notes
                    </h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $restock->notes }}</p>
                    </div>
                </div>
            @endif

            {{-- ITEMS TABLE --}}
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Order Items ({{ $restock->items->count() }} products)
                    </h3>
                    <div class="text-sm text-gray-600">
                        Total: <span class="font-semibold">{{ $restock->items->sum('quantity') }}</span> units
                        • Amount: <span class="font-semibold">Rp {{ number_format($restock->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Product</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Current Stock</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Unit Price</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($restock->items as $item)
                                @php
                                    $product = $item->product;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name ?? 'Product Deleted' }}</div>
                                        @if($product)
                                            <div class="text-xs text-gray-500">SKU: {{ $product->sku }}</div>
                                            <div class="text-xs text-gray-500">Category: {{ $product->category->name ?? 'N/A' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-700">
                                        {{ $product->current_stock ?? 0 }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-700">
                                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Total:</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                        {{ $restock->items->sum('quantity') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900">
                                    Rp {{ number_format($restock->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="border-t pt-6">
                <div class="flex justify-between items-center">
                    {{-- STATUS INDICATORS --}}
                    <div class="text-sm text-gray-600">
                        @if($restock->status === 'Pending')
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editable
                            </span>
                        @endif
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex space-x-3">
                        {{-- CONFIRM BUTTON (Supplier) --}}
                        @if($restock->status === 'Pending' && auth()->id() == $restock->supplier_id)
                            <form action="{{ route('restocks.confirm', $restock) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium"
                                        onclick="return confirm('Konfirmasi order ini?')">
                                    ✅ Confirm Order
                                </button>
                            </form>
                        @endif

                        {{-- SHIP BUTTON (Supplier) --}}
                        @can('ship', $restock)
                            @if($restock->status === 'Confirmed')
                                <form action="{{ route('restocks.deliver', $restock) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow-sm flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Mark as Shipped
                                    </button>
                                </form>
                            @endif
                        @endcan

                        {{-- RECEIVE BUTTON --}}
                    @if(auth()->user()->isManager())
                        @if($restock->status === 'In Transit')
                            <form action="{{ route('restocks.receive', $restock) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-sm flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Receive Order
                                </button>
                            </form>
                        @endif
                    @endif
                        {{-- CANCEL BUTTON --}}
                        @can('cancel', $restock)
                            @if(in_array($restock->status, ['Pending', 'Confirmed']))
                                <button type="button" 
                                        onclick="showCancelModal('{{ $restock->id }}', '{{ $restock->po_number }}')"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-sm flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancel Order
                                </button>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CANCEL MODAL --}}
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Cancel Order</h3>
            <button type="button" onclick="hideCancelModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <p class="text-sm text-gray-600 mb-4">
            Are you sure you want to cancel order <span id="cancel-po-number" class="font-semibold"></span>?
        </p>
        
        <form id="cancelForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 text-gray-700">
                    Reason for cancellation
                </label>
                <textarea name="cancellation_reason" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                          placeholder="Please provide a reason for cancellation..."
                          maxlength="500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideCancelModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Confirm Cancel
                </button>
            </div>
        </form>
    </div>
</div>

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
@endsection