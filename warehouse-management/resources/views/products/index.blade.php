@extends('layouts.app')

@section('title', 'Product List')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Product List</h1>
        
        @role('Admin|Manager')
        <a href="{{ route('admin.products.create') }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-black font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Tambah Produk Baru
        </a>
        @endrole
    </div>

    {{-- Search & Filter Form --}}
    {{-- Form ini akan mengirimkan parameter GET untuk filter dan search --}}
    <form method="GET" action="{{ route('admin.products.index') }}" class="space-y-4">
        
        {{-- Search Bar --}}
        @include('shared.search-bar')
        
        {{-- Filter Section (menggunakan $categories) --}}
        {{-- Catatan: Variabel $categories harus dikirim dari Controller --}}
        @include('shared.filter', ['filters' => ['Category', 'Stock Status', 'Sort'], 'categories' => $categories])
        
        <div class="flex space-x-2">
            <x-warehouse.button type="secondary" class="bg-indigo-600 text-white hover:bg-indigo-700">
                Apply Filters
            </x-warehouse.button>
            <a href="{{ route('admin.products.index') }}" class="inline-block">
                <x-warehouse.button type="outline">
                    Reset
                </x-warehouse.button>
            </a>
        </div>
    </form>

    <x-warehouse.card class="mt-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-200 text-left text-sm font-semibold text-gray-600">
                        <th class="p-3">SKU</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Category</th>
                        <th class="p-3">Stock</th>
                        <th class="p-3">Location</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="p-3 whitespace-nowrap">{{ $product->sku }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $product->name }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $product->category->name }}</td>
                            <td class="p-3 whitespace-nowrap">
                                @php
                                    $status = $product->stock < $product->min_stock ? 'low' : 'ok';
                                @endphp
                                <x-warehouse.badge status="{{ $status }}">
                                    {{ $product->stock }}
                                </x-warehouse.badge>
                            </td>
                            <td class="p-3 whitespace-nowrap">{{ $product->location }}</td>
                            <td class="p-3 flex space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.products.show', $product) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition">View</a>
                                
                                @role('Admin')
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 transition">Edit</a>
                                    
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Are you sure you want to delete {{ $product->name }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition">
                                            Delete
                                        </button>
                                    </form>
                                @endrole
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4 text-gray-500">
                                No products found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $products->links('shared.pagination') }} 
            {{-- Menggunakan blade pagination custom --}}
        </div>
    </x-warehouse.card>
@endsection