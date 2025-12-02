@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-xl mx-auto">
    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Edit Category</h2>

    <div class="bg-white border rounded shadow p-6">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- NAME --}}
            <div class="mb-4">
                <label class="block font-medium text-gray-900 mb-1">
                    Category Name <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name" required
                    value="{{ old('name', $category->name) }}"
                    class="w-full border px-3 py-2 rounded focus:ring-indigo-500 focus:border-indigo-500" />
                @error('name')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- DESCRIPTION --}}
            <div class="mb-4">
                <label class="block font-medium text-gray-900 mb-1">
                    Description
                </label>
                <textarea name="description" rows="3"
                    class="w-full border px-3 py-2 rounded focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- BUTTONS --}}
            <div class="flex justify-between mt-6">
                <a href="{{ route('admin.categories.index') }}"
                    class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>

                <button type="submit"
                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
