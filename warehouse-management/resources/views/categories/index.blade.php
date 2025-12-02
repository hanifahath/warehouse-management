@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-gray-900">Categories</h2>

    <a href="{{ route('admin.categories.create') }}"
        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        + Add Category
    </a>
</div>

<div class="bg-white border rounded shadow p-4">
    {{-- SEARCH + SORT --}}
    <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Search --}}
        <div>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search category name..."
                class="w-full border px-3 py-2 rounded" />
        </div>

        {{-- Sort --}}
        <div>
            <select name="sort" class="w-full border px-3 py-2 rounded">
                <option value="">Sort By</option>
                <option value="name_asc" {{ request('sort')=='name_asc' ? 'selected' : '' }}>Name (A → Z)</option>
                <option value="name_desc" {{ request('sort')=='name_desc' ? 'selected' : '' }}>Name (Z → A)</option>
                <option value="created_new" {{ request('sort')=='created_new' ? 'selected' : '' }}>Newest</option>
                <option value="created_old" {{ request('sort')=='created_old' ? 'selected' : '' }}>Oldest</option>
            </select>
        </div>

        {{-- Button --}}
        <div>
            <button class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Apply
            </button>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="overflow-auto">
        <table class="min-w-full text-left border">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 font-medium text-gray-900">Name</th>
                    <th class="px-4 py-2 font-medium text-gray-900">Description</th>
                    <th class="px-4 py-2 font-medium text-gray-900 w-32">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($categories as $category)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $category->name }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $category->description ?? '-' }}</td>

                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                                class="text-blue-600 hover:underline text-sm">
                                Edit
                            </a>

                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-6 text-gray-500">
                        No categories found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection
