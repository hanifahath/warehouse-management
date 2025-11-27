<!DOCTYPE html>
<html>
<head>
    <title>Detail Transaksi {{ $transaction->transaction_number }}</title>
</head>
<body>
    <h2>Detail Transaksi</h2>
    <a href="{{ route('transactions.index') }}">Kembali ke Daftar Transaksi</a>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif
    
    <table border="1" cellpadding="10" cellspacing="0">
        <tr><th>No. Transaksi</th><td>{{ $transaction->transaction_number }}</td></tr>
        <tr><th>Tipe</th><td>{{ $transaction->type }}</td></tr>
        <tr><th>Status</th><td style="color: {{ $transaction->status == 'Pending' ? 'orange' : 'green' }};"><b>{{ $transaction->status }}</b></td></tr>
        <tr><th>Tanggal</th><td>{{ $transaction->date }}</td></tr>
        <tr><th>Dibuat Oleh</th><td>{{ $transaction->creator->name ?? 'N/A' }}</td></tr>
        
        @if ($transaction->approved_by)
            <tr><th>Disetujui Oleh</th><td>{{ $transaction->approver->name ?? 'N/A' }}</td></tr>
        @endif
        
        @if ($transaction->type === 'Incoming')
            <tr><th>Supplier</th><td>{{ $transaction->supplier->name ?? 'N/A' }}</td></tr>
        @else
            <tr><th>Pelanggan</th><td>{{ $transaction->customer_name }}</td></tr>
        @endif
        <tr><th>Catatan</th><td>{{ $transaction->notes }}</td></tr>
    </table>

    <h3>Item Transaksi</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Kuantitas</th>
                <th>Harga Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->price_at_transaction) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <br>
    
    {{-- Tombol Approval (Hanya untuk Manager dan Status Pending) --}}
    @if ($transaction->status === 'Pending' && in_array(auth()->user()->role, ['Admin', 'Manager']))
        <form action="{{ route('transactions.approve', $transaction) }}" method="POST">
            @csrf
            <button type="submit" onclick="return confirm('SETUJUI TRANSAKSI INI? Stok akan diupdate.')" 
                    style="background-color: blue; color: white; padding: 10px;">
                SETUJUI TRANSAKSI
            </button>
        </form>
    @endif
</body>
</html>