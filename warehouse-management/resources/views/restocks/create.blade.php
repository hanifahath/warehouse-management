@extends('layouts.app')

@section('title', 'Create Restock Order')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded shadow p-6 border">

        <h2 class="text-2xl font-semibold mb-6">Create Restock Order</h2>

        <form action="{{ route('restocks.store') }}" method="POST">
            @csrf

            {{-- SUPPLIER --}}
            <div class="mb-5">
                <label class="block font-medium mb-1">Supplier</label>
                <select name="supplier_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Select Supplier --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- NOTES --}}
            <div class="mb-5">
                <label class="block font-medium mb-1">Notes (optional)</label>
                <textarea name="notes" class="w-full border rounded px-3 py-2"
                          rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- ITEMS --}}
            <div>
                <h3 class="text-lg font-semibold mb-3">Restock Items</h3>

                <div id="items-container">
                    <div class="item-row grid grid-cols-4 gap-3 mb-4 border p-4 rounded bg-gray-50">
                        
                        {{-- PRODUCT --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Product</label>
                            <select name="items[0][product_id]" class="w-full border rounded px-2 py-2">
                                <option value="">-- Select Product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- QUANTITY --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Qty</label>
                            <input type="number" min="1" name="items[0][quantity]" class="w-full border rounded px-2 py-2" />
                        </div>

                        {{-- PRICE --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Price</label>
                            <input type="number" min="0" name="items[0][price]" class="w-full border rounded px-2 py-2" />
                        </div>

                        {{-- REMOVE BUTTON --}}
                        <div class="flex items-end">
                            <button type="button"
                                class="remove-item bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 w-full">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ADD ITEM BUTTON --}}
                <button type="button" id="add-item"
                    class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    + Add Item
                </button>
            </div>

            {{-- SUBMIT --}}
            <div class="mt-8 flex justify-end">
                <a href="{{ route('restocks.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded mr-3">Cancel</a>

                <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Create Restock
                </button>
            </div>

        </form>
    </div>
</div>

{{-- SCRIPT --}}
<script>
    let index = 1;

    document.getElementById('add-item').addEventListener('click', function () {
        const container = document.getElementById('items-container');

        const row = document.createElement('div');
        row.classList.add('item-row', 'grid', 'grid-cols-4', 'gap-3', 'mb-4', 'border', 'p-4', 'rounded', 'bg-gray-50');

        row.innerHTML = `
            <div>
                <label class="block text-sm font-medium mb-1">Product</label>
                <select name="items[${index}][product_id]" class="w-full border rounded px-2 py-2">
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Qty</label>
                <input type="number" min="1" name="items[${index}][quantity]" class="w-full border rounded px-2 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Price</label>
                <input type="number" min="0" name="items[${index}][price]" class="w-full border rounded px-2 py-2" />
            </div>

            <div class="flex items-end">
                <button type="button" class="remove-item bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 w-full">
                    Remove
                </button>
            </div>
        `;

        container.appendChild(row);
        index++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });
</script>
@endsection
