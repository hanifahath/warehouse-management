{{-- resources/views/transactions/history.blade.php --}}
@extends('layouts.app')

@section('title', 'Transaction History')

@section('content')
<div class="px-6 py-6">
    
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Transaction History</h1>
        
        <div class="flex gap-3">
            @can('create', App\Models\Transaction::class)
                <a href="{{ route('transactions.create.incoming') }}" 
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                    + Incoming
                </a>
                <a href="{{ route('transactions.create.outgoing') }}" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm">
                    + Outgoing
                </a>
            @endcan
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="bg-white p-5 rounded-xl shadow border mb-6">
        <form method="GET" action="{{ route('transactions.history') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- TYPE FILTER --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">All Types</option>
                        <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                        <option value="outgoing" {{ request('outgoing') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                    </select>
                </div>

                {{-- STATUS FILTER --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                {{-- DATE FROM --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                {{-- DATE TO --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <div class="flex justify-between items-center pt-2">
                <div class="text-sm text-gray-600">
                    Showing {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} 
                    of {{ $transactions->total() }} records
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                        Apply Filters
                    </button>
                    <a href="{{ route('transactions.history') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 text-sm">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- TRANSACTIONS TABLE --}}
    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                {{-- TRANSACTION NUMBER --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $transaction->transaction_number }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if($transaction->type === 'outgoing' && $transaction->customer_name)
                                            {{ Str::limit($transaction->customer_name, 20) }}
                                        @elseif($transaction->type === 'incoming' && $transaction->supplier)
                                            {{ Str::limit($transaction->supplier->name, 20) }}
                                        @endif
                                    </div>
                                </td>

                                {{-- DATE --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transaction->date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i') }}</div>
                                </td>

                                {{-- TYPE --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs font-medium rounded-full 
                                        {{ $transaction->type === 'incoming' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ strtoupper($transaction->type) }}
                                    </span>
                                </td>

                                {{-- STATUS --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs font-medium rounded-full
                                        @if($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($transaction->status === 'approved') bg-green-100 text-green-800
                                        @elseif($transaction->status === 'rejected') bg-red-100 text-red-800
                                        @elseif($transaction->status === 'completed') bg-indigo-100 text-indigo-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>

                                {{-- ITEMS --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $transaction->items->count() }} items</div>
                                    <div class="text-xs text-gray-500">
                                        @foreach($transaction->items->take(2) as $item)
                                            {{ $item->product->name }} ({{ $item->quantity }})<br>
                                        @endforeach
                                        @if($transaction->items->count() > 2)
                                            ... +{{ $transaction->items->count() - 2 }} more
                                        @endif
                                    </div>
                                </td>

                                {{-- TOTAL --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                    </div>
                                </td>

                                {{-- ACTIONS --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex gap-3">
                                        <a href="{{ route('transactions.show', $transaction) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            View
                                        </a>
                                        
                                        @if($transaction->status === 'pending' && auth()->id() === $transaction->created_by)
                                            <a href="{{ route('transactions.edit', $transaction) }}" 
                                               class="text-blue-600 hover:text-blue-900 text-sm">
                                                Edit
                                            </a>
                                        @endif
                                        
                                        @if($transaction->status === 'rejected' && $transaction->rejection_reason)
                                            <button onclick="showRejectionReason('{{ addslashes($transaction->rejection_reason) }}')"
                                                    class="text-red-600 hover:text-red-900 text-sm">
                                                Reason
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $transactions->withQueryString()->links() }}
                </div>
            @endif
        @else
            {{-- EMPTY STATE --}}
            <div class="p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                <p class="text-sm text-gray-500 mb-6">
                    @if(request()->anyFilled(['type', 'status', 'date_from', 'date_to']))
                        Try changing your filters
                    @else
                        Get started by creating your first transaction
                    @endif
                </p>
                @can('create', App\Models\Transaction::class)
                    <div class="flex gap-3 justify-center">
                        <a href="{{ route('transactions.create.outgoing') }}" 
                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                            + Create Outgoing
                        </a>
                        <a href="{{ route('transactions.create.incoming') }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            + Create Incoming
                        </a>
                    </div>
                @endcan
            </div>
        @endif
    </div>

</div>

{{-- REJECTION REASON MODAL --}}
<div id="rejectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Rejection Reason</h3>
                <button type="button" onclick="hideRejectionModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="mb-4">
                <p id="rejectionReasonText" class="text-sm text-gray-700 bg-red-50 p-4 rounded-lg"></p>
            </div>
            
            <div class="flex justify-end">
                <button type="button" 
                        onclick="hideRejectionModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectionReason(reason) {
    document.getElementById('rejectionReasonText').textContent = reason;
    document.getElementById('rejectionModal').classList.remove('hidden');
}

function hideRejectionModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('rejectionModal').addEventListener('click', function(e) {
    if (e.target.id === 'rejectionModal') {
        hideRejectionModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('rejectionModal').classList.contains('hidden')) {
        hideRejectionModal();
    }
});
</script>
@endsection