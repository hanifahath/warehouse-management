@extends('layouts.app')

@section('content')
<div class="px-6 py-6 max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>

        <a href="{{ route('admin.products.show', $product) }}"
           class="text-indigo-600 hover:underline text-sm">
            Back to Detail
        </a>
    </div>

    {{-- FORM CARD --}}
    <div class="bg-white border border-gray-200 shadow rounded-lg p-6">

        <form action="{{ route('admin.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- NAME --}}
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Product Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                           class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- SKU --}}
                <div>
                    <label class="block text-sm text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                           class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                    @error('sku') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- CATEGORY --}}
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Category</label>
                    <select name="category_id"
                        class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- STOCK --}}
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Stock</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                           class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                    @error('stock') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- MIN STOCK --}}
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Minimum Stock</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}"
                           class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                    @error('min_stock') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- PURCHASE PRICE --}}
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Purchase Price</label>
                    <input type="number" name="purchase_price"
                           value="{{ old('purchase_price', $product->purchase_price) }}"
                           class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                    @error('purchase_price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- SELLING PRICE --}}
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Selling Price</label>
                    <input type="number" name="selling_price"
                           value="{{ old('selling_price', $product->selling_price) }}"
                           class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                    @error('selling_price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- LOCATION --}}
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Rack Location</label>
                    <input type="text" name="location" value="{{ old('location', $product->location) }}"
                           class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                    @error('location') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- DESCRIPTION - FULL WIDTH --}}
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="5"
                              class="w-full border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- SUBMIT --}}
            <div class="flex justify-end mt-8">
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Save Changes
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
