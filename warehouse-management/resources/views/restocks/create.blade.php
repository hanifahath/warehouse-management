<!DOCTYPE html>
<html>
<head>
    <title>Buat Purchase Order (PO)</title>
</head>
<body>
    <h2>Buat Purchase Order Baru (Role: Manager)</h2>
    <a href="{{ route('restock.index') }}">Kembali ke Daftar PO</a>
    
    @if ($errors->any())
        <div style="color: red;">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <form action="{{ route('restock.store') }}" method="POST">
        @csrf
        
        <label for="supplier_id">Supplier:</label><br>
        {{-- Asumsi variabel $suppliers tersedia dari controller --}}
        <select id="supplier_id" name="supplier_id" required>
            <option value="">-- Pilih Supplier --</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach
        </select><br><br>

        <label for="order_date">Tanggal Order:</label><br>
        <input type="date" id="order_date" name="order_date" value="{{ now()->toDateString() }}" required><br><br>

        <label for="expected_delivery_date">Estimasi Kirim:</label><br>
        <input type="date" id="expected_delivery_date" name="expected_delivery_date"><br><br>

        <label for="notes">Catatan:</label><br>
        <textarea id="notes" name="notes">{{ old('notes') }}</textarea><br><br>

        <h3>Item yang Dipesan (Hanya 1 Item untuk Uji Coba)</h3>
        
        <label for="items[0][product_id]">Produk:</label><br>
        {{-- Asumsi variabel $products tersedia dari controller --}}
        <select name="items[0][product_id]" required>
            <option value="">-- Pilih Produk --</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}">[{{ $product->sku }}] {{ $product->name }}</option>
            @endforeach
        </select><br><br>

        <label for="items[0][quantity]">Kuantitas Pesanan:</label><br>
        <input type="number" name="items[0][quantity]" value="10" min="1" required><br><br>
        
        <button type="submit">Buat Purchase Order (Pending)</button>
    </form>
</body>
</html>