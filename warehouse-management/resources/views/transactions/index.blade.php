<!DOCTYPE html>
<html>
<head>
    <title>Daftar Transaksi</title>
</head>
<body>
    <h2>Daftar Transaksi Gudang (Role: {{ auth()->user()->role }})</h2>
    
    <p>
        <a href="{{ route('transactions.create_incoming') }}">Buat Transaksi MASUK</a> | 
        <a href="{{ route('transactions.create_outgoing') }}">Buat Transaksi KELUAR</a>
    </p>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Tipe</th>
                <th>Status</th>
                <th>Dibuat Oleh</th>
                <th>Total Item</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $t)
                <tr>
                    <td>{{ $t->transaction_number }}</td>
                    <td>{{ $t->type }}</td>
                    <td style="color: {{ $t->status == 'Pending' ? 'orange' : 'green' }};">{{ $t->status }}</td>
                    <td>{{ $t->creator->name ?? 'N/A' }}</td>
                    <td>{{ $t->items->count() }}</td>
                    <td>
                        <a href="{{ route('transactions.show', $t) }}">Detail</a>
                        
                        @if ($t->status === 'Pending' && auth()->user()->role === 'Manager')
                            <form action="{{ route('transactions.approve', $t) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" onclick="return confirm('Setujui transaksi ini? Stok akan diupdate.')" style="color:blue;">SETUJUI</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>