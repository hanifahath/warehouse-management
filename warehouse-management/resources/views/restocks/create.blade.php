@extends('layouts.app')

@section('title', 'Create Restock Order')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Create Restock Order</h1>

    @include('shared.form-errors')

    <form method="POST" action="{{ route('restocks.store') }}">
        @csrf
        <x-warehouse.card>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Supplier</label>
                    <select name="supplier_id" class="w-full border rounded px-2 py-1" required>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Expected Delivery Date</label>
                    <input type="date" name="expected_delivery_date" class="w-full border rounded px-2 py-1" required>
                </div>
                <div class="col-span-2">
                    <label>Notes</label>
                    <textarea name="notes" class="w-full border rounded px-2 py-1"></textarea>
                </div>
            </div>

            <h2 class="text-lg font-bold mt-6 mb-2">Restock Items</h2>
            <div id="restock-items" class="space-y-2">
                <div class="grid grid-cols-3 gap-2">
                    <select name="items[0][product_id]" class="border rounded px-2 py-1" required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                    <input type="number" name="items[0][quantity]" placeholder="Quantity" class="border rounded px-2 py-1" required>
                    <input type="text" name="items[0][notes]" placeholder="Notes" class="border rounded px-2 py-1">
                </div>
            </div>

            <div class="mt-4">
                <x-warehouse.button type="primary">Save Restock Order</x-warehouse.button>
            </div>
        </x-warehouse.card>
    </form>
@endsection