<!DOCTYPE html>

<html>
<head>
<title>Daftar Restock Order (PO)</title>
<!-- Tambahkan sedikit style minimal untuk readability -->
    <style>
    body { font-family: sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { text-align: left; padding: 8px; }
    th { background-color: #f2f2f2; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .btn-confirm {
    background-color: #9333ea;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
    }

    .btn-confirm:hover { background-color: #7e22ce; }

    .btn-receive { 
        background-color: #10b981; 
        color: white; 
        padding: 5px 10px; 
        border: none; 
        border-radius: 4px; 
        cursor: pointer; 
        transition: background-color 0.2s;
    }
    .btn-receive:hover { background-color: #059669; }

    .btn-detail {
        background-color: #3b82f6; 
        color: white; 
        padding: 5px 10px; 
        border: none; 
        border-radius: 4px; 
        cursor: pointer; 
        text-decoration: none; 
        display: inline-block;
        transition: background-color 0.2s;
    }
    .btn-detail:hover { background-color: #2563eb; }

</style>


</head>
<body>
<h2>Daftar Restock Order (PO)</h2>

@if (in_array(auth()->user()->role, ['Admin', 'Manager']))
    <p><a href="{{ route('restocks.create') }}" class="btn-detail" style="background-color: #f97316;">Buat PO Baru</a></p>
@endif

@if (session('success'))
    <div class="success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="error">{{ session('error') }}</div>
@endif

{{-- Notifikasi Error Validasi (PENTING) --}}
@if ($errors->any())
    <div class="error" style="background-color: #fdd; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
        <strong>Kesalahan Validasi:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
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
                    @elseif($order->status == 'Approved') blue
                    @elseif($order->status == 'Received') green
                    @else red
                    @endif;">
                    <b>{{ $order->status }}</b>
                </td>
                <td>
                    {{-- Aksi Supplier: Konfirmasi --}}
                    {{-- HANYA Supplier yang bersangkutan dan status 'Pending' yang bisa melihat tombol ini --}}
                    @if (auth()->user()->role === 'Supplier' && $order->supplier_id === auth()->id() && $order->status === 'Pending')
                        <form action="{{ route('restocks.confirm', $order) }}" method="POST" style="display:inline;">
                            @csrf
                            {{-- Menggunakan PATCH untuk update parsial (perubahan status) --}}
                            @method('PATCH') 
                            <button type="submit" onclick="return confirm('Konfirmasi order ini?')" class="btn-confirm">Konfirmasi PO</button>
                        </form>
                    
                    {{-- Aksi Manager/Staff: Terima Barang --}}
                    {{-- Staff/Admin/Manager dapat menerima barang jika statusnya 'Approved' --}}
                    @elseif (in_array(auth()->user()->role, ['Admin', 'Manager', 'Staff']) && $order->status === 'Approved')
                        <form action="{{ route('restocks.receive', $order) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH') {{-- HARUS ADA DI SINI --}}
                            <button type="submit" onclick="return confirm('TERIMA BARANG? Stok akan diupdate instan.')" class="btn-receive">TERIMA BARANG</button>
                        </form>
                    
                    {{-- Default: Lihat Detail --}}
                    @else
                        <a href="{{ route('restocks.show', $order) }}" class="btn-detail">Lihat Detail</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $orders->links() }}


</body>
</html>