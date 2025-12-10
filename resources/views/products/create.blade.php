@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Tambah Produk Baru</h1>
        <a href="{{ route('products.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-lg transition duration-150">
            ← Kembali ke Daftar
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat {{ $errors->count() }} kesalahan dalam pengisian form:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- Bagian 1: Informasi Dasar --}}
            <div class="border-b border-gray-200 pb-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Informasi Produk
                </h2>
                <p class="text-sm text-gray-600 mt-1">Data identitas produk yang akan disimpan di gudang</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Produk --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                           placeholder="Contoh: Notebook A4 80gr">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SKU --}}
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                        SKU (Kode Unik) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="sku" name="sku" required
                           value="{{ old('sku') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                           placeholder="Contoh: PRD-001-2024">
                    <p class="mt-1 text-xs text-gray-500">SKU harus unik dan tidak boleh sama dengan produk lain</p>
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Satuan --}}
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">
                        Satuan <span class="text-red-500">*</span>
                    </label>
                    <select id="unit" name="unit" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                        <option value="">-- Pilih Satuan --</option>
                        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs (Buah)</option>
                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kg (Kilogram)</option>
                        <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>Box (Kotak)</option>
                        <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                        <option value="meter" {{ old('unit') == 'meter' ? 'selected' : '' }}>Meter</option>
                        <option value="pack" {{ old('unit') == 'pack' ? 'selected' : '' }}>Pack (Pak)</option>
                        <option value="dus" {{ old('unit') == 'dus' ? 'selected' : '' }}>Dus</option>
                        <option value="lainnya" {{ old('unit') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian 2: Harga --}}
            <div class="border-b border-gray-200 pb-4 pt-6">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informasi Harga
                </h2>
                <p class="text-sm text-gray-600 mt-1">Harga pembelian dan penjualan produk</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Harga Beli --}}
                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Harga Beli (Modal) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">Rp</span>
                        </div>
                        <input type="number" id="purchase_price" name="purchase_price" 
                               step="0.01" min="0" required
                               value="{{ old('purchase_price') }}"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                               placeholder="0">
                    </div>
                    @error('purchase_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga Jual --}}
                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Harga Jual <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">Rp</span>
                        </div>
                        <input type="number" id="selling_price" name="selling_price" 
                               step="0.01" min="0" required
                               value="{{ old('selling_price') }}"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                               placeholder="0">
                    </div>
                    @error('selling_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian 3: Stok & Lokasi --}}
            <div class="border-b border-gray-200 pb-4 pt-6">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Stok dan Lokasi
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kelola stok awal dan penempatan di gudang</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Stok Awal --}}
                <div>
                    <label for="current_stock" class="block text-sm font-medium text-gray-700 mb-1">
                        Stok Awal <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="current_stock" name="current_stock" 
                           min="0" required
                           value="{{ old('current_stock', 0) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                    @error('current_stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stok Minimum --}}
                <div>
                    <label for="min_stock" class="block text-sm font-medium text-gray-700 mb-1">
                        Stok Minimum (Alert) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="min_stock" name="min_stock" 
                           min="0" required
                           value="{{ old('min_stock', 5) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                    <p class="mt-1 text-xs text-gray-500">Sistem akan memberi peringatan jika stok ≤ nilai ini</p>
                    @error('min_stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lokasi Rak --}}
                <div>
                    <label for="rack_location" class="block text-sm font-medium text-gray-700 mb-1">
                        Lokasi Rak <span class="text-gray-500 text-xs">(Opsional)</span>
                    </label>
                    <input type="text" id="rack_location" name="rack_location"
                           value="{{ old('rack_location') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                           placeholder="Contoh: RAK-A-01">
                    <p class="mt-1 text-xs text-gray-500">Format: RAK-{Section}-{Nomor}</p>
                    @error('rack_location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian 4: Deskripsi & Gambar --}}
            <div class="border-b border-gray-200 pb-4 pt-6">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Detail Lainnya
                </h2>
                <p class="text-sm text-gray-600 mt-1">Informasi tambahan dan gambar produk</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Deskripsi --}}
                <div class="md:col-span-1">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi Produk <span class="text-gray-500 text-xs">(Opsional)</span>
                    </label>
                    <textarea id="description" name="description" rows="5"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                        placeholder="Deskripsi produk, spesifikasi, atau catatan tambahan...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gambar Produk --}}
                <div class="md:col-span-1">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        Gambar Produk <span class="text-gray-500 text-xs">(Opsional)</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition duration-150">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                    <span>Upload gambar</span>
                                    <input id="image" name="image" type="file" accept="image/*" class="sr-only">
                                </label>
                                <p class="pl-1">atau drag & drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP maks. 2MB</p>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    {{-- Preview Image --}}
                    <div id="imagePreview" class="mt-4 hidden">
                        <p class="text-sm text-gray-700 mb-2">Preview:</p>
                        <img id="previewImage" class="max-w-full h-48 object-cover rounded-lg border border-gray-200">
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="pt-6 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('products.index') }}" 
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-150">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition duration-150 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript untuk image preview --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Validation for selling price > purchase price
    const purchasePriceInput = document.getElementById('purchase_price');
    const sellingPriceInput = document.getElementById('selling_price');
    
    sellingPriceInput.addEventListener('blur', function() {
        const purchasePrice = parseFloat(purchasePriceInput.value) || 0;
        const sellingPrice = parseFloat(this.value) || 0;
        
        if (sellingPrice > 0 && purchasePrice > 0 && sellingPrice <= purchasePrice) {
            alert('⚠️ Harga jual harus lebih tinggi dari harga beli!');
            this.focus();
        }
    });
    
    // Validation for min stock
    const currentStockInput = document.getElementById('current_stock');
    const minStockInput = document.getElementById('min_stock');
    
    currentStockInput.addEventListener('blur', function() {
        const currentStock = parseInt(this.value) || 0;
        const minStock = parseInt(minStockInput.value) || 0;
        
        if (currentStock > 0 && currentStock <= minStock) {
            alert('⚠️ Stok awal sebaiknya lebih tinggi dari stok minimum untuk menghindari alert langsung!');
        }
    });
});
</script>
@endsection
@endsection