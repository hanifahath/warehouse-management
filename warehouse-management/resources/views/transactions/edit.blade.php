@extends('layouts.app')

@section('title', 'Edit Transaction')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Transaction</h1>

    @include('shared.form-errors')

    <form method="POST" action="{{ route('transactions.update', $transaction) }}">
        @csrf @method('PUT')

        <x-warehouse.card>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Type</label>
                    <input type="text" value="{{ $transaction->type }}" class="w-full border rounded px-2 py-1 bg-gray-200" disabled>
                </div>
                <div>
                    <label>Date</label>
                    <input type="date" name="date" value="{{ $transaction->date->format('Y-m-d') }}" class="w-full border rounded px-2 py-1" required>
                </div>
                <div class="col-span-2">
                    <label>Notes</label>
                    <textarea name="notes" class="w-full border rounded px-2 py-1">{{ $transaction->notes }}</textarea>
                </div>
            </div>

            <h2 class="text-lg font-bold mt-6 mb-2">Transaction Items</h2>
            @foreach($transaction->items as $i => $item)
                <div class="grid grid-cols-3 gap-2 mb-2">
                    <select name="items[{{ $i }}][product_id]" class="border rounded px-2 py-1" required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $item->