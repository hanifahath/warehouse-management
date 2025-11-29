@extends('layouts.app')

@section('title', 'Create Outgoing Transaction')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Create Outgoing Transaction</h1>

    @include('shared.form-errors')

    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf
        <input type="hidden" name="type" value="Outgoing">

        <x-warehouse.card>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Date</label>
                    <input type="date" name="date" class="w-full border rounded px-2 py-1" required>
                </div>
                <div class="col-span-2">
                    <label>Notes</label>
                    <textarea name="notes" class="w-full border rounded px-2 py-1"></textarea>
                </div>
            </div>

            <h2 class="text-lg font-bold mt-6 mb-2">Transaction Items</h2>
            <div class="grid grid-cols-3 gap-2">
                <select name="items[0][product_id]" class="border rounded px-2 py-1" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                    @endforeach
                </select>
                <input type="number" name="items[0][quantity]" placeholder="Quantity" class="border rounded px-2 py-1" required>
                <input type="number" name="items[0][price_at_transaction]" placeholder="Price" class="border rounded px-2 py-1" required>
            </div>

            <div class="mt-4">
                <x-warehouse.button type="primary">Save Transaction</x-warehouse.button>
            </div>
        </x-warehouse.card>
    </form>
@endsection