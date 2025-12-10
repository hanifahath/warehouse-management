@extends('layouts.app')

@section('title', 'Create Outgoing Transaction')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-900">Create Outgoing Transaction</h1>
    <a href="{{ route('transactions.index') }}" 
       class="text-indigo-600 hover:underline text-sm">
        ← Back to Transactions
    </a>
</div>

@if ($errors->any())
    <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Ada kesalahan:</strong>
        <ul class="list-disc list-inside mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('transactions.store.outgoing') }}" method="POST">
    @csrf
    <input type="hidden" name="type" value="Outgoing">

    {{-- Transaction Info --}}
    <div class="bg-white p-5 rounded-xl shadow border mb-5">
        <label class="block text-gray-700 font-medium">Transaction Date</label>
        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
            class="w-full border border-gray-300 rounded-lg p-2 mt-1 mb-4" required>

        <label class="block text-gray-700 font-medium">Customer Name</label>
        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
            class="w-full border border-gray-300 rounded-lg p-2 mt-1 mb-4"
            placeholder="Enter customer name" required>

        <label class="block text-gray-700 font-medium">Notes (optional)</label>
        <textarea name="notes" rows="3"
            class="w-full border border-gray-300 rounded-lg p-2 mt-1">{{ old('notes') }}</textarea>
    </div>

    {{-- ITEMS --}}
    <div class="bg-white p-5 rounded-xl shadow border border-gray-200">
        <h2 class="text-gray-900 font-semibold mb-4">Add Items</h2>

        <div id="itemsContainer" class="space-y-4">
            {{-- First item row --}}
            <div class="grid grid-cols-4 gap-4 border p-4 rounded-lg bg-gray-50 item-row">
                <div class="col-span-2">
                    <label class="text-gray-700 font-medium">Product</label>
                    <select name="items[0][product_id]" 
                        class="productSelect w-full border border-gray-300 rounded-lg p-2 mt-1"
                        required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                data-stock="{{ $product->current_stock }}"
                                data-price="{{ $product->selling_price }}"
                                {{ old('items.0.product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} ({{ $product->sku }}) — 
                                Stock: {{ $product->current_stock }} — 
                                Price: Rp{{ number_format($product->selling_price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-gray-700 font-medium">Quantity</label>
                    <input type="number"
                        name="items[0][quantity]"
                        value="{{ old('items.0.quantity', 1) }}"
                        min="1"
                        class="quantityInput w-full border border-gray-300 rounded-lg p-2 mt-1"
                        required>
                </div>

                {{-- Hidden price field --}}
                <input type="hidden" 
                    name="items[0][price_at_transaction]"
                    value="{{ old('items.0.price_at_transaction', 0) }}"
                    class="priceInput">

                <div class="flex items-end">
                    <button type="button" 
                        class="removeItem px-3 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm">
                        Remove
                    </button>
                </div>
            </div>
        </div>

        <button type="button" id="addItem"
            class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">
            + Add Another Item
        </button>
    </div>

    <button type="submit"
        class="mt-6 px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
        Submit Outgoing Transaction
    </button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize old items if validation failed
    @if(old('items'))
        let oldItems = @json(old('items'));
        if (oldItems.length > 1) {
            for (let i = 1; i < oldItems.length; i++) {
                addNewItemRow();
                
                // Fill with old values
                let row = document.querySelectorAll('.item-row')[i];
                let item = oldItems[i];
                
                if (item.product_id) {
                    row.querySelector('.productSelect').value = item.product_id;
                    triggerPriceUpdate(row.querySelector('.productSelect'));
                }
                
                if (item.quantity) {
                    row.querySelector('.quantityInput').value = item.quantity;
                }
                
                if (item.price_at_transaction) {
                    row.querySelector('.priceInput').value = item.price_at_transaction;
                }
            }
        }
    @endif
    
    // Add new item row
    document.getElementById('addItem').addEventListener('click', function() {
        addNewItemRow();
    });
    
    function addNewItemRow() {
        let container = document.getElementById('itemsContainer');
        let index = container.children.length;
        let template = document.querySelector('.item-row').cloneNode(true);
        
        // Update names
        template.querySelector('.productSelect').name = `items[${index}][product_id]`;
        template.querySelector('.quantityInput').name = `items[${index}][quantity]`;
        template.querySelector('.priceInput').name = `items[${index}][price_at_transaction]`;
        
        // Clear values
        template.querySelector('.productSelect').value = '';
        template.querySelector('.quantityInput').value = 1;
        template.querySelector('.priceInput').value = 0;
        
        // Remove any error messages
        let errorMsg = template.querySelector('.stock-error');
        if (errorMsg) errorMsg.remove();
        
        container.appendChild(template);
    }
    
    // Remove item row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeItem')) {
            let rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
                reindexRows();
            }
        }
    });
    
    // Reindex rows after removal
    function reindexRows() {
        document.querySelectorAll('.item-row').forEach((row, index) => {
            row.querySelector('.productSelect').name = `items[${index}][product_id]`;
            row.querySelector('.quantityInput').name = `items[${index}][quantity]`;
            row.querySelector('.priceInput').name = `items[${index}][price_at_transaction]`;
        });
    }
    
    // Product selection handler
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('productSelect')) {
            triggerPriceUpdate(e.target);
            validateStock(e.target);
        }
    });
    
    // Quantity input handler
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantityInput')) {
            let productSelect = e.target.closest('.item-row').querySelector('.productSelect');
            if (productSelect.value) {
                validateStock(productSelect);
            }
        }
    });
    
    function triggerPriceUpdate(selectElement) {
        let selectedOption = selectElement.options[selectElement.selectedIndex];
        let row = selectElement.closest('.item-row');
        let priceInput = row.querySelector('.priceInput');
        
        if (selectedOption.dataset.price) {
            priceInput.value = selectedOption.dataset.price;
        }
    }
    
    function validateStock(selectElement) {
        let row = selectElement.closest('.item-row');
        let quantityInput = row.querySelector('.quantityInput');
        let selectedOption = selectElement.options[selectElement.selectedIndex];
        
        if (!selectedOption.value || !quantityInput.value) return;
        
        let stock = parseInt(selectedOption.dataset.stock) || 0;
        let quantity = parseInt(quantityInput.value) || 0;
        
        // Remove existing error
        let existingError = row.querySelector('.stock-error');
        if (existingError) existingError.remove();
        
        if (quantity > stock) {
            quantityInput.classList.add('border-red-500', 'bg-red-50');
            
            let errorDiv = document.createElement('div');
            errorDiv.className = 'stock-error text-red-500 text-sm mt-1';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i> 
                                 Stock insufficient. Available: ${stock}`;
            
            quantityInput.parentNode.appendChild(errorDiv);
        } else {
            quantityInput.classList.remove('border-red-500', 'bg-red-50');
        }
    }
});
</script>
@endsection