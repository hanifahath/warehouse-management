@extends('layouts.app')

@section('title', $category->name . ' - Category Details')

@push('styles')
<style>
    /* Styling for different stock levels for better visibility */
    .stock-out { color: #dc2626; } /* text-red-600 */
    .stock-low { color: #d97706; } /* text-yellow-600 */
    .stock-in { color: #10b981; } /* text-green-600 */

    .tag-out-of-stock { background-color: #fef2f2; color: #b91c1c; } /* bg-red-100 text-red-800 */
    .tag-low-stock { background-color: #fffbeb; color: #92400e; } /* bg-yellow-100 text-yellow-800 */
    .tag-in-stock { background-color: #ecfdf5; color: #065f46; } /* bg-green-100 text-green-800 */
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Category Details</h1>
            <p class="text-gray-600 mt-1">View and manage category information</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('categories.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Categories
            </a>

            @can('update', $category)
                <a href="{{ route('categories.edit', $category) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Category Information</h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">
                                    Basic Information
                                </h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Category Name</dt>
                                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $category->name }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                                        <dd class="mt-1 text-gray-900">
                                            @if($category->description)
                                                {{ $category->description }}
                                            @else
                                                <span class="text-gray-400 italic">No description provided</span>
                                            @endif
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Category ID</dt>
                                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $category->id }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">
                                    Statistics
                                </h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Total Products</dt>
                                        <dd class="mt-1 text-2xl font-bold text-indigo-600">
                                            {{ $category->products_count ?? $category->products()->count() }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                                        <dd class="mt-1 text-gray-900">
                                            {{ $category->created_at->format('F d, Y') }}
                                            <span class="text-sm text-gray-500">({{ $category->created_at->diffForHumans() }})</span>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                        <dd class="mt-1 text-gray-900">
                                            {{ $category->updated_at->format('F d, Y') }}
                                            <span class="text-sm text-gray-500">({{ $category->updated_at->diffForHumans() }})</span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Products in This Category</h2>
                    <span class="text-sm text-gray-600">
                        {{ $category->products_count ?? $category->products()->count() }} {{ Str::plural('product', $category->products_count ?? $category->products()->count()) }}
                    </span>
                </div>

                <div class="p-6">
                    @if(($category->products_count ?? $category->products()->count()) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            SKU
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Product Name
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stock
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($category->products->take(10) as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                                {{ $product->sku }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <a href="{{ route('products.show', $product) }}"
                                               class="font-medium text-gray-900 hover:text-indigo-600 hover:underline">
                                                {{ $product->name }}
                                            </a>
                                            @if($product->description)
                                            <p class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                                {{ Str::limit($product->description, 60) }}
                                            </p>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <span class="font-medium @if($product->current_stock == 0) stock-out @elseif($product->current_stock <= $product->min_stock) stock-low @else stock-in @endif">
                                                {{ $product->current_stock }}
                                            </span>
                                            <span class="text-xs text-gray-500">{{ $product->unit }}</span>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            @if($product->current_stock == 0)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full tag-out-of-stock">
                                                    Out of Stock
                                                </span>
                                            @elseif($product->current_stock <= $product->min_stock)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full tag-low-stock">
                                                    Low Stock
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium rounded-full tag-in-stock">
                                                    In Stock
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(($category->products_count ?? $category->products()->count()) > 10)
                            <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                                <p class="text-sm text-gray-600">
                                    Showing 10 of {{ $category->products_count ?? $category->products()->count() }} products
                                </p>
                                <a href="{{ route('products.index', ['category_id' => $category->id]) }}"
                                   class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                    View all products in this category
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                            <p class="mt-1 text-sm text-gray-500">No products have been assigned to this category yet.</p>
                            <div class="mt-6">
                                @can('create', App\Models\Product::class)
                                    <a href="{{ route('products.create') }}?category_id={{ $category->id }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Product to Category
                                    </a>
                                @else
                                    <button disabled
                                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Product to Category
                                    </button>
                                    <p class="text-xs text-gray-500 mt-2">Only admin and manager can add products</p>
                                @endcan
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Category Image</h2>
                </div>

                <div class="p-6">
                    @if($category->image_path)
                        <div class="relative w-full h-64 rounded-lg overflow-hidden border border-gray-300">
                            <img src="{{ Storage::url($category->image_path) }}"
                                 alt="{{ $category->name }}"
                                 class="w-full h-full object-cover">
                            <a href="{{ Storage::url($category->image_path) }}"
                               target="_blank"
                               class="absolute top-2 right-2 bg-white text-gray-700 rounded-full p-2 hover:bg-gray-100 focus:outline-none shadow-sm"
                               title="View full size">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </a>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-500">
                                Image uploaded: {{ $category->updated_at->format('M d, Y') }}
                            </p>
                        </div>
                    @else
                        <div class="w-full h-64 rounded-lg border-2 border-dashed border-gray-300 flex flex-col items-center justify-center bg-gray-50">
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No image uploaded</p>
                            @can('update', $category)
                                <a href="{{ route('categories.edit', $category) }}"
                                   class="mt-3 text-sm text-indigo-600 hover:text-indigo-900">
                                    Upload an image
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        @can('update', $category)
                            <a href="{{ route('categories.edit', $category) }}"
                               class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">Edit Category</span>
                                </div>
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <div class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-md bg-gray-50 opacity-50 cursor-not-allowed">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-500">Edit Category</span>
                                </div>
                                <span class="text-xs text-gray-500 px-2 py-1 bg-gray-200 rounded">Admin/Manager Only</span>
                            </div>
                        @endcan

                        @can('create', App\Models\Product::class)
                            <a href="{{ route('products.create') }}?category_id={{ $category->id }}"
                               class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">Add New Product</span>
                                </div>
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <div class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-md bg-gray-50 opacity-50 cursor-not-allowed">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-500">Add New Product</span>
                                </div>
                                <span class="text-xs text-gray-500 px-2 py-1 bg-gray-200 rounded">
                                    {{ auth()->user()->isStaff() ? 'Staff cannot add products' : 'Unauthorized' }}
                                </span>
                            </div>
                        @endcan

                        @can('viewAny', App\Models\Product::class)
                            <a href="{{ route('products.index', ['category_id' => $category->id]) }}"
                               class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">View All Products</span>
                                </div>
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <div class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-md bg-gray-50 opacity-50 cursor-not-allowed">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-500">View All Products</span>
                                </div>
                                <span class="text-xs text-gray-500 px-2 py-1 bg-gray-200 rounded">No Access</span>
                            </div>
                        @endcan

                        @can('delete', $category)
                            <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                  onsubmit="return confirm('Delete category {{ $category->name }}? All products in this category will be uncategorized.')"
                                  class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full flex items-center justify-between px-4 py-3 border border-red-300 text-red-700 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span class="text-sm font-medium">Delete Category</span>
                                    </div>
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <div class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-md bg-gray-50 opacity-50 cursor-not-allowed">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-500">Delete Category</span>
                                </div>
                                <span class="text-xs text-gray-500 px-2 py-1 bg-gray-200 rounded">
                                    @if(($category->products_count ?? $category->products()->count()) > 0)
                                        Has Products
                                    @else
                                        Admin/Manager Only
                                    @endif
                                </span>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection