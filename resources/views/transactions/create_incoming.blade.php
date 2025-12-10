@extends('layouts.app')

@section('title', 'Create Incoming Transaction')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-900">Create Incoming Transaction</h1>
    <a href="{{ route('transactions.index') }}" 
       class="text-indigo-600 hover:underline text-sm">
        ← Back to Transactions
    </a>
</div>

{{-- ERROR DISPLAY --}}
@if ($errors->any())
    <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong class="font-bold">Validation Errors:</strong>
        <ul class="list-disc list-inside mt-2 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- FORM --}}
<form action="{{ route('transactions.store.incoming') }}" 
      method="POST" 
      id="transactionForm">
    @csrf

    <input type="hidden" name="type" value="incoming">

    {{-- Transaction Info --}}
    <div class="bg-white p-5 rounded-xl shadow border mb-5">
        <label class="block text-gray-700 font-medium">Transaction Date *</label>
        <input type="date" name="date" 
            value="{{ old('date', date('Y-m-d')) }}"
            class="w-full border border-gray-300 rounded-lg p-2 mt-1 mb-4 @error('date') border-red-500 @enderror" 
            required>
        @error('date')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        <label class="block text-gray-700 font-medium">Supplier *</label>
        <select name="supplier_id"
            class="w-full border border-gray-300 rounded-lg p-2 mt-1 mb-4 @error('supplier_id') border-red-500 @enderror" 
            required>
            <option value="">Select Supplier</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" 
                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
        @error('supplier_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        <label class="block text-gray-700 font-medium">Notes (optional)</label>
        <textarea name="notes" rows="3"
            class="w-full border border-gray-300 rounded-lg p-2 mt-1">{{ old('notes') }}</textarea>
    </div>

    {{-- Items --}}
    <div class="bg-white p-5 rounded-xl shadow border border-gray-200">
        <h2 class="text-gray-900 font-semibold mb-4">Add Items *</h2>
        
        @error('items')
            <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
        @enderror

        <div id="itemsContainer" class="space-y-4">
            @php
                $oldItems = old('items', [['product_id' => '', 'quantity' => '', 'price_at_transaction' => '']]);
            @endphp
            
            @foreach($oldItems as $index => $item)
            <div class="grid grid-cols-4 gap-4 border p-4 rounded-lg bg-gray-50 item-row">
                <div>
                    <label class="text-gray-700 font-medium">Product *</label>
                    <select name="items[{{ $index }}][product_id]" 
                        class="item-product w-full border border-gray-300 rounded-lg p-2 mt-1 @error('items.' . $index . '.product_id') border-red-500 @enderror" 
                        required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                {{ old('items.' . $index . '.product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} ({{ $product->sku }})
                            </option>
                        @endforeach
                    </select>
                    @error('items.' . $index . '.product_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700 font-medium">Quantity *</label>
                    <input type="number" min="1" 
                        name="items[{{ $index }}][quantity]"
                        value="{{ old('items.' . $index . '.quantity', '') }}"
                        class="item-qty w-full border border-gray-300 rounded-lg p-2 mt-1 @error('items.' . $index . '.quantity') border-red-500 @enderror" 
                        required>
                    @error('items.' . $index . '.quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700 font-medium">Price *</label>
                    <input type="number" min="0" step="0.01"
                        name="items[{{ $index }}][price_at_transaction]"
                        value="{{ old('items.' . $index . '.price_at_transaction', '') }}"
                        class="item-price w-full border border-gray-300 rounded-lg p-2 mt-1 @error('items.' . $index . '.price_at_transaction') border-red-500 @enderror" 
                        required>
                    @error('items.' . $index . '.price_at_transaction')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-end">
                    @if($index > 0)
                    <button type="button" 
                        class="removeItem px-3 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm">
                        Remove
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" id="addItem"
            class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">
            + Add Another Item
        </button>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" id="submitBtn"
            class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
            Submit Incoming Transaction
        </button>
        
        <a href="{{ route('transactions.history') }}"
            class="px-5 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
            Cancel
        </a>
    </div>
</form>

<script>
// ==================== SIMPLE DYNAMIC FORM ====================
// Hanya untuk menambah/hapus item rows, TANPA validasi

// Add item functionality
document.getElementById('addItem').addEventListener('click', function () {
    let container = document.getElementById('itemsContainer');
    let index = container.children.length;
    
    // Clone first row as template
    let template = container.querySelector('.item-row');
    let newRow = template.cloneNode(true);
    
    // Clear values
    newRow.querySelectorAll('input').forEach(input => {
        if (input.type !== 'hidden') input.value = '';
    });
    newRow.querySelectorAll('select').forEach(select => select.value = '');
    
    // Update names with new index
    newRow.querySelector('.item-product').name = `items[${index}][product_id]`;
    newRow.querySelector('.item-qty').name = `items[${index}][quantity]`;
    newRow.querySelector('.item-price').name = `items[${index}][price_at_transaction]`;
    
    // Clear old error styling and messages
    newRow.querySelectorAll('.border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });
    newRow.querySelectorAll('.text-red-500').forEach(el => el.remove());
    
    // Ensure remove button exists
    const btnContainer = newRow.querySelector('.flex.items-end');
    btnContainer.innerHTML = '<button type="button" class="removeItem px-3 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm">Remove</button>';
    
    container.appendChild(newRow);
});

// Remove item functionality
document.addEventListener('click', function(e){
    if(e.target.classList.contains('removeItem')){
        const row = e.target.closest('.item-row');
        const allRows = document.querySelectorAll('.item-row');
        
        if(allRows.length > 1){
            row.remove();
            // Re-index remaining rows
            document.querySelectorAll('.item-row').forEach((row, index) => {
                row.querySelector('.item-product').name = `items[${index}][product_id]`;
                row.querySelector('.item-qty').name = `items[${index}][quantity]`;
                row.querySelector('.item-price').name = `items[${index}][price_at_transaction]`;
            });
        }
    }
});
</script>
@endsection