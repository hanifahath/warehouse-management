<!DOCTYPE html>
<html>
<head>
    <title>Buat Transaksi Masuk</title>
</head>
<body>
    <h2>Buat Transaksi Masuk (Restock/Retur)</h2>
    <a href="{{ route('transactions.index') }}">Kembali ke Daftar Transaksi</a>
    
    @if ($errors->any())
        <div style="color: red;">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <form action="{{ route('transactions.store_incoming') }}" method="POST">
        @csrf
        
        <label for="supplier_id">Supplier:</label><br>
        <select id="supplier_id" name="supplier_id" required>
            <option value="">-- Pilih Supplier --</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach
        </select><br><br>

        <label for="date">Tanggal Transaksi:</label><br>
        <input type="date" id="date" name="date" value="{{ now()->toDateString() }}" required><br><br>

        <label for="notes">Catatan:</label><br>
        <textarea id="notes" name="notes">{{ old('notes') }}</textarea><br><br>

        <h3>Detail Item (Hanya untuk 1 Item saat Cek Fungsionalitas)</h3>
        
        <label for="items[0][product_id]">Produk:</label><br>
        <select name="items[0][product_id]" required>
            <option value="">-- Pilih Produk --</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}">[{{ $product->sku }}] {{ $product->name }}</option>
            @endforeach
        </select><br><br>

        <label for="items[0][quantity]">Kuantitas Masuk:</label><br>
        <input type="number" name="items[0][quantity]" value="1" min="1" required><br><br>

        <label for="items[0][price_at_transaction]">Harga Beli Satuan:</label><br>
        <input type="number" name="items[0][price_at_transaction]" value="0" required><br><br>
        
        <button type="submit">Buat Transaksi Masuk (Pending)</button>
    </form>
</body>
</html>