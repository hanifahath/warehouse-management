<!DOCTYPE html>
<html>
<head>
    <title>Buat Purchase Order (PO)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f7f7; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        h2 { color: #1f2937; margin-bottom: 20px; }
        label { display: block; margin-top: 10px; font-weight: 600; color: #374151; }
        input[type="text"], input[type="number"], input[type="date"], textarea, select {
            width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; margin-top: 5px; box-sizing: border-box;
        }
        button {
            background-color: #10b981; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer;
            margin-top: 20px; font-weight: bold; transition: background-color 0.3s;
        }
        button:hover { background-color: #059669; }
        .error-message { color: #ef4444; background-color: #fee2e2; border: 1px solid #fca5a5; padding: 10px; border-radius: 8px; margin-bottom: 20px; }
        ul { margin-left: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Buat Purchase Order Baru (Role: Manager)</h2>
        
        <a href="{{ route('restocks.index') }}" class="text-blue-600 hover:text-blue-800 underline mb-4 inline-block">Kembali ke Daftar PO</a>
        
        @if ($errors->any())
            <div class="error-message">
                <p class="font-bold">Terjadi Kesalahan:</p>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif

        <form action="{{ route('restocks.store') }}" method="POST">
            @csrf
            
            <label for="supplier_id">Supplier:</label>
            {{-- Variabel $suppliers diasumsikan tersedia dari controller (Sesuai solusi error pertama) --}}
            <select id="supplier_id" name="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                @foreach ($suppliers as $supplier)
                    {{-- Menggunakan old() untuk mempertahankan pilihan setelah gagal validasi --}}
                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>

            <label for="order_date">Tanggal Order:</label>
            <input type="date" id="order_date" name="order_date" value="{{ old('order_date', now()->toDateString()) }}" required>

            <label for="expected_delivery_date">Estimasi Kirim:</label>
            <input type="date" id="expected_delivery_date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}">

            <label for="notes">Catatan:</label>
            <textarea id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>

            <h3 class="text-lg font-bold mt-6 mb-2 text-gray-700">Item yang Dipesan (Hanya 1 Item untuk Uji Coba)</h3>
            
            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                <label for="items[0][product_id]">Produk:</label>
                {{-- Variabel $products diasumsikan tersedia dari controller --}}
                <select name="items[0][product_id]" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ old('items.0.product_id') == $product->id ? 'selected' : '' }}>
                            [{{ $product->sku }}] {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                
                {{-- START: Field yang hilang, menyebabkan error unit_price is required --}}
                <label for="items[0][unit_price]">Harga Satuan (Rp):</label>
                <input type="number" name="items[0][unit_price]" value="{{ old('items.0.unit_price', 0) }}" min="0" step="100" required>
                {{-- END: Field yang hilang --}}

                <label for="items[0][quantity]">Kuantitas Pesanan:</label>
                <input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity', 10) }}" min="1" required>
            </div>

            <button type="submit">Buat Purchase Order (Pending)</button>
        </form>
    </div>
</body>
</html>