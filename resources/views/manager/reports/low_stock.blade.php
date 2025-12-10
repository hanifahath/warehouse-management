@extends('layouts.app')

@section('title', 'Laporan Stok Rendah')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Laporan Produk Stok Rendah</h1>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Total Low Stock</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['total_low_stock'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Kritis</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['critical_count'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">(≤50% dari min stock)</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Nilai yang Berisiko</p>
            <p class="text-2xl font-bold">Rp {{ number_format($stats['total_value_at_risk'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Warning Alert --}}
    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 rounded-lg">
        <div class="flex">
            <svg class="h-5 w-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-bold">Perhatian Stok!</p>
                <p class="text-sm">Produk di bawah ini memiliki jumlah stok yang lebih kecil dari batas minimum aman. Segera lakukan pemesanan atau pengadaan ulang.</p>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white p-4 shadow-lg rounded-xl mb-6">
        <form action="{{ route('reports.low-stock') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4 items-end">
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
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150">
                    Filter
                </button>
                <a href="{{ route('reports.low-stock') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-150">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if ($lowStockProducts->count())
    <div class="overflow-x-auto bg-white shadow-lg rounded-xl">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-5 py-3 border-b-2 border-gray-200">SKU</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Nama Produk</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Kategori</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Stok Saat Ini</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Min. Stok Aman</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Kekurangan</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Tingkat Kritis</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lowStockProducts as $product)
                @php
                    $deficit = $product->min_stock - $product->current_stock;
                    $criticalLevel = $product->current_stock <= ($product->min_stock * 0.5) ? 'high' : ($product->current_stock <= ($product->min_stock * 0.75) ? 'medium' : 'low');
                @endphp
                <tr class="hover:bg-red-50 transition duration-150">
                    <td class="px-5 py-5 border-b border-gray-200 text-sm font-mono">{{ $product->sku }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm font-medium">
                        <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $product->name }}
                        </a>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">{{ $product->category->name ?? 'N/A' }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center font-bold text-red-700">
                        {{ number_format($product->current_stock) }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                        {{ number_format($product->min_stock) }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center font-bold text-red-700">
                        {{ number_format($deficit) }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                        @if($criticalLevel === 'high')
                            <span class="px-3 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full">
                                Tinggi
                            </span>
                        @elseif($criticalLevel === 'medium')
                            <span class="px-3 py-1 text-xs font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full">
                                Sedang
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">
                                Rendah
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                        <a href="{{ route('restocks.create', ['product_id' => $product->id]) }}" 
                           class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition duration-150">
                            Buat Restock
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $lowStockProducts->links() }}
    </div>

    @else
        <div class="text-center py-10 bg-white rounded-lg shadow">
            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-500 text-lg mt-2">Hebat! Tidak ada produk yang berada di bawah batas stok minimum saat ini.</p>
        </div>
    @endif
</div>
@endsection