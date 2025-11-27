<!DOCTYPE html>
<html>
<head>
    <title>Daftar Restock Order (PO)</title>
</head>
<body>
    <h2>Daftar Restock Order</h2>
    
    @if (in_array(auth()->user()->role, ['Admin', 'Manager']))
        <p><a href="{{ route('restocks.create') }}">Buat PO Baru</a></p>
    @endif

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>No. PO</th>
                <th>Supplier</th>
                <th>Tgl Order</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->po_number }}</td>
                    <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td style="color: 
                        @if($order->status == 'Pending') orange
                        @elseif($order->status == 'Received') green
                        @else blue
                        @endif;">
                        <b>{{ $order->status }}</b>
                    </td>
                    <td>
                        {{-- Aksi Supplier: Konfirmasi --}}
                        @if (auth()->user()->role === 'Supplier' && $order->supplier_id === auth()->id() && $order->status === 'Pending')
                            <form action="{{ route('restocks.confirm', $order) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" onclick="return confirm('Konfirmasi order ini?')" style="color:purple;">Konfirmasi PO</button>
                            </form>
                        
                        {{-- Aksi Manager/Staff: Terima Barang --}}
                        @elseif (in_array(auth()->user()->role, ['Admin', 'Manager', 'Staff']) && $order->status !== 'Received' && $order->status !== 'Pending')
                            <form action="{{ route('restocks.receive', $order) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" onclick="return confirm('TERIMA BARANG? Stok akan diupdate instan.')" style="color:green;">TERIMA BARANG</button>
                            </form>
                        @else
                            Lihat Detail
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    {{ $orders->links() }}
</body>
</html>