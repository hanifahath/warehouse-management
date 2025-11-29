@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Inventory Report</h1>

    {{-- Filter & Search --}}
    @include('shared.search-bar')
    @include('shared.filter', ['filters' => ['Category', 'Unit']])

    <x-warehouse.card>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">SKU</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Category</th>
                    <th class="p-2">Stock</th>
                    <th class="p-2">Unit</th>
                    <th class="p-2">Location</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="border-b">
                        <td class="p-2">{{ $product->sku }}</td>
                        <td class="p-2">{{ $product->name }}</td>
                        <td class="p-2">{{ $product->category->name }}</td>
                        <td class="p-2">
                            <x-warehouse.badge 
                                status="{{ $product->stock < $product->min_stock ? 'low' : 'ok' }}" />
                            {{ $product->stock }}
                        </td>
                        <td class="p-2">{{ $product->unit }}</td>
                        <td class="p-2">{{ $product->location }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-warehouse.card>
@endsection