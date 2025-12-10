@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
            <p class="text-gray-600 mt-1">Manage product categories</p>
        </div>

        @can('create', App\Models\Category::class)
        <a href="{{ route('categories.create') }}"
        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Category
        </a>
        @endcan

    </div>

    {{-- ALERT MESSAGES --}}
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

    {{-- FILTERS CARD --}}
<div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
    <form method="GET" class="space-y-4">
        
        {{-- SEARCH ROW --}}
        <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Search Category</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by category name or description..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
        </div>

        {{-- FILTERS ROW --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            
            {{-- SORTING FILTER --}}
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Sort By</label>
                <select name="sort"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Default (Newest First)</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A → Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z → A)</option>
                    <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>Oldest First</option>
                    <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Newest First</option>
                </select>
            </div>

            {{-- PRODUCTS COUNT FILTER (Optional) --}}
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Has Products</label>
                <select name="has_products"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">All Categories</option>
                    <option value="yes" {{ request('has_products') == 'yes' ? 'selected' : '' }}>With Products</option>
                    <option value="no" {{ request('has_products') == 'no' ? 'selected' : '' }}>Without Products</option>
                </select>
            </div>
        </div>

        {{-- ACTION BUTTONS - Di pojok kanan bawah --}}
        <div class="flex justify-end items-center pt-3 border-t border-gray-200">
            <div class="flex gap-2">
                @if(request()->anyFilled(['search', 'sort', 'has_products']))
                    <a href="{{ route('categories.index') }}"
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

    {{-- CATEGORIES TABLE CARD --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        
        {{-- TABLE HEADER --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Category List</h2>
            <span class="text-sm font-medium text-gray-700">
                {{ $categories->count() }} {{ Str::plural('category', $categories->count()) }}
            </span>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Image
            </th>
        </div>

        {{-- TABLE --}}
        @if($categories->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        
                        {{-- NAME --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-md bg-indigo-100 text-indigo-800">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">
                                        {{ $category->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ID: {{ $category->id }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- DESCRIPTION --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs">
                                @if($category->description)
                                    {{ Str::limit($category->description, 100) }}
                                @else
                                    <span class="text-gray-400 italic">No description</span>
                                @endif
                            </div>
                        </td>

                        {{-- CREATED AT --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $category->created_at->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $category->created_at->diffForHumans() }}
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->image_path)
                                <div class="h-10 w-10 rounded-md overflow-hidden border border-gray-200">
                                    <img src="{{ $category->image_path }}" 
                                    alt="{{ $category->name }}"
                                    class="card-img-top"
                                    style="height: 200px; object-fit: cover;">
                                </div>
                            @else
                                <div class="h-10 w-10 rounded-md bg-gray-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </td>

                        {{-- ACTIONS --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-3">
                                
                                {{-- VIEW --}}
                                @can('view', $category)
                                <a href="{{ route('categories.show', $category) }}"
                                   class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                   title="View details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @endcan

                                {{-- EDIT --}}
                                @can('update', $category)
                                <a href="{{ route('categories.edit', $category) }}"
                                   class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                   title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endcan

                                {{-- DELETE --}}
                                @can('delete', $category)
                                <form method="POST" 
                                      action="{{ route('categories.destroy', $category) }}"
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete the category \"{{ $category->name }}\"? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900 transition-colors focus:outline-none"
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
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No categories found</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                @if(request()->anyFilled(['search', 'sort']))
                    Try adjusting your search terms or filters
                @else
                    Get started by creating your first product category
                @endif
            </p>
            @can('create', App\Models\Category::class)
            <a href="{{ route('categories.create') }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Category
            </a>
            @endcan
        </div>
        @endif

        {{-- TABLE FOOTER --}}
        @if($categories->count() > 0)
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Optional: Add some interactivity
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus search input
    const searchInput = document.getElementById('search');
    if (searchInput && !searchInput.value) {
        setTimeout(() => searchInput.focus(), 100);
    }
    
    // Show confirmation for delete
    const deleteForms = document.querySelectorAll('form[onsubmit*="confirm"]');
    deleteForms.forEach(form => {
        form.onsubmit = function(e) {
            const categoryName = this.querySelector('button[title="Delete"]')?.dataset?.name || 
                               this.getAttribute('data-category-name') || 
                               'this category';
            return confirm(`Are you sure you want to delete ${categoryName}? This action cannot be undone.`);
        };
    });
});
</script>
@endpush