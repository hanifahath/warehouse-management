<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk</title>
</head>
<body>
    <h2>Tambah Produk Baru</h2>
    <a href="{{ route('products.index') }}">Kembali ke Daftar Produk</a>
    
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <label for="name">Nama:</label><br>
        <input type="text" id="name" name="name" value="{{ old('name') }}"><br><br>

        <label for="sku">SKU:</label><br>
        <input type="text" id="sku" name="sku" value="{{ old('sku') }}"><br><br>
        
        <label for="category_id">Kategori ID:</label><br>
        <input type="number" id="category_id" name="category_id" value="{{ old('category_id') }}"><br><br>

        <label for="purchase_price">Harga Beli:</label><br>
        <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}"><br><br>

        <label for="selling_price">Harga Jual:</label><br>
        <input type="number" id="selling_price" name="selling_price" value="{{ old('selling_price') }}"><br><br>
        
        <label for="unit">Satuan (pcs/pack):</label><br>
        <input type="text" id="unit" name="unit" value="{{ old('unit') }}"><br><br>

        <label for="min_stock">Min. Stok:</label><br>
        <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock') }}"><br><br>

        <button type="submit">Simpan Produk</button>
    </form>
</body>
</html>