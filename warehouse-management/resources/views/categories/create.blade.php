@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Create Category</h1>

    @include('shared.form-errors')

    <form method="POST" action="{{ route('categories.store') }}">
        @csrf
        <x-warehouse.card>
            <div>
                <label>Category Name</label>
                <input type="text" name="name" class="w-full border rounded px-2 py-1" required>
            </div>
            <div class="mt-4">
                <x-warehouse.button type="primary">Save Category</x-warehouse.button>
            </div>
        </x-warehouse.card>
    </form>
@endsection