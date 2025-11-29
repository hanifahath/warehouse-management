@extends('layouts.app')

@section('title', 'Create Product')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Create Product</h1>

    @include('shared.form-errors')

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        <x-warehouse.card>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Product Name</label>
                    <input type="text" name="name" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>SKU</label>
                    <input type="text" name="sku" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Category</label>
                    <select name="category_id" class="w-full border rounded px-2 py-1" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Purchase Price</label>
                    <input type="number" name="purchase_price" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Selling Price</label>
                    <input type="number" name="selling_price" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Minimum Stock</label>
                    <input type="number" name="min_stock" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Current Stock</label>
                    <input type="number" name="stock" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Unit</label>
                    <input type="text" name="unit" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Location</label>
                    <input type="text" name="location" class="w-full border rounded px-2 py-1">
                </div>
                <div>
                    <label>Image</label>
                    <input type="file" name="image" class="w-full border rounded px-2 py-1">
                </div>
                <div class="col-span-2">
                    <label>Description</label>
                    <textarea name="description" class="w-full border rounded px-2 py-1"></textarea>
                </div>
            </div>
            <div class="mt-4">
                <x-warehouse.button type="primary">Save Product</x-warehouse.button>
            </div>
        </x-warehouse.card>
    </form>
@endsection