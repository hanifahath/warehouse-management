@extends('layouts.app')

@section('title', 'Create Incoming Transaction')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Create Incoming Transaction</h1>
    <a href="{{ route('transactions.history') }}" 
       class="text-indigo-600 hover:underline text-sm">
        ← Back to Transactions
    </a>
</div>

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

<form action="{{ route('transactions.store.incoming') }}" method="POST" id="transactionForm">
    @csrf

    <div class="space-y-6">
        <div class="bg-white p-6 rounded-xl shadow border">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Transaction Date *</label>
                    <input type="date" name="date" 
                        value="{{ old('date', date('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-lg p-3 @error('date') border-red-500 @enderror" 
                        required>
                    @error('date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Link to Restock Order (Optional)</label>
                    <select name="restock_order_id" id="restock_order_id"
                        class="w-full border border-gray-300 rounded-lg p-3">
                        <option value="">Select Restock Order</option>
                        @foreach($restockOrders as $order)
                            <option value="{{ $order->id }}" 
                                {{ old('restock_order_id') == $order->id ? 'selected' : '' }}
                                data-supplier="{{ $order->supplier_id }}">
                                {{ $order->po_number }} - {{ optional($order->supplier)->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('restock_order_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Supplier *</label>
                    <select name="supplier_id" id="supplier_id"
                        class="w-full border border-gray-300 rounded-lg p-3 @error('supplier_id') border-red-500 @enderror" 
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
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg p-3">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow border">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Products *</h2>
                <button type="button" id="loadFromPO" 
                    class="px-4 py-2 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    Load from Restock Order
                </button>
            </div>

            @error('items')
                <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
            @enderror

            <div id="itemsContainer" class="space-y-4">
                @php
                    $oldItems = old('items', [['product_id' => '', 'quantity' => '', 'price_at_transaction' => '']]);
                @endphp
                
                @foreach($oldItems as $index => $item)
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 border p-4 rounded-lg bg-gray-50 item-row">
                    <div>
                        <label class="text-gray-700 font-medium">Product *</label>
                        <select name="items[{{ $index }}][product_id]" 
                            class="item-product w-full border border-gray-300 rounded-lg p-2 mt-1"
                            required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    data-price="{{ $product->purchase_price }}"
                                    {{ old('items.' . $index . '.product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-700 font-medium">Quantity *</label>
                        <input type="number" min="1" 
                            name="items[{{ $index }}][quantity]"
                            value="{{ old('items.' . $index . '.quantity', '') }}"
                            class="item-qty w-full border border-gray-300 rounded-lg p-2 mt-1" 
                            required>
                    </div>

                    <div>
                        <label class="text-gray-700 font-medium">Price (Rp) *</label>
                        <input type="number" min="0" step="0.01"
                            name="items[{{ $index }}][price_at_transaction]"
                            value="{{ old('items.' . $index . '.price_at_transaction', '') }}"
                            class="item-price w-full border border-gray-300 rounded-lg p-2 mt-1" 
                            required>
                        <small class="text-gray-500 text-xs">
                            @if(old('items.' . $index . '.product_id'))
                                @php
                                    $selectedProduct = $products->firstWhere('id', old('items.' . $index . '.product_id'));
                                @endphp
                                @if($selectedProduct)
                                    Product price: Rp {{ number_format($selectedProduct->purchase_price, 0) }}
                                @endif
                            @endif
                        </small>
                    </div>

                    <div>
                        <label class="text-gray-700 font-medium">Subtotal</label>
                        <div class="p-2 bg-gray-100 rounded-lg mt-1">
                            <span class="text-gray-700 font-semibold" id="subtotal_{{ $index }}">
                                Rp 0
                            </span>
                        </div>
                    </div>

                    <div class="flex items-end">
                        @if($index > 0)
                        <button type="button" 
                            class="removeItem px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm w-full">
                            Remove
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <button type="button" id="addItem"
                class="mt-6 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm font-medium">
                + Add Product
            </button>
        </div>

        <div class="bg-white p-4 rounded-xl shadow border">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                <span id="totalAmount" class="text-2xl font-bold text-green-600">
                    Rp 0
                </span>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" 
                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                Create Incoming Transaction
            </button>
            
            <a href="{{ route('transactions.history') }}"
                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium">
                Cancel
            </a>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const restockOrderSelect = document.getElementById('restock_order_id');
    const supplierSelect = document.getElementById('supplier_id');
    const loadFromPOBtn = document.getElementById('loadFromPO');
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItem');
    let itemCounter = {{ count($oldItems) }};

    restockOrderSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const supplierId = selectedOption.dataset.supplier;
        loadFromPOBtn.disabled = !this.value;
        
        if (supplierId && !supplierSelect.value) {
            supplierSelect.value = supplierId;
        }
    });

    loadFromPOBtn.addEventListener('click', function() {
        const restockOrderId = restockOrderSelect.value;
        if (!restockOrderId) return;

        const originalText = this.textContent;
        this.textContent = 'Loading...';
        this.disabled = true;

        fetch(`/transactions/restock-orders/${restockOrderId}/items`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    itemsContainer.innerHTML = '';
                    itemCounter = 0;

                    data.data.items.forEach(item => {
                        addItemRow(
                            item.product_id,
                            item.product_name,
                            item.ordered_quantity,
                            item.price,
                            item.sku,
                            item.unit
                        );
                    });

                    if (data.data.supplier_id && !supplierSelect.value) {
                        supplierSelect.value = data.data.supplier_id;
                    }

                    alert(`Loaded ${data.data.items.length} items from PO-${data.data.po_number}`);
                } else {
                    alert(data.message || 'Failed to load items');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading items: ' + error.message);
            })
            .finally(() => {
                this.textContent = originalText;
                this.disabled = false;
            });
    });

    addItemBtn.addEventListener('click', function() {
        addItemRow('', '', 1, 0);
    });

    itemsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeItem')) {
            const row = e.target.closest('.item-row');
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                reindexRows();
                updateTotalDisplay();
            }
        }
    });

    itemsContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-product')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const price = selectedOption.dataset.price || 0;
            const priceInput = e.target.closest('.item-row').querySelector('.item-price');
            if (priceInput) priceInput.value = price;
        }
        updateTotalDisplay();
    });

    itemsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price')) {
            updateTotalDisplay();
        }
    });

    function addItemRow(productId, productName, quantity, price = 0, sku = '', unit = '') {
        const formattedPrice = parseFloat(price || 0).toFixed(2);
        const subtotal = parseFloat(price || 0) * parseInt(quantity || 0);
        
        const row = `
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 border p-4 rounded-lg bg-gray-50 item-row">
                <div>
                    <label class="text-gray-700 font-medium">Product *</label>
                    <select name="items[${itemCounter}][product_id]" 
                        class="item-product w-full border border-gray-300 rounded-lg p-2 mt-1" 
                        required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                            data-price="{{ $product->purchase_price }}"
                            ${productId == {{ $product->id }} ? 'selected' : ''}>
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                        @endforeach
                    </select>
                    <small class="text-gray-500 text-xs">${sku ? 'SKU: ' + sku : ''}</small>
                </div>
                
                <div>
                    <label class="text-gray-700 font-medium">Quantity *</label>
                    <input type="number" min="1" 
                        name="items[${itemCounter}][quantity]"
                        value="${quantity}"
                        class="item-qty w-full border border-gray-300 rounded-lg p-2 mt-1" 
                        required>
                    <small class="text-gray-500 text-xs">${unit ? 'Unit: ' + unit : ''}</small>
                </div>
                
                <div>
                    <label class="text-gray-700 font-medium">Price (Rp) *</label>
                    <input type="number" min="0" step="0.01"
                        name="items[${itemCounter}][price_at_transaction]"
                        value="${formattedPrice}"
                        class="item-price w-full border border-gray-300 rounded-lg p-2 mt-1" 
                        required>
                    <small class="text-gray-500 text-xs">
                        ${price > 0 ? 'Rp ' + formatRupiah(price) : ''}
                    </small>
                </div>
                
                <div>
                    <label class="text-gray-700 font-medium">Subtotal</label>
                    <div class="p-2 bg-gray-100 rounded-lg mt-1">
                        <span class="text-gray-700 font-semibold" id="subtotal_${itemCounter}">
                            Rp ${formatRupiah(subtotal)}
                        </span>
                    </div>
                </div>
                
                <div class="flex items-end">
                    <button type="button" 
                        class="removeItem px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm w-full">
                        Remove
                    </button>
                </div>
            </div>
        `;
        
        itemsContainer.insertAdjacentHTML('beforeend', row);
        itemCounter++;
        updateTotalDisplay();
    }

    function formatRupiah(amount) {
        return parseFloat(amount || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function calculateTotal() {
        let total = 0;
        
        document.querySelectorAll('.item-row').forEach((row) => {
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const quantity = parseInt(row.querySelector('.item-qty').value) || 0;
            total += price * quantity;
        });
        
        return total;
    }

    function updateTotalDisplay() {
        const total = calculateTotal();
        const totalElement = document.getElementById('totalAmount');
        if (totalElement) {
            totalElement.textContent = `Rp ${formatRupiah(total)}`;
        }
    }

    function reindexRows() {
        document.querySelectorAll('.item-row').forEach((row, index) => {
            row.querySelector('.item-product').name = `items[${index}][product_id]`;
            row.querySelector('.item-qty').name = `items[${index}][quantity]`;
            row.querySelector('.item-price').name = `items[${index}][price_at_transaction]`;
        });
        itemCounter = document.querySelectorAll('.item-row').length;
    }

    updateTotalDisplay();
});
</script>
@endsection