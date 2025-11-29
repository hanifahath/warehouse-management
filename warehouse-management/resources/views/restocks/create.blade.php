@extends('layouts.app')

@section('title', 'Buat Pesanan Restock Baru')

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-6 border-b pb-3">Buat Pesanan Restock Baru</h1>

        {{-- TAMPILKAN ERROR VALIDASI UMUM (JIKA ADA) --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error Validasi!</strong>
                <span class="block sm:inline">Terdapat kesalahan pada input. Silakan periksa setiap field, terutama Item Restock.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('restocks.store') }}" id="restock-form" class="space-y-8">
            @csrf
            
            {{-- Order Details Card --}}
            <div class="bg-white shadow-xl rounded-xl p-6">
                <h2 class="text-xl font-bold text-indigo-700 mb-4 border-b pb-2">Detail Pesanan Utama</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    {{-- Supplier ID --}}
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                        <select id="supplier_id" name="supplier_id" 
                                class="w-full border-gray-300 rounded-lg shadow-sm p-2.5 transition duration-150 focus:ring-indigo-500 focus:border-indigo-500 @error('supplier_id') border-red-500 @enderror" 
                                required>
                            <option value="" disabled {{ old('supplier_id') == null ? 'selected' : '' }}>Pilih Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }} ({{ $supplier->role }})
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- Order Date --}}
                    <div>
                        <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pesanan <span class="text-red-500">*</span></label>
                        <input type="date" id="order_date" name="order_date" 
                               value="{{ old('order_date', date('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm p-2.5 transition duration-150 focus:ring-indigo-500 focus:border-indigo-500 @error('order_date') border-red-500 @enderror" required>
                        @error('order_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- Expected Delivery Date --}}
                    <div>
                        <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Perkiraan Tanggal Kirim</label>
                        <input type="date" id="expected_delivery_date" name="expected_delivery_date" 
                               value="{{ old('expected_delivery_date') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm p-2.5 transition duration-150 focus:ring-indigo-500 focus:border-indigo-500 @error('expected_delivery_date') border-red-500 @enderror">
                        @error('expected_delivery_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea id="notes" name="notes" rows="3" 
                              class="w-full border-gray-300 rounded-lg shadow-sm p-2.5 transition duration-150 focus:ring-indigo-500 focus:border-indigo-500 @error('notes') border-red-500 @enderror" 
                              placeholder="Masukkan catatan tambahan untuk pesanan ini...">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            {{-- Restock Items Card --}}
            <div class="bg-white shadow-xl rounded-xl p-6">
                <h2 class="text-xl font-bold text-indigo-700 mb-4 border-b pb-2">Item Restock <span class="text-red-500">*</span></h2>
                
                {{-- Header Bar for Items --}}
                <div class="grid grid-cols-10 md:grid-cols-12 gap-4 font-bold text-gray-600 text-sm pb-2 mb-2 border-b">
                    <div class="col-span-4">Produk</div>
                    <div class="col-span-3">Kuantitas <span class="text-red-500">*</span></div>
                    <div class="col-span-3">Harga Satuan (Rp) <span class="text-red-500">*</span></div>
                    <div class="col-span-2 text-center">Hapus</div>
                </div>
                
                {{-- Container untuk Item Restock --}}
                <div id="restock-items" class="space-y-4">
                    
                    {{-- TAMPILKAN BARIS YANG DIISI ULANG SETELAH VALIDASI GAGAL --}}
                    @php $item_index = 0; @endphp
                    @foreach (old('items', []) as $key => $item)
                        <div class="grid grid-cols-10 md:grid-cols-12 gap-4 items-center item-row border-b pb-4" id="item-row-{{ $item_index }}">
                            
                            {{-- Product ID (Wajib) --}}
                            <div class="col-span-4">
                                <select name="items[{{ $item_index }}][product_id]" 
                                        class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500 item-product-id @error('items.'.$item_index.'.product_id') border-red-500 @enderror" 
                                        required>
                                    <option value="" disabled {{ !isset($item['product_id']) || $item['product_id'] == '' ? 'selected' : '' }}>Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ ($item['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('items.'.$item_index.'.product_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Quantity (Wajib, Min: 1) --}}
                            <div class="col-span-3">
                                <input type="number" name="items[{{ $item_index }}][quantity]" 
                                       value="{{ $item['quantity'] ?? '' }}"
                                       placeholder="Min: 1" min="1" 
                                       class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500 item-quantity @error('items.'.$item_index.'.quantity') border-red-500 @enderror" 
                                       required>
                                @error('items.'.$item_index.'.quantity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Unit Price (Wajib, Min: 0) --}}
                            <div class="col-span-3">
                                <input type="number" name="items[{{ $item_index }}][unit_price]" 
                                       value="{{ $item['unit_price'] ?? '' }}"
                                       placeholder="0.00" step="0.01" min="0" 
                                       class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500 @error('items.'.$item_index.'.unit_price') border-red-500 @enderror" 
                                       required>
                                @error('items.'.$item_index.'.unit_price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Remove Button --}}
                            <div class="col-span-2 flex justify-center">
                                <button type="button" class="text-red-500 hover:text-red-700 remove-item-btn p-2 rounded-full hover:bg-red-50 transition duration-150" title="Hapus Item">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.86 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3"></path></svg>
                                </button>
                            </div>
                        </div>
                        @php $item_index++; @endphp
                    @endforeach
                    
                    {{-- Item Row Template (Disembunyikan, untuk kloning JS) --}}
                    <div id="item-template" class="grid grid-cols-10 md:grid-cols-12 gap-4 items-center hidden item-row border-b pb-4">
                        {{-- Product ID (Wajib) --}}
                        <div class="col-span-4">
                            {{-- TAMBAHKAN 'disabled' --}}
                            <select disabled class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500 item-product-id" required>
                                <option value="" disabled selected>Pilih Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Quantity (Wajib, Min: 1) --}}
                        <div class="col-span-3">
                            {{-- TAMBAHKAN 'disabled' --}}
                            <input disabled type="number" placeholder="Min: 1" min="1" 
                                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500 item-quantity" required>
                        </div>
                        {{-- Unit Price (Wajib, Min: 0) --}}
                        <div class="col-span-3">
                            {{-- TAMBAHKAN 'disabled' --}}
                            <input disabled type="number" placeholder="0.00" step="0.01" min="0" 
                                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        {{-- Remove Button --}}
                        <div class="col-span-2 flex justify-center">
                            <button type="button" class="text-red-500 hover:text-red-700 remove-item-btn p-2 rounded-full hover:bg-red-50 transition duration-150" title="Hapus Item">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.86 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="item-error-feedback" class="text-sm text-red-500 mt-4 hidden">
                    Minimal harus ada satu item produk yang valid untuk dipesan.
                </div>

                {{-- Tombol Tambah Item --}}
                <div class="mt-6">
                    <button type="button" id="add-item-btn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Tambah Item
                    </button>
                </div>

                {{-- Tampilkan error validasi array items secara keseluruhan --}}
                @error('items') 
                    <p class="text-sm text-red-500 mt-4 p-2 bg-red-100 border border-red-300 rounded-lg">
                        {{ $message }}
                    </p> 
                @enderror

                {{-- Tombol Submit --}}
                <div class="mt-8 pt-6 border-t">
                    <button type="submit" class="w-full md:w-auto px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg transition duration-150 ease-in-out">
                        Simpan Pesanan Restock
                    </button>
                </div>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('restock-form');
                const itemContainer = document.getElementById('restock-items');
                const addItemButton = document.getElementById('add-item-btn');
                const itemTemplate = document.getElementById('item-template');
                const itemErrorFeedback = document.getElementById('item-error-feedback');
                
                // Ambil index terakhir dari item yang sudah dimuat (jika ada data 'old')
                let itemIndex = {{ old('items') ? count(old('items')) : 0 }};

                /**
                 * Fungsi untuk menginisialisasi baris item.
                 */
                function updateItemRow(row, index, data = null) {
                    // Hapus semua pesan error yang mungkin ada di template
                    row.querySelectorAll('p.text-xs.text-red-500.mt-1').forEach(p => p.remove());

                    row.id = `item-row-${index}`;
                    
                    row.querySelectorAll('select, input').forEach(input => {
                        
                        // HAPUS ATRIBUT DISABLED (Penting agar data bisa dikirim)
                        input.removeAttribute('disabled');
                        
                        // Tambahkan atribut name dengan index yang benar
                        if (input.tagName === 'SELECT') {
                           input.name = `items[${index}][product_id]`;
                        } else if (input.classList.contains('item-quantity')) {
                           input.name = `items[${index}][quantity]`;
                        } else {
                           input.name = `items[${index}][unit_price]`;
                        }

                        input.classList.remove('border-red-500'); // Hapus border error
                        
                        // Khusus untuk elemen baru (non-old), reset value
                        if (!data) {
                           input.value = '';
                           if (input.tagName === 'SELECT') {
                               input.selectedIndex = 0;
                           }
                        }
                    });
                    
                    // Tambahkan event listener untuk tombol hapus
                    const removeButton = row.querySelector('.remove-item-btn');
                    removeButton.onclick = function() {
                        // LOGIKA PENCEGAHAN: Memastikan minimal 1 baris item tetap ada.
                        if (itemContainer.querySelectorAll('.item-row:not(#item-template)').length > 1) { 
                            row.remove();
                        } else {
                            // Feedback visual bahwa baris tidak bisa dihapus
                            removeButton.classList.add('animate-pulse', 'ring-2', 'ring-red-500');
                            setTimeout(() => {
                                removeButton.classList.remove('animate-pulse', 'ring-2', 'ring-red-500');
                            }, 500);
                        }
                    };
                    
                    // Hanya tambahkan baris ke container jika ini adalah baris baru (bukan baris yang dimuat oleh Blade)
                    if (!itemContainer.contains(row)) {
                        itemContainer.appendChild(row);
                    }
                }

                // --- Inisialisasi Awal ---
                
                // Jika tidak ada item lama atau error, inisialisasi satu baris kosong
                if (itemIndex === 0) {
                     // Kita harus menggunakan index 0 jika kontainer kosong
                     const newRow = itemTemplate.cloneNode(true);
                     newRow.classList.remove('hidden', 'border-b', 'pb-4');
                     updateItemRow(newRow, 0); // Panggil dengan index 0
                     itemIndex = 1; // Set index selanjutnya ke 1
                } else {
                    // Jika ada data old, pastikan event listener hapus terpasang
                    itemContainer.querySelectorAll('.item-row:not(#item-template)').forEach(row => {
                        const removeButton = row.querySelector('.remove-item-btn');
                        if (removeButton) {
                            removeButton.onclick = function() {
                                if (itemContainer.querySelectorAll('.item-row:not(#item-template)').length > 1) { 
                                    row.remove();
                                } else {
                                    removeButton.classList.add('animate-pulse', 'ring-2', 'ring-red-500');
                                    setTimeout(() => {
                                        removeButton.classList.remove('animate-pulse', 'ring-2', 'ring-red-500');
                                    }, 500);
                                }
                            };
                        }
                    });
                }


                // --- Event Listener untuk Menambah Item Baru ---

                addItemButton.addEventListener('click', function() {
                    const newRow = itemTemplate.cloneNode(true);
                    newRow.classList.remove('hidden', 'border-b', 'pb-4'); 
                    updateItemRow(newRow, itemIndex); 
                    itemIndex++; // Naikkan index global
                });

                // --- Event Listener untuk Pengecekan Klien Saat Submit ---
                
                form.addEventListener('submit', function(e) {
                    // Validasi Kustom JavaScript akan dilakukan, jadi matikan validasi browser default
                    // Ini memastikan logic required dari HTML tidak bentrok dengan logic JS/Laravel
                    // Hentikan default submit untuk melakukan validasi manual
                    e.preventDefault(); 
                    
                    itemErrorFeedback.classList.add('hidden'); 
                    const currentItems = itemContainer.querySelectorAll('.item-row:not(#item-template)');

                    // Cek minimal 1 item
                    if (currentItems.length === 0) {
                        itemErrorFeedback.textContent = "Minimal harus ada satu item produk untuk dipesan.";
                        itemErrorFeedback.classList.remove('hidden');
                        return;
                    }
                    
                    let allValid = true;
                    const selectedProductIds = [];
                    
                    currentItems.forEach(row => {
                        const productSelect = row.querySelector('.item-product-id');
                        const quantityInput = row.querySelector('.item-quantity');
                        const priceInput = row.querySelector('input[name*="unit_price"]');
                        
                        let rowValid = true;

                        // Reset border
                        productSelect.classList.remove('border-red-500', 'ring-red-500');
                        quantityInput.classList.remove('border-red-500', 'ring-red-500');
                        priceInput.classList.remove('border-red-500', 'ring-red-500');

                        // Validasi Produk
                        if (productSelect.value === "") {
                            rowValid = false;
                            productSelect.classList.add('border-red-500', 'ring-red-500');
                        } else {
                            // Cek Duplikasi
                            if (selectedProductIds.includes(productSelect.value)) {
                                rowValid = false;
                                productSelect.classList.add('border-red-500', 'ring-red-500');
                                if (itemErrorFeedback.classList.contains('hidden')) {
                                     itemErrorFeedback.textContent = "Terdapat duplikasi produk. Harap pilih produk yang berbeda untuk setiap baris.";
                                     itemErrorFeedback.classList.remove('hidden');
                                }
                            }
                            selectedProductIds.push(productSelect.value);
                        }
                        
                        // Validasi Kuantitas
                        const quantity = parseFloat(quantityInput.value);
                        if (isNaN(quantity) || quantity <= 0) {
                            rowValid = false;
                            quantityInput.classList.add('border-red-500', 'ring-red-500');
                        }
                        
                        // Validasi Harga Satuan
                        const price = parseFloat(priceInput.value);
                         if (isNaN(price) || price < 0) {
                            rowValid = false;
                            priceInput.classList.add('border-red-500', 'ring-red-500');
                        }


                        if (!rowValid) {
                            allValid = false;
                        }
                    });

                    if (allValid) {
                        // Jika semua validasi kustom berhasil, kirim formulir secara manual
                        form.submit();
                    } else {
                         // Jika ada yang tidak valid dan pesan duplikasi belum muncul, tampilkan pesan umum
                        if (itemErrorFeedback.classList.contains('hidden')) {
                            itemErrorFeedback.textContent = "Semua baris item harus diisi dengan Produk, Kuantitas (>0), dan Harga Satuan (>=0) yang valid.";
                            itemErrorFeedback.classList.remove('hidden');
                        }
                        // Gulir ke atas untuk melihat error
                        itemErrorFeedback.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });

            });
        </script>
    </div>
@endsection