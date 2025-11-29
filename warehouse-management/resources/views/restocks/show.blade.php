@extends('layouts.app')

@section('title', 'Restock Order Details')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Restock Order Details</h1>

    <x-warehouse.card>
        <div class="grid grid-cols-2 gap-4">
            <div><strong>PO Number:</strong> {{ $restock->po_number }}</div>
            <div><strong>Supplier:</strong> {{ $restock->supplier->name }}</div>
            <div><strong>Order Date:</strong> {{ $restock->order_date->format('d M Y') }}</div>
            <div><strong>Expected Delivery:</strong> {{ $restock->expected_delivery_date->format('d M Y') }}</div>
            <div><strong>Status:</strong> <x-warehouse.badge status="{{ strtolower($restock->status) }}" /></div>
            <div><strong>Notes:</strong> {{ $restock->notes }}</div>
        </div>
    </x-warehouse.card>

    <h2 class="text-xl font-bold mt-6 mb-2">Items</h2>
    <x-warehouse.card>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Product</th>
                    <th class="p-2">Quantity</th>
                    <th class="p-2">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($restock->items as $item)
                    <tr class="border-b">
                        <td class="p-2">{{ $item->product->name }} ({{ $item->product->sku }})</td>
                        <td class="p-2">{{ $item->quantity }}</td>
                        <td class="p-2">{{ $item->notes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-warehouse.card>

    {{-- Timeline Progress --}}
    <h2 class="text-xl font-bold mt-6 mb-2">Order Timeline</h2>
    <x-warehouse.card>
        <ul class="list-disc pl-5">
            <li>Created: {{ $restock->order_date->format('d M Y H:i') }}</li>
            @if($restock->status === 'Confirmed')
                <li>Confirmed by Supplier</li>
            @endif
            @if($restock->status === 'Delivered')
                <li>Delivered to Warehouse</li>
            @endif
        </ul>
    </x-warehouse.card>

    {{-- Supplier Confirmation --}}
    @role('Supplier')
        @if($restock->status === 'Pending')
            <div class="mt-4">
                <form method="POST" action="{{ route('restocks.confirm', $restock) }}">
                    @csrf
                    <x-warehouse.button type="primary">Confirm Order</x-warehouse.button>
                </form>
            </div>
        @endif
    @endrole
@endsection