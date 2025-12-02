@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900">Buat Produk Baru</h1>
        <a href="{{ route('admin.products.index') }}" 
           class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition duration-150 ease-in-out">
            ← Kembali ke Daftar Produk
        </a>
    </div>

    {{-- PENTING: Tambahkan enctype="multipart/form-data" untuk upload file (image) --}}
    <div class="bg-white shadow-xl rounded-xl p-8 border border-gray-200">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- Bagian 1: Informasi Dasar (Nama, SKU, Kategori, Satuan) --}}
            <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">Informasi Produk</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Product Name (Wajib) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1 required">Nama Produk</label>
                    <input type="text" name="name" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SKU (Wajib) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1 required">SKU (Kode Produk Unik)</label>
                    <input type="text" name="sku" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('sku') }}">
                    @error('sku')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Category ID (Wajib) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1 required">Kategori</label>
                    {{-- Pastikan variabel $categories dikirim dari controller --}}
                    <select name="category_id" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Unit (Wajib) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1 required">Satuan (Contoh: Pcs, Kg, Box)</label>
                    <input type="text" name="unit" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('unit') }}">
                    @error('unit')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian 2: Harga (Purchase & Selling) --}}
            <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 pt-4">Informasi Harga</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Purchase Price (Wajib) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1 required">Harga Beli (Modal)</label>
                    <input type="number" name="purchase_price" step="0.01" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('purchase_price') }}">
                    @error('purchase_price')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Selling Price (Wajib) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1 required">Harga Jual</label>
                    <input type="number" name="selling_price" step="0.01" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('selling_price') }}">
                    @error('selling_price')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian 3: Stok & Lokasi --}}
            <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 pt-4">Stok dan Lokasi</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Stock (Wajib) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1 required">Stok Awal</label>
                    <input type="number" name="current_stock" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('current_stock') }}">
                    @error('current_stock')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Minimum Stock (Wajib) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1 required">Stok Minimum (Alert)</label>
                    <input type="number" name="min_stock" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('min_stock') }}">
                    @error('min_stock')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Rack Location (Opsional) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Lokasi Rak (Opsional)</label>
                    <input type="text" name="rack_location"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('rack_location') }}">
                    @error('rack_location')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian 4: Deskripsi & Gambar --}}
            <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 pt-4">Detail Lainnya</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Description (Opsional) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Deskripsi Produk (Opsional)</label>
                    <textarea name="description" rows="4"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Image (Opsional) --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Gambar Produk (Opsional)</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-gray-700 border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WEBP. Maks: 2MB.</p>
                    @error('image')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="pt-6 border-t border-gray-200 flex justify-end">
                <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out transform hover:scale-[1.01]">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
{{-- Style untuk penanda wajib (optional, jika Anda ingin menggunakan class 'required') --}}
<style>
    .required:after {
        content: " *";
        color: #ef4444; /* Red-600 */
    }
</style>
@endsection