@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Categories</h1>

    <x-warehouse.card>
        <div class="mb-4">
            <a href="{{ route('admin.categories.create') }}">
                <x-warehouse.button type="primary">Create New Category</x-warehouse.button>
            </a>
        </div>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Name</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr class="border-b">
                        <td class="p-2">{{ $category->name }}</td>
                        <td class="p-2 flex space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-yellow-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline"
                                        onclick="return confirm('Delete this category?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @include('shared.pagination', ['paginator' => $categories])
    </x-warehouse.card>
@endsection