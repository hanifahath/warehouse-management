@extends('layouts.app')

@section('title', 'Edit Transaction')

@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-900">Edit Transaction #{{ $transaction->id }}</h1>

    <a href="{{ route(transactions.show', $transaction->id) }}" 
       class="text-indigo-600 hover:underline text-sm">
        ← Back to Detail
    </a>
</div>

@if($transaction->status !== 'pending')
    <div class="bg-red-100 text-red-800 p-4 rounded-lg border border-red-300 mb-4">
        This transaction cannot be edited because it is already <strong>{{ $transaction->status }}</strong>.
    </div>
@endif

<form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Type (readonly) --}}
    <input type="hidden" name="type" value="{{ $transaction->type }}">

    <div class="bg-white p-5 rounded-xl shadow border border-gray-200">
        <h2 class="text-gray-900 font-semibold mb-4">
            Edit Items — {{ ucfirst($transaction->type) }}
        </h2>

        <div id="itemsContainer" class="space-y-4">

            @foreach($transaction->items as $item)
            <div class="grid grid-cols-{{ $transaction->type=='outgoing' ? '4' : '3' }} gap-4 border p-4 rounded-lg bg-gray-50 item-row">

                {{-- Product --}}
                <div class="{{ $transaction->type=='outgoing' ? 'col-span-2' : '' }}">
                    <label class="text-gray-700 font-medium">Product</label>
                    <select name="product_id[]" 
                        class="productSelect w-full border border-gray-300 rounded-lg p-2 mt-1"
                        required>

                        <option value="">Select Product</option>

                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                data-stock="{{ $product->stock }}"
                                @if($product->id == $item->product_id) selected @endif>
                                {{ $product->name }} ({{ $product->sku }}) 
                                — Stock: {{ $product->stock }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- Quantity --}}
                <div>
                    <label class="text-gray-700 font-medium">Quantity</label>
                    <input type="number" name="quantity[]" 
                        min="1"
                        class="quantityInput w-full border border-gray-300 rounded-lg p-2 mt-1"
                        value="{{ $item->quantity }}"
                        required>
                </div>

                {{-- Remove --}}
                <div class="flex items-end">
                    <button type="button" 
                        class="removeItem px-3 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm">
                        Remove
                    </button>
                </div>

            </div>
            @endforeach

        </div>

        <button type="button" id="addItem"
            class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">
            + Add Item
        </button>
    </div>

    {{-- Submit --}}
    <button type="submit"
        class="mt-6 px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
        Update Transaction
    </button>
</form>

<script>
function updateMaxQuantity(selectEl) {
    let stock = selectEl.selectedOptions[0].dataset.stock;
    let qtyInput = selectEl.closest('.item-row').querySelector('.quantityInput');
    qtyInput.max = stock;
}

document.querySelectorAll('.productSelect').forEach(select => {
    select.addEventListener('change', function () {
        updateMaxQuantity(this);
    });
});

document.getElementById('addItem').addEventListener('click', function () {
    let container = document.getElementById('itemsContainer');
    let rows = document.querySelectorAll('.item-row');
    let clone = rows[0].cloneNode(true);

    clone.querySelectorAll('input').forEach(i => i.value = '');
    clone.querySelector('.productSelect').value = '';

    container.appendChild(clone);
});

document.addEventListener('click', function(e){
    if(e.target.classList.contains('removeItem')){
        let rows = document.querySelectorAll('.item-row');
        if(rows.length > 1){
            e.target.closest('.item-row').remove();
        }
    }
});
</script>

@endsection
