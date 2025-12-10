@extends('layouts.app')

@section('title', 'Edit Restock Order')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Edit Restock Order #{{ $restock->po_number }}</h2>
            <a href="{{ route('restocks.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200">
                ← Back to List
            </a>
        </div>

        <!-- Status Alert -->
        @if(!in_array($restock->status, ['Pending', 'Confirmed']))
        <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 rounded-lg">
            <div class="flex">
                <svg class="h-5 w-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-bold">Cannot Edit Order</p>
                    <p class="mt-1">This order is currently <span class="font-semibold">{{ $restock->status }}</span>. Only orders with status "Pending" or "Confirmed" can be edited.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Error Display -->
        @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="mt-2 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Success Message -->
        @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-bold">Success!</p>
                    <p class="mt-1">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('restocks.update', $restock) }}" method="POST" id="restockForm">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Supplier <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            {{ !in_array($restock->status, ['Pending', 'Confirmed']) ? 'disabled' : '' }}>
                        <option value="">-- Select Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $restock->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                                @if($supplier->company_name)
                                    - {{ $supplier->company_name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Order Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Order Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="order_date" required 
                           value="{{ old('order_date', $restock->order_date->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           {{ !in_array($restock->status, ['Pending', 'Confirmed']) ? 'disabled' : '' }}>
                    @error('order_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expected Delivery Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Expected Delivery Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="expected_delivery_date" required 
                           value="{{ old('expected_delivery_date', $restock->expected_delivery_date->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           {{ !in_array($restock->status, ['Pending', 'Confirmed']) ? 'disabled' : '' }}>
                    @error('expected_delivery_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <div class="px-4 py-2.5 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="font-medium {{ $restock->status == 'Pending' ? 'text-yellow-600' : ($restock->status == 'Confirmed' ? 'text-blue-600' : ($restock->status == 'In Transit' ? 'text-orange-600' : 'text-green-600')) }}">
                            {{ $restock->status }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Status cannot be changed from edit form</p>
                </div>

                <!-- PO Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        PO Number
                    </label>
                    <div class="px-4 py-2.5 bg-gray-50 rounded-lg border border-gray-200 font-mono">
                        {{ $restock->po_number }}
                    </div>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Notes (optional)
                    </label>
                    <textarea name="notes" rows="3" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                              placeholder="Additional information for this order..."
                              {{ !in_array($restock->status, ['Pending', 'Confirmed']) ? 'disabled' : '' }}>{{ old('notes', $restock->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Items Section -->
            <div class="border-t pt-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Restock Items</h3>
                    @if(in_array($restock->status, ['Pending', 'Confirmed']))
                    <button type="button" id="add-item" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        + Add Product
                    </button>
                    @endif
                </div>

                <!-- Items Container -->
                <div class="space-y-4 mb-6" id="items-container">
                    <!-- Existing items -->
                    @foreach($restock->items as $index => $item)
                    <div class="item-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Product <span class="text-red-500">*</span>
                                </label>
                                <select name="items[{{ $index }}][product_id]" required 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 product-select"
                                        {{ !in_array($restock->status, ['Pending', 'Confirmed']) ? 'disabled' : '' }}>
                                    <option value="">-- Select Product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old("items.$index.product_id", $item->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->sku }}) - Stock: {{ $product->current_stock }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="items[{{ $index }}][quantity]" required min="1" 
                                       value="{{ old("items.$index.quantity", $item->quantity) }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 quantity-input"
                                       {{ !in_array($restock->status, ['Pending', 'Confirmed']) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex items-end">
                                @if(in_array($restock->status, ['Pending', 'Confirmed']) && $loop->index > 0)
                                <button type="button" 
                                        class="remove-btn px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition duration-200 w-full">
                                    Remove
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <a href="{{ route('restocks.index') }}" 
                       class="px-5 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200">
                        Cancel
                    </a>
                    @if(in_array($restock->status, ['Pending', 'Confirmed']))
                    <button type="submit" 
                            class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                        Update Restock Order
                    </button>
                    @else
                    <a href="{{ route('restocks.show', $restock) }}" 
                       class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        View Order Details
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

@if(in_array($restock->status, ['Pending', 'Confirmed']))
<!-- Simple JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    const products = @json($products);
    
    // Track item index for new items
    let itemIndex = {{ $restock->items->count() }};
    
    // Function to update product options for a select
    function updateProductOptions(selectElement) {
        const currentValue = selectElement.value;
        selectElement.innerHTML = `
            <option value="">-- Select Product --</option>
            ${products.map(p => `
                <option value="${p.id}" ${p.id == currentValue ? 'selected' : ''}>
                    ${p.name} (${p.sku}) - Stock: ${p.current_stock}
                </option>
            `).join('')}
        `;
    }
    
    // Add new item row
    addButton.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'item-row bg-gray-50 p-4 rounded-lg border border-gray-200';
        
        row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Product <span class="text-red-500">*</span>
                    </label>
                    <select name="items[${itemIndex}][product_id]" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 product-select">
                        <option value="">-- Select Product --</option>
                        ${products.map(p => `
                            <option value="${p.id}">
                                ${p.name} (${p.sku}) - Stock: ${p.current_stock}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="items[${itemIndex}][quantity]" required min="1" value="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 quantity-input">
                </div>
                <div class="flex items-end">
                    <button type="button" 
                            class="remove-btn px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition duration-200 w-full">
                        Remove
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(row);
        itemIndex++;
        
        // Add remove event to new row
        const removeBtn = row.querySelector('.remove-btn');
        removeBtn.addEventListener('click', function() {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length <= 1) {
                alert('At least one product is required');
                return;
            }
            row.remove();
        });
    });
    
    // Add remove event to existing rows
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length <= 1) {
                alert('At least one product is required');
                return;
            }
            this.closest('.item-row').remove();
        });
    });
    
    // Simple form validation
    document.getElementById('restockForm').addEventListener('submit', function(e) {
        const productSelects = document.querySelectorAll('select[name^="items["][name$="][product_id]"]');
        const quantityInputs = document.querySelectorAll('input[name^="items["][name$="][quantity]"]');
        
        let hasValidItems = false;
        let errorMessages = [];
        
        productSelects.forEach((select, index) => {
            const quantityInput = quantityInputs[index];
            
            // Check if product is selected
            if (!select.value) {
                select.classList.add('border-red-500');
                errorMessages.push(`Row ${index + 1}: Please select a product`);
            } else {
                select.classList.remove('border-red-500');
            }
            
            // Check if quantity is valid
            if (!quantityInput || !quantityInput.value || quantityInput.value < 1) {
                if (quantityInput) quantityInput.classList.add('border-red-500');
                errorMessages.push(`Row ${index + 1}: Quantity must be at least 1`);
            } else {
                if (quantityInput) quantityInput.classList.remove('border-red-500');
            }
            
            // Check if both are valid
            if (select.value && quantityInput && quantityInput.value && quantityInput.value >= 1) {
                hasValidItems = true;
            }
        });
        
        if (!hasValidItems) {
            e.preventDefault();
            alert('Please add at least one product with valid quantity');
            return false;
        }
        
        if (errorMessages.length > 0) {
            e.preventDefault();
            alert('Please fix the following errors:\n' + errorMessages.join('\n'));
            return false;
        }
        
        return true;
    });
});
</script>
@endif
@endsection