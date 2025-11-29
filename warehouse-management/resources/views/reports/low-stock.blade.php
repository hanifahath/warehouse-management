@extends('layouts.app')

@section('title', 'Low Stock Report')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Low Stock Products</h1>

    <x-warehouse.stat-card title="Total Low Stock Products" value="{{ $lowStockCount }}" />

    <x-warehouse.card>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">SKU</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Category</th>
                    <th class="p-2">Stock</th>
                    <th class="p-2">Min Stock</th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts as $product)
                    <tr class="border-b">
                        <td class="p-2">{{ $product->sku }}</td>
                        <td class="p-2">{{ $product->name }}</td>
                        <td class="p-2">{{ $product->category->name }}</td>
                        <td class="p-2">
                            <x-warehouse.badge status="low" />
                            {{ $product->stock }}
                        </td>
                        <td class="p-2">{{ $product->min_stock }}</td>
                        <td class="p-2">
                            @role('Manager')
                                <x-warehouse.button type="primary">
                                    Create Restock Order
                                </x-warehouse.button>
                            @endrole
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-warehouse.card>
@endsection