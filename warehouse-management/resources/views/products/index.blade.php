@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- PAGE TITLE --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Products</h1>

        @can('create', App\Models\Product::class)
        <a href="{{ route('admin.products.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Add Product
        </a>
        @endcan
    </div>

    {{-- SEARCH + FILTERS --}}
    <form method="GET" class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Search --}}
            <div class="md:col-span-2">
                <label class="block text-gray-700 text-sm mb-1">Search Product</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name or SKU..."
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Category --}}
            <div>
                <label class="block text-gray-700 text-sm mb-1">Category</label>
                <select name="category_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" 
                            @selected(request('category_id') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Stock Status --}}
            <div>
                <label class="block text-gray-700 text-sm mb-1">Stock Status</label>
                <select name="stock_status"
                        class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="">All</option>
                    <option value="safe" @selected(request('stock_status')=='safe')>Safe</option>
                    <option value="low" @selected(request('stock_status')=='low')>Low Stock</option>
                    <option value="empty" @selected(request('stock_status')=='empty')>Out of Stock</option>
                </select>
            </div>

        </div>

        <div class="mt-4 flex justify-end">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Filter
            </button>
        </div>
    </form>


    {{-- SORTING --}}
    <div class="flex justify-end mb-3">
        <form method="GET">
            <select name="sort"
                onchange="this.form.submit()"
                class="px-3 py-2 border border-gray-300 rounded text-sm">
                <option value="">Sort By</option>
                <option value="name_asc" @selected(request('sort')=='name_asc')>Name (A-Z)</option>
                <option value="name_desc" @selected(request('sort')=='name_desc')>Name (Z-A)</option>
                <option value="stock_asc" @selected(request('sort')=='lowest_stock')>Stock (Low → High)</option>
                <option value="stock_desc" @selected(request('sort')=='highest_stock')>Stock (High → Low)</option>
                <option value="latest" @selected(request('sort')=='latest')>Newest</option>
            </select>
        </form>
    </div>


    {{-- PRODUCT TABLE --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">

        <table class="w-full">
            <thead class="bg-gray-50 text-gray-600 text-sm border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">SKU</th>
                    <th class="px-4 py-3 text-left font-medium">Name</th>
                    <th class="px-4 py-3 text-left font-medium">Category</th>
                    <th class="px-4 py-3 text-left font-medium">Stock</th>
                    <th class="px-4 py-3 text-left font-medium">Rack</th>
                    <th class="px-4 py-3 text-right font-medium">Actions</th>
                </tr>
            </thead>

            <tbody class="text-sm text-gray-700">

                @forelse ($products as $product)
                    <tr class="border-b last:border-0 hover:bg-gray-50">

                        {{-- SKU --}}
                        <td class="px-4 py-3">{{ $product->sku }}</td>

                        {{-- Name --}}
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.products.show', $product) }}"
                               class="text-indigo-600 hover:underline">
                                {{ $product->name }}
                            </a>
                        </td>

                        {{-- Category --}}
                        <td class="px-4 py-3">{{ $product->category->name ?? '-' }}</td>

                        {{-- Stock with Badge --}}
                        <td class="px-4 py-3">
                            @if($product->current_stock == 0)
                                <span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs">
                                    Out of Stock
                                </span>
                            @elseif($product->current_stock <= $product->min_stock)
                                <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs">
                                    Low ({{ $product->stock }})
                                </span>
                            @else
                                <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs">
                                    {{ $product->current_stock }}
                                </span>
                            @endif
                        </td>

                        {{-- Rack --}}
                        <td class="px-4 py-3">{{ $product->rack_location ?? '-' }}</td>

                        {{-- ACTIONS --}}
                        <td class="px-4 py-3 text-right">

                            {{-- EDIT --}}
                            @can('update', $product)
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="text-indigo-600 hover:underline mr-3">
                                Edit
                            </a>
                            @endcan

                            {{-- DELETE --}}
                            @can('delete', $product)
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                  class="inline"
                                  onsubmit="return confirm('Delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                            @endcan

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>


    {{-- Pagination --}}
    <div class="mt-4">
        {{ $products->links() }}
    </div>

</div>
@endsection
