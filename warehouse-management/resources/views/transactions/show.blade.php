@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Transaction Details</h1>

    <x-warehouse.card>
        <div class="grid grid-cols-2 gap-4">
            <div><strong>Transaction #:</strong> {{ $transaction->transaction_number }}</div>
            <div><strong>Type:</strong> {{ $transaction->type }}</div>
            <div><strong>Date:</strong> {{ $transaction->date->format('d M Y') }}</div>
            <div>
                <strong>Status:</strong>
                <x-warehouse.badge status="{{ strtolower($transaction->status) }}" />
            </div>
            @if($transaction->type === 'Incoming')
                <div><strong>Supplier:</strong> {{ $transaction->supplier->name }}</div>
            @else
                <div><strong>Customer:</strong> {{ $transaction->customer_name }}</div>
            @endif
            <div><strong>Notes:</strong> {{ $transaction->notes }}</div>
        </div>
    </x-warehouse.card>

    <h2 class="text-xl font-bold mt-6 mb-2">Transaction Items</h2>
    <x-warehouse.card>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Product</th>
                    <th class="p-2">Quantity</th>
                    <th class="p-2">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                    <tr class="border-b">
                        <td class="p-2">{{ $item->product->name }} ({{ $item->product->sku }})</td>
                        <td class="p-2">{{ $item->quantity }}</td>
                        <td class="p-2">Rp {{ number_format($item->price_at_transaction, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-warehouse.card>

    {{-- Aksi tambahan --}}
    <div class="mt-4 flex space-x-2">
        <a href="{{ route('transactions.edit', $transaction) }}">
            <x-warehouse.button type="primary">Edit</x-warehouse.button>
        </a>
        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}">
            @csrf @method('DELETE')
            <x-warehouse.button type="danger" onclick="return confirm('Delete this transaction?')">
                Delete
            </x-warehouse.button>
        </form>
    </div>
@endsection