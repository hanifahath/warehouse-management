@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="px-6 py-6">

    {{-- ROLE SWITCH --}}
    @php
        $role = strtolower(auth()->user()->role ?? 'guest');
    @endphp

    {{-- ======================= ADMIN DASHBOARD ======================= --}}
    @if($role === 'admin')
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}</p>
        </div>

        {{-- STATISTIK UTAMA --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            {{-- Total Produk --}}
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Produk</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-full">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs text-indigo-600 hover:underline mt-2 block">View Products →</a>
            </div>

            {{-- Transaksi Bulan Ini --}}
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Transaksi Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['monthly_transactions'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Nilai Inventori --}}
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Nilai Inventori</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['inventory_value'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Pending Suppliers --}}
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pending Suppliers</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_suppliers'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
                <a href="{{ route('users.index') }}?role=supplier&status=pending" class="text-xs text-red-600 hover:underline mt-2 block">Review Now →</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- LOW STOCK ALERTS --}}
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">Produk Stok Rendah</h2>
                    <p class="text-sm text-gray-600 mt-1">Segera lakukan restock</p>
                </div>
                <div class="p-6">
                    @if($lowStock->isEmpty())
                        <p class="text-center py-4 text-gray-500">Tidak ada produk low stock</p>
                    @else
                        <div class="space-y-3">
                            @foreach($lowStock as $product)
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div>
                                        <p class="font-medium">{{ $product->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $product->sku }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-red-600 font-bold">{{ $product->current_stock }}</span>
                                        <p class="text-sm text-gray-600">/ {{ $product->min_stock }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- RECENT ACTIVITIES --}}
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">Aktivitas Terbaru</h2>
                </div>
                <div class="p-6">
                    @if($recentActivities->isEmpty())
                        <p class="text-center py-4 text-gray-500">Belum ada aktivitas</p>
                    @else
                        <div class="space-y-3">
                            @foreach($recentActivities as $activity)
                                <div class="flex items-center text-sm">
                                    <div class="w-8 h-8 rounded-full 
                                        @if($activity->type === 'Incoming') bg-green-100 text-green-600
                                        @else bg-blue-100 text-blue-600 @endif 
                                        flex items-center justify-center mr-3 text-xs font-bold">
                                        @if($activity->type === 'Incoming') IN @else OUT @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">#{{ $activity->transaction_number }}</p>
                                        <p class="text-gray-600">
                                            {{ $activity->creator->name ?? 'System' }} • 
                                            {{ $activity->created_at->format('H:i') }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded
                                        @if($activity->status === 'Pending') bg-yellow-100 text-yellow-800
                                        @elseif(in_array($activity->status, ['Verified','Approved'])) bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $activity->status }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ======================= MANAGER DASHBOARD ======================= --}}
    @if($role === 'manager')
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Manager Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}</p>
        </div>

        {{-- STATISTIK UTAMA --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Produk</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] ?? 0 }}</p>
                    </div>
                    <span class="text-3xl">📦</span>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs text-indigo-600 hover:underline mt-2 block">View →</a>
            </div>
            
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Nilai Inventori</p>
                        <p class="text-xl font-bold text-gray-900">Rp {{ number_format($stats['inventory_value'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <span class="text-3xl">💰</span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Low Stock Alert</p>
                        <p class="text-2xl font-bold {{ $stats['low_stock_count'] > 0 ? 'text-red-600' : 'text-gray-600' }}">
                            {{ $stats['low_stock_count'] ?? 0 }}
                        </p>
                    </div>
                    <span class="text-3xl">⚠️</span>
                </div>
                <a href="{{ route('products.index', array_merge(request()->except('page'), ['low_stock' => true])) }}" class="text-xs text-red-600 hover:underline mt-2 block">View →</a>
            </div>
            
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pending Approval</p>
                        <p class="text-2xl font-bold {{ $stats['pending_transactions'] > 0 ? 'text-yellow-600' : 'text-gray-600' }}">
                            {{ $stats['pending_transactions'] ?? 0 }}
                        </p>
                    </div>
                    <span class="text-3xl">⏳</span>
                </div>
                <a href="{{ route('transactions.pending.approvals') }}" class="text-xs text-yellow-600 hover:underline mt-2 block">Review →</a>
            </div>
        </div>

        {{-- GRID 2 KOLOM --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- LOW STOCK ALERTS --}}
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-900">Produk Stok Rendah</h2>
                    <a href="{{ route('products.index', array_merge(request()->except('page'), ['low_stock' => true])) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Lihat Semua
                    </a>
                </div>
                <div class="p-6">
                    @if($lowStock->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-2">Tidak ada produk dengan stok rendah</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($lowStock as $product)
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                            <span class="text-red-600 font-bold">{{ $product->current_stock }}</span>
                                        </div>
                                        <div>
                                            <a href="{{ route('products.show', $product) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                                {{ $product->name }}
                                            </a>
                                            <p class="text-sm text-gray-500">{{ $product->sku }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm text-red-600 font-semibold">
                                            {{ $product->current_stock }} / {{ $product->min_stock }}
                                        </span>
                                        <div class="mt-1">
                                            <a href="{{ route('restocks.create', ['product_id' => $product->id]) }}" 
                                               class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">
                                                Buat Restock
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- PENDING TRANSACTIONS --}}
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-900">Transaksi Perlu Approval</h2>
                    <a href="{{ route('transactions.pending.approvals') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Lihat Semua ({{ $stats['pending_transactions'] ?? 0 }})
                    </a>
                </div>
                <div class="p-6">
                    @if($pending->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-2">Tidak ada transaksi pending</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($pending as $transaction)
                                <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($transaction->type === 'Incoming') bg-green-100 text-green-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ strtoupper($transaction->type) }}
                                            </span>
                                            <span class="text-sm text-gray-500 ml-2">#{{ $transaction->transaction_number }}</span>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $transaction->created_at->format('d/m H:i') }}</span>
                                    </div>
                                    
                                    <p class="text-sm text-gray-700 mb-3">
                                        Oleh: {{ $transaction->creator->name ?? 'System' }}
                                    </p>
                                    
                                    <div class="flex justify-between items-center">
                                        <div class="text-sm">
                                            <span class="font-medium">{{ $transaction->items->count() }} item</span>
                                        </div>
                                        <a href="{{ route('transactions.show', $transaction) }}" 
                                           class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg hover:bg-indigo-100 font-medium">
                                            Review
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ======================= STAFF DASHBOARD ======================= --}}
    @if($role === 'staff')
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Staff Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}</p>
        </div>

        {{-- QUICK ACTIONS --}}
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Entry Transaksi</h2>
            <div class="flex space-x-3">
                <a href="{{ route('transactions.create.incoming') }}" 
                   class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition text-center font-semibold">
                    📥 Barang Masuk
                </a>
                <a href="{{ route('transactions.create.outgoing') }}" 
                   class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-center font-semibold">
                    📤 Barang Keluar
                </a>
            </div>
        </div>

        {{-- STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['today_transactions'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_transactions'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Completed Today</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['completed_today'] ?? 0 }}</p>
            </div>
        </div>

        {{-- TODAY'S TRANSACTIONS --}}
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Transaksi Hari Ini</h2>
            @if($todayTransactions->isEmpty())
                <p class="text-center py-8 text-gray-500">Belum ada transaksi hari ini</p>
            @else
                <div class="space-y-3">
                    @foreach($todayTransactions as $tx)
                        <div class="p-4 border rounded-lg bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <strong class="{{ $tx->type === 'Incoming' ? 'text-green-600' : 'text-blue-600' }}">
                                        {{ strtoupper($tx->type) }}
                                    </strong> — #{{ $tx->transaction_number }}
                                    <div class="text-sm text-gray-500 mt-1">{{ $tx->created_at->format('H:i:s') }}</div>
                                </div>
                                <span class="px-3 py-1 text-xs rounded-full font-medium
                                    @if($tx->status === 'Pending') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ $tx->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- ======================= SUPPLIER DASHBOARD ======================= --}}
    @if($role === 'supplier')
  
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Supplier Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}</p>
        </div>

        {{-- STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Pending Orders</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_orders'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Confirmed</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['confirmed_orders'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <p class="text-sm text-gray-500">In Transit</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['in_transit_orders'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Delivered (Month)</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['delivered_this_month'] ?? 0 }}</p>
            </div>
        </div>

        {{-- PENDING ORDERS --}}
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Order Perlu Konfirmasi</h2>
            @if($pendingOrders->isEmpty())
                <p class="text-center py-8 text-gray-500">Tidak ada order pending</p>
            @else
                <div class="space-y-3">
                    @foreach($pendingOrders as $order)
                        <div class="p-4 border rounded-lg bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <strong>PO #{{ $order->po_number }}</strong>
                                    <div class="text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</div>
                                    <div class="text-sm text-gray-600 mt-1">{{ $order->items->count() }} items</div>
                                </div>
                                <a href="{{ route('restocks.show', $order) }}" 
                                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Lihat & Konfirmasi
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- DELIVERY HISTORY --}}
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Riwayat Pengiriman</h2>
            @if($deliveryHistory->isEmpty())
                <p class="text-center py-8 text-gray-500">Belum ada riwayat</p>
            @else
                <div class="space-y-3">
                    @foreach($deliveryHistory as $h)
                        <div class="p-4 border rounded-lg bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <strong>PO #{{ $h->po_number }}</strong>
                                    <span class="ml-2 px-2 py-1 text-xs rounded
                                        @if($h->status === 'in_transit') bg-purple-100 text-purple-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $h->status)) }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $h->updated_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>
@endsection