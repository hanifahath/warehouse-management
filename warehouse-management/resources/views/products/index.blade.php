@extends('layouts.app')

@section('title', 'Product List')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Product List</h1>

    {{-- Search & Filter --}}
    @include('shared.search-bar')
    @include('shared.filter', ['filters' => ['Category', 'Stock Status', 'Sort']])

    <x-warehouse.card>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">SKU</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Category</th>
                    <th class="p-2">Stock</th>
                    <th class="p-2">Location</th>
                    <th class="p-2">Actions</th>
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
                        <td class="p-2">{{ $product->location }}</td>
                        <td class="p-2 flex space-x-2">
                            <a href="{{ route('products.show', $product) }}" 
                               class="text-blue-600 hover:underline">View</a>
                            <a href="{{ route('products.edit', $product) }}" 
                               class="text-yellow-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:underline"
                                        onclick="return confirm('Delete this product?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @include('shared.pagination', ['paginator' => $products])
    </x-warehouse.card>
@endsection