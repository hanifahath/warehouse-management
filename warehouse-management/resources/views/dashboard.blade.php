@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- PAGE TITLE --}}
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

    {{-- ROLE SWITCH --}}
    @php
        $role = auth()->user()->role;
    @endphp

    {{-- ======================= ADMIN ======================= --}}
    @if($role === 'Admin')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            {{-- Total Produk --}}
            <x-warehouse.stat-card 
                title="Total Produk"
                value="{{ $stats['total_products'] ?? 0 }}"
            />

            {{-- Transaksi Bulan Ini --}}
            <x-warehouse.stat-card 
                title="Transaksi Bulan Ini"
                value="{{ $stats['monthly_transactions'] ?? 0 }}"
            />

            {{-- Nilai Inventori --}}
            <x-warehouse.stat-card 
                title="Nilai Inventori"
                value="Rp {{ number_format($stats['inventory_value'] ?? 0, 0, ',', '.') }}"
            />
        </div>

        {{-- Low Stock Alert --}}
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Produk Stok Rendah</h2>

            @if(empty($lowStock))
                <p class="text-gray-500 text-sm">Tidak ada produk dengan stok rendah.</p>
            @else
                <table class="w-full">
                    <thead class="text-left text-sm text-gray-500 border-b">
                        <tr>
                            <th class="pb-2">SKU</th>
                            <th class="pb-2">Nama</th>
                            <th class="pb-2">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($lowStock as $item)
                            <tr class="border-b last:border-0">
                                <td class="py-2">{{ $item->sku }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800">
                                        {{ $item->stock }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif



    {{-- ======================= MANAGER ======================= --}}
    @if($role === 'Manager')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <x-warehouse.stat-card title="Total Items" value="{{ $stats['total_items'] ?? 0 }}" />
            <x-warehouse.stat-card title="Low Stock Alerts" value="{{ $stats['low_stock_count'] ?? 0 }}" />
            <x-warehouse.stat-card title="Pending Approval" value="{{ $stats['pending_transactions'] ?? 0 }}" />
        </div>

        <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">Transaksi Pending</h2>

            @if(empty($pending))
                <p class="text-gray-500 text-sm">Tidak ada transaksi pending.</p>
            @else
                @foreach($pending as $tx)
                    <div class="border p-3 rounded-lg mb-2 bg-gray-50">
                        <div class="font-semibold">{{ $tx->type }} - #{{ $tx->id }}</div>
                        <div class="text-sm text-gray-500">{{ $tx->created_at->format('d M Y H:i') }}</div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-3">Restock Orders</h2>

            @foreach($restocks ?? [] as $order)
                <div class="p-3 border rounded-lg mb-2 bg-gray-50">
                    <div class="flex justify-between">
                        <span class="font-semibold">PO#{{ $order->id }}</span>
                        <span class="px-2 py-1 rounded text-sm 
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'in_transit') bg-blue-100 text-blue-800
                            @elseif($order->status == 'received') bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif



    {{-- ======================= STAFF ======================= --}}
    @if($role === 'Staff')
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Entry Transaksi</h2>

            <a href="{{ route('transactions.create_incoming') }}" 
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 mr-2">
                Barang Masuk
            </a>

            <a href="{{ route('transactions.create_outgoing') }}" 
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Barang Keluar
            </a>
        </div>

        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Transaksi Hari Ini</h2>

            @foreach($todayTransactions ?? [] as $tx)
                <div class="p-3 border rounded-lg mb-2 bg-gray-50">
                    <strong>{{ strtoupper($tx->type) }}</strong> — #{{ $tx->id }}
                    <div class="text-sm text-gray-500">{{ $tx->created_at->format('H:i') }}</div>
                </div>
            @endforeach
        </div>
    @endif



    {{-- ======================= SUPPLIER ======================= --}}
    @if($role === 'Supplier')
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">Order Perlu Konfirmasi</h2>

            @foreach($pendingOrders ?? [] as $order)
                <div class="p-3 border rounded-lg mb-2 bg-gray-50">
                    <strong>PO#{{ $order->id }}</strong>
                    <div class="text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</div>
                </div>
            @endforeach
        </div>

        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-3">Riwayat Pengiriman</h2>

            @foreach($history ?? [] as $h)
                <div class="p-3 border rounded-lg mb-2 bg-gray-50">
                    <strong>PO#{{ $h->id }}</strong> — {{ ucfirst($h->status) }}
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection