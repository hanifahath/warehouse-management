<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tambah Produk Baru
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                {{-- x-warehouse-alert diubah menjadi x-warehouse.alert --}}
                <x-warehouse.alert type="error" class="mb-6">
                    <p class="font-medium mb-2">Terdapat kesalahan pada form:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-warehouse.alert>
            @endif

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Kolom Kiri: Informasi Dasar --}}
                    {{-- x-warehouse-card diubah menjadi x-warehouse.card --}}
                    <x-warehouse.card title="Informasi Dasar">
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nama Produk <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    SKU (Kode Unik) <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="contoh: PRD-001">
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Kategori <span class="text-red-600">*</span>
                                </label>
                                <select name="category_id" id="category_id" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Kategori --</option>
                                    {{-- Loop categories jika ada --}}
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Deskripsi
                                </label>
                                <textarea name="description" id="description" rows="3"
                                          class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            </div>

                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Satuan <span class="text-red-600">*</span>
                                </label>
                                <select name="unit" id="unit" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs (Pieces)</option>
                                    <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>Box</option>
                                    <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kg (Kilogram)</option>
                                    <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                                    <option value="pack" {{ old('unit') == 'pack' ? 'selected' : '' }}>Pack</option>
                                </select>
                            </div>
                        </div>
                    </x-warehouse.card>

                    {{-- Kolom Kanan: Harga & Stok --}}
                    {{-- x-warehouse-card diubah menjadi x-warehouse.card --}}
                    <x-warehouse.card title="Harga & Stok">
                        <div class="space-y-4">
                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Harga Beli (Rp) <span class="text-red-600">*</span>
                                </label>
                                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" required min="0"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="selling_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Harga Jual (Rp) <span class="text-red-600">*</span>
                                </label>
                                <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}" required min="0"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="min_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Stok Minimum <span class="text-red-600">*</span>
                                </label>
                                <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', 10) }}" required min="0"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Alert akan muncul jika stok di bawah nilai ini
                                </p>
                            </div>

                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Stok Awal
                                </label>
                                <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Kosongkan atau isi 0 jika stok akan diisi via transaksi
                                </p>
                            </div>

                            <div>
                                <label for="warehouse_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Lokasi Rak Gudang
                                </label>
                                <input type="text" name="warehouse_location" id="warehouse_location" value="{{ old('warehouse_location') }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="contoh: A1-05">
                            </div>
                        </div>
                    </x-warehouse.card>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 flex items-center justify-end gap-4">
                    <a href="{{ route('products.index') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                        Batal
                    </a>
                    {{-- x-warehouse-button diubah menjadi x-warehouse.button --}}
                    <x-warehouse.button type="submit" variant="primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Produk
                    </x-warehouse.button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>