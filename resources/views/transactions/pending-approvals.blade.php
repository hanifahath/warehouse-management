{{-- resources/views/transactions/pending-approvals.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- PAGE TITLE --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pending Approvals</h1>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <div class="text-3xl font-bold text-yellow-600">{{ $transactions->count() }}</div>
                <div class="text-sm text-gray-600">Pending Review</div>
            </div>
            @can('viewAny', App\Models\Transaction::class)
            <a href="{{ route('transactions.index') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm font-medium">
                View All Transactions
            </a>
            @endcan
        </div>
    </div>

    {{-- TRANSACTIONS LIST --}}
    @if($transactions->count() > 0)
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden mb-6">
        
        {{-- TABLE HEADER --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Pending Transactions</h2>
            <span class="text-sm text-gray-600">
                Showing {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }}
            </span>
        </div>

        {{-- TRANSACTIONS TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3">Transaction</th>
                        <th class="px-6 py-3">Created By</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Items</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-gray-200">
                    @foreach ($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition-colors">
                        
                        {{-- TRANSACTION NUMBER --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="font-medium text-gray-900">#{{ $transaction->transaction_number }}</div>
                                @if($transaction->is_urgent)
                                <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    URGENT
                                </span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                @if($transaction->type === 'incoming')
                                    From: {{ $transaction->supplier->name ?? 'N/A' }}
                                @else
                                    To: {{ $transaction->customer_name ?? 'Walk-in Customer' }}
                                @endif
                            </div>
                        </td>

                        {{-- CREATED BY --}}
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $transaction->creator->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $transaction->creator->role->name ?? 'Staff' }}</div>
                        </td>

                        {{-- TYPE --}}
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ 
                                $transaction->type === 'incoming' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' 
                            }}">
                                {{ strtoupper($transaction->type) }}
                            </span>
                        </td>

                        {{-- ITEMS --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($transaction->items->take(2) as $item)
                                <span class="px-2 py-1 bg-gray-100 rounded text-xs">
                                    {{ $item->product->name }} ({{ $item->quantity }})
                                </span>
                                @endforeach
                                @if($transaction->items->count() > 2)
                                <span class="px-2 py-1 bg-gray-100 rounded text-xs">
                                    +{{ $transaction->items->count() - 2 }} more
                                </span>
                                @endif
                            </div>
                        </td>

                        {{-- AMOUNT --}}
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </div>
                        </td>

                        {{-- DATE --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $transaction->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i') }}</div>
                        </td>

                        {{-- ACTIONS --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-3">
                                
                                {{-- VIEW --}}
                                <a href="{{ route('transactions.show', $transaction) }}"
                                   class="text-indigo-600 hover:text-indigo-800"
                                   title="View details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                {{-- APPROVE --}}
                                @can('approve', $transaction)
                                <form method="POST" action="{{ route('transactions.approve', $transaction) }}"
                                      class="inline"
                                      onsubmit="return confirm('Approve transaction #{{ $transaction->transaction_number }}? This will update inventory stock.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="text-green-600 hover:text-green-800"
                                            title="Approve">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </form>
                                @endcan

                                {{-- REJECT --}}
                                @can('reject', $transaction)
                                <button type="button"
                                        onclick="showRejectModal({{ $transaction->id }})"
                                        class="text-red-600 hover:text-red-800"
                                        title="Reject">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                @endcan
                            </div>
                        </td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    {{-- EMPTY STATE --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-xl font-medium text-gray-900 mb-2">All Caught Up!</h3>
        <p class="text-gray-600 mb-6">No pending transactions requiring your approval.</p>
        @can('viewAny', App\Models\Transaction::class)
        <a href="{{ route('transactions.index') }}"
           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
            View Transaction History
        </a>
        @endcan
    </div>
    @endif

    {{-- PAGINATION --}}
    @if($transactions->hasPages())
    <div class="mt-6">
        {{ $transactions->withQueryString()->links() }}
    </div>
    @endif

</div>

{{-- REJECT MODAL --}}
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Reject Transaction</h3>
                <button type="button" onclick="hideRejectModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">
                        Please provide a reason for rejecting this transaction. The staff member will be notified.
                    </p>
                    
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Rejection <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" 
                              rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                              placeholder="Example: Insufficient stock, incorrect pricing, customer cancellation..."
                              required></textarea>
                    <p class="mt-1 text-xs text-gray-500">This will be visible to the staff who created the transaction.</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="hideRejectModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium text-sm">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Reject Modal Functions
let currentTransactionId = null;

function showRejectModal(transactionId) {
    currentTransactionId = transactionId;
    const form = document.getElementById('rejectForm');
    form.action = `/transactions/${transactionId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
    
    // Focus on textarea
    setTimeout(() => {
        form.querySelector('textarea').focus();
    }, 100);
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    
    // Reset form
    const form = document.getElementById('rejectForm');
    form.reset();
    currentTransactionId = null;
}

// Close modal on outside click
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target.id === 'rejectModal') {
        hideRejectModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('rejectModal').classList.contains('hidden')) {
        hideRejectModal();
    }
});
</script>
@endpush

@endsection