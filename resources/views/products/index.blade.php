@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- PAGE TITLE --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Products</h1>

        @can('create', App\Models\Product::class)
        <a href="{{ route('products.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm font-medium">
            + Add Product
        </a>
        @endcan
    </div>

    {{-- FILTERS CARD --}}
    <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
        <form method="GET" class="space-y-4">
            
            {{-- SEARCH ROW --}}
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Search Product</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, SKU, or description..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
            </div>

            {{-- FILTERS ROW --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                
                {{-- CATEGORY FILTER --}}
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">Category</label>
                    <select name="category_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" 
                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- STOCK STATUS FILTER --}}
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">Stock Status</label>
                    <select name="stock_status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">All Stock Status</option>
                        <option value="safe" {{ request('stock_status') == 'safe' ? 'selected' : '' }}>Safe Stock</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="empty" {{ request('stock_status') == 'empty' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>

                {{-- SORTING FILTER --}}
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">Sort By</label>
                    <select name="sort"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Default (Newest First)</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A → Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z → A)</option>
                        <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>Stock (Low → High)</option>
                        <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stock (High → Low)</option>
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
            </div>

            {{-- ACTION BUTTONS - Di pojok kanan bawah --}}
            <div class="flex justify-end items-center pt-3 border-t border-gray-200">
                <div class="flex gap-2">
                    @if(request()->anyFilled(['search', 'category_id', 'stock_status', 'sort']))
                        <a href="{{ route('products.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium text-sm">
                            Reset
                        </a>
                    @endif
                    
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm">
                        Apply Filters
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- ACTIVE FILTERS DISPLAY --}}
    @if(request()->anyFilled(['search', 'category_id', 'stock_status', 'sort']))
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Active filters:</span>
                
                @if(request('search'))
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    Search: "{{ request('search') }}"
                </span>
                @endif
                
                @if(request('category_id'))
                    @php
                        $selectedCategory = $categories->firstWhere('id', request('category_id'));
                    @endphp
                    @if($selectedCategory)
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                        Category: {{ $selectedCategory->name }}
                    </span>
                    @endif
                @endif
                
                @if(request('stock_status'))
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    Stock: {{ ucfirst(request('stock_status')) }}
                </span>
                @endif
                
                @if(request('sort'))
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    Sort: {{ 
                        match(request('sort')) {
                            'name_asc' => 'Name A-Z',
                            'name_desc' => 'Name Z-A',
                            'stock_asc' => 'Stock Low-High',
                            'stock_desc' => 'Stock High-Low',
                            'latest' => 'Newest',
                            'oldest' => 'Oldest',
                            default => 'Custom'
                        }
                    }}
                </span>
                @endif
            </div>
            
            <div class="text-sm">
                <span class="font-medium text-gray-700">{{ $products->total() }}</span>
                <span class="text-gray-600"> products found</span>
            </div>
        </div>
    </div>
    @endif

    {{-- PRODUCT TABLE --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        
        {{-- TABLE HEADER --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Product List</h2>
            <span class="text-sm text-gray-600">
                Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }}
            </span>
        </div>

        {{-- TABLE --}}
        @if($products->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3">Product Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Current Stock</th>
                        <th class="px-6 py-3">Min Stock</th>
                        <th class="px-6 py-3">Rack Location</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-gray-200">
                    @foreach ($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors">
                        
                        {{-- SKU --}}
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">
                                {{ $product->sku }}
                            </span>
                        </td>

                        {{-- NAME --}}
                        <td class="px-6 py-4">
                            <div class="flex items-start space-x-3">
                                @if($product->image_path)
                                <div class="flex-shrink-0">
                                    <img src="{{ $product->image_path }}" 
                                    alt="{{ $product->name }}"
                                    class="img-fluid"
                                    onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                                </div>
                                @endif
                                <div>
                                    <a href="{{ route('products.show', $product) }}"
                                       class="font-medium text-gray-900 hover:text-indigo-600 hover:underline">
                                        {{ $product->name }}
                                    </a>
                                    <div class="text-sm text-gray-500 mt-1">
                                        @if($product->description)
                                            {{ Str::limit($product->description, 50) }}
                                        @else
                                            <span class="text-gray-400">No description</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- CATEGORY --}}
                        <td class="px-6 py-4">
                            @if($product->category)
                                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs">
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        {{-- CURRENT STOCK --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <span class="font-medium {{ 
                                    $product->current_stock == 0 ? 'text-red-600' : 
                                    ($product->current_stock <= $product->min_stock ? 'text-yellow-600' : 'text-green-600') 
                                }}">
                                    {{ $product->current_stock }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500">{{ $product->unit }}</span>
                            </div>
                        </td>

                        {{-- MIN STOCK --}}
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $product->min_stock }} {{ $product->unit }}</span>
                        </td>

                        {{-- RACK LOCATION --}}
                        <td class="px-6 py-4">
                            @if($product->rack_location)
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">
                                    {{ $product->rack_location }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        {{-- ACTIONS --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-3">
                                
                                {{-- VIEW --}}
                                <a href="{{ route('products.show', $product) }}"
                                   class="text-indigo-600 hover:text-indigo-800"
                                   title="View details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                {{-- EDIT --}}
                                @can('update', $product)
                                <a href="{{ route('products.edit', $product) }}"
                                   class="text-yellow-600 hover:text-yellow-800"
                                   title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endcan

                                {{-- DELETE --}}
                                @can('delete', $product)
                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                      class="inline"
                                      onsubmit="return confirm('Delete product {{ $product->name }}? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800"
                                            title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        {{-- EMPTY STATE --}}
        <div class="py-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
            <p class="text-gray-500 mb-6">
                @if(request()->anyFilled(['search', 'category_id', 'stock_status']))
                    Try adjusting your filters or search terms
                @else
                    Get started by creating your first product
                @endif
            </p>
            @can('create', App\Models\Product::class)
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Product
            </a>
            @endcan
        </div>
        @endif
    </div>

    {{-- PAGINATION --}}
    @if($products->hasPages())
    <div class="mt-6">
        {{ $products->withQueryString()->links() }}
    </div>
    @endif

</div>

{{-- DEBUG SCRIPT (Optional) --}}
@if(config('app.debug'))
<script>
console.log('Product List Debug:');
console.log('Sort Parameter:', '{{ request("sort") }}');
console.log('Total Products:', {{ $products->total() }});
console.log('Stock Sorting:', {
    'stock_asc': 'Low to High',
    'stock_desc': 'High to Low'
}[ '{{ request("sort") }}' ]);
</script>
@endif

@endsection