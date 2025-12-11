@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="px-6 py-6">
    
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Transaction Details</h1>
        <div class="flex gap-3">
            @can('viewAny', App\Models\Transaction::class)
                {{-- Admin/Manager bisa lihat semua --}}
                <a href="{{ route('transactions.index') }}"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm">
                    ← Back to All Transactions
                </a>
            @else
                {{-- Staff hanya bisa lihat history sendiri --}}
                <a href="{{ route('transactions.history') }}"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm">
                    ← Back to My Transactions
                </a>
            @if($transaction->status === 'pending' && auth()->id() === $transaction->created_by)
                <a href="{{ route('transactions.edit', $transaction) }}"
                   class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm">
                    Edit
                </a>
            @endif
            @endcan
        </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- MAIN CARD --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        {{-- LEFT COLUMN: Transaction Info --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                
                {{-- Transaction Header --}}
                <div class="flex justify-between items-start mb-6 pb-6 border-b border-gray-200">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl font-bold text-gray-900 font-mono">
                                {{ $transaction->transaction_number }}
                            </span>
                            <div class="flex gap-2">
                                {{-- Type Badge --}}
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $transaction->type === 'incoming' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ strtoupper($transaction->type) }}
                                </span>
                                
                                {{-- Status Badge --}}
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif(in_array($transaction->status, ['approved', 'verified'])) bg-green-100 text-green-800
                                    @elseif(in_array($transaction->status, ['completed', 'shipped'])) bg-indigo-100 text-indigo-800
                                    @elseif($transaction->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">
                            Created: {{ $transaction->created_at->format('d M Y, H:i') }}
                            @if($transaction->approved_at)
                                • Approved: {{ $transaction->approved_at->format('d M Y, H:i') }}
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Transaction Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Basic Information --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Transaction Date</label>
                                <p class="mt-1 text-gray-900">{{ $transaction->date->format('d F Y') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Created By</label>
                                <p class="mt-1 text-gray-900">{{ $transaction->creator->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $transaction->creator->role ?? '' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Approved By</label>
                                <p class="mt-1 text-gray-900">{{ $transaction->approver->name ?? 'Pending' }}</p>
                                @if($transaction->approved_at)
                                    <p class="text-sm text-gray-500">{{ $transaction->approved_at->format('d M Y, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Party Information --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ $transaction->type === 'incoming' ? 'Supplier Information' : 'Customer Information' }}
                        </h3>
                        <div class="space-y-3">
                            @if($transaction->type === 'incoming')
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Supplier</label>
                                    @if($transaction->supplier)
                                        <p class="mt-1 text-gray-900">{{ $transaction->supplier->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->supplier->email ?? '' }}</p>
                                    @else
                                        <p class="mt-1 text-gray-500 italic">No supplier assigned</p>
                                    @endif
                                </div>
                            @else
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Customer Name</label>
                                    <p class="mt-1 text-gray-900">{{ $transaction->customer_name ?? 'Walk-in Customer' }}</p>
                                </div>
                            @endif
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Notes</label>
                                @if($transaction->notes)
                                    <p class="mt-1 text-gray-900 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        {{ $transaction->notes }}
                                    </p>
                                @else
                                    <p class="mt-1 text-gray-500 italic">No notes provided</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Actions & Status --}}
        <div>
            <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                
                {{-- Status Timeline --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Status Timeline</span>
                    </div>
                    
                    <div class="space-y-4">
                        {{-- Created --}}
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Created</p>
                                <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        
                        {{-- Pending/Approved --}}
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full 
                                    {{ $transaction->status === 'pending' ? 'bg-yellow-100' : 'bg-green-100' }} 
                                    flex items-center justify-center">
                                    <svg class="w-4 h-4 {{ $transaction->status === 'pending' ? 'text-yellow-600' : 'text-green-600' }}" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        @if($transaction->status === 'pending')
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        @else
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        @endif
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $transaction->status === 'pending' ? 'Pending Approval' : ucfirst($transaction->status) }}
                                </p>
                                @if($transaction->approved_at)
                                    <p class="text-sm text-gray-500">{{ $transaction->approved_at->format('d M Y, H:i') }}</p>
                                    <p class="text-sm text-gray-500">By: {{ $transaction->approver->name ?? 'Manager' }}</p>
                                @else
                                    <p class="text-sm text-gray-500">Waiting for manager approval</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                @if(auth()->user()->role === 'manager' && $transaction->status === 'pending')
                    <div class="space-y-3">
                        <form action="{{ route('transactions.approve', $transaction) }}" method="POST" class="w-full">
                            @csrf
                            @method('POST')
                            <button type="submit" 
                                    onclick="return confirm('Approve this transaction?')"
                                    class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Approve Transaction
                            </button>
                        </form>
                        
                        <form action="{{ route('transactions.reject', $transaction) }}" method="POST" class="w-full">
                            @csrf
                            @method('POST')
                            <button type="submit" 
                                    onclick="return confirm('Reject this transaction?')"
                                    class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Reject Transaction
                            </button>
                        </form>
                    </div>
                @endif
                
                @if($transaction->status === 'pending' && auth()->id() === $transaction->created_by)
                    <div class="mt-4">
                        <a href="{{ route('transactions.edit', $transaction) }}"
                           class="block w-full px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium text-center flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                            Edit Transaction
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ITEMS TABLE --}}
    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Transaction Items</h3>
                    <p class="text-sm text-gray-500 mt-1">Total {{ $transaction->items->count() }} item(s)</p>
                </div>
                @if($transaction->items->count() > 0)
                    <div class="text-sm font-medium text-gray-700">
                        Total Quantity: <span class="text-indigo-600">{{ $transaction->items->sum('quantity') }}</span>
                    </div>
                @endif
            </div>
        </div>
        
        @if($transaction->items->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transaction->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($item->product->image_url)
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $item->product->image_url) }}" alt="{{ $item->product->name }}">
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->product->category->name ?? 'No Category' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                            {{ $item->product->sku }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="px-3 py-1 inline-flex text-sm font-medium rounded-full 
                                    {{ $transaction->type === 'incoming' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $item->quantity }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500">{{ $item->product->unit }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($item->price_at_transaction, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            Rp {{ number_format($item->quantity * $item->price_at_transaction, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">{{ $item->product->current_stock ?? 0 }}</span>
                                <span class="ml-2 text-xs text-gray-500">{{ $item->product->unit }}</span>
                                
                                {{-- Stock Indicator --}}
                                @if($item->product->min_stock && $item->product->current_stock <= $item->product->min_stock)
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">Low Stock</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-8 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-500">No items found in this transaction.</p>
        </div>
        @endif
    </div>

</div>

{{-- JavaScript untuk konfirmasi --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmation for approve/reject
    const approveForm = document.querySelector('form[action*="approve"]');
    const rejectForm = document.querySelector('form[action*="reject"]');
    
    if (approveForm) {
        approveForm.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to approve this transaction?')) {
                e.preventDefault();
            }
        });
    }
    
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to reject this transaction?')) {
                e.preventDefault();
            }
        });
    }
    
    // Log untuk debugging
    console.log('Transaction Details Loaded:', {
        id: {{ $transaction->id }},
        type: '{{ $transaction->type }}',
        status: '{{ $transaction->status }}',
        items_count: {{ $transaction->items->count() }}
    });
});
</script>
@endsection