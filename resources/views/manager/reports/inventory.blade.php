@extends('layouts.app')

@section('title', 'Laporan Inventaris')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Laporan Inventaris Produk</h1>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Total Produk</p>
            <p class="text-2xl font-bold">{{ $stats['total_products'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Nilai Inventori</p>
            <p class="text-2xl font-bold">Rp {{ number_format($stats['total_value'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Low Stock</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['low_stock_count'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Out of Stock</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['out_of_stock_count'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white p-4 shadow-lg rounded-xl mb-6">
        <form action="{{ route('reports.inventory') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4 items-end">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700">Cari Produk</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       placeholder="Nama atau SKU..."
                       class="mt-1 block w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                <select id="category_id" name="category_id"
                        class="mt-1 block w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="stock_status" class="block text-sm font-medium text-gray-700">Status Stok</label>
                <select id="stock_status" name="stock_status"
                        class="mt-1 block w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="healthy" {{ request('stock_status') == 'healthy' ? 'selected' : '' }}>Stok Aman</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150">
                    Filter
                </button>
                <a href="{{ route('reports.inventory') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-150">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if ($products->count())
    <div class="overflow-x-auto bg-white shadow-lg rounded-xl">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-5 py-3 border-b-2 border-gray-200">SKU</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Nama Produk</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Kategori</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Stok Saat Ini</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Min. Stok</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Harga Beli</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Nilai Stok</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-5 py-5 border-b border-gray-200 text-sm font-mono">{{ $product->sku }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm font-medium">
                        <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $product->name }}
                        </a>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">{{ $product->category->name ?? 'N/A' }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                        <span class="font-bold @if($product->current_stock <= $product->min_stock) text-red-600 @else text-gray-800 @endif">
                            {{ number_format($product->current_stock) }}
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">{{ number_format($product->min_stock) }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                        Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center font-bold">
                        Rp {{ number_format($product->current_stock * $product->purchase_price, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                        @if($product->current_stock == 0)
                            <span class="px-3 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full">
                                Habis
                            </span>
                        @elseif($product->current_stock <= $product->min_stock)
                            <span class="px-3 py-1 text-xs font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full">
                                Low Stock
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                                Aman
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>

    @else
        <div class="text-center py-10 bg-white rounded-lg shadow">
            <p class="text-gray-500 text-lg">Tidak ada data produk ditemukan.</p>
            @if(request()->hasAny(['search', 'category_id', 'stock_status']))
                <p class="text-sm text-gray-400 mt-2">Coba ubah filter pencarian Anda</p>
            @endif
        </div>
    @endif
</div>
@endsection