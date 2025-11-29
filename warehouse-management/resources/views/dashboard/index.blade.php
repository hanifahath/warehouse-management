@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    {{-- Admin Dashboard --}}
    @role('Admin')
        <div class="grid grid-cols-3 gap-4 mb-6">
            <x-warehouse.stat-card title="Total Products" value="{{ $productsCount }}" />
            <x-warehouse.stat-card title="Transactions This Month" value="{{ $transactionsCount }}" />
            <x-warehouse.stat-card title="Inventory Value" value="Rp {{ number_format($inventoryValue, 0, ',', '.') }}" />
        </div>

        <h2 class="text-xl font-bold mb-2">Low Stock Alerts</h2>
        <x-warehouse.card>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">SKU</th>
                        <th class="p-2">Name</th>
                        <th class="p-2">Stock</th>
                        <th class="p-2">Min Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $product)
                        <tr class="border-b">
                            <td class="p-2">{{ $product->sku }}</td>
                            <td class="p-2">{{ $product->name }}</td>
                            <td class="p-2">
                                <x-warehouse.badge status="low" /> {{ $product->stock }}
                            </td>
                            <td class="p-2">{{ $product->min_stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-warehouse.card>
    @endrole

    {{-- Warehouse Manager Dashboard --}}
    @role('Manager')
        <div class="grid grid-cols-3 gap-4 mb-6">
            <x-warehouse.stat-card title="Total Items in Warehouse" value="{{ $totalItems ?? 0}}" />
            <x-warehouse.stat-card title="Low Stock Products" value="{{ $lowStockCount ?? 0}}" />
            <x-warehouse.stat-card title="Pending Transactions" value="{{ $pendingTransactionsCount ?? 0}}" />
        </div>

        <h2 class="text-xl font-bold mb-2">Pending Transactions</h2>
        <x-warehouse.card>
            <ul>
                @foreach($pendingTransactions as $trx)
                    <li>
                        {{ $trx->transaction_number }} - {{ $trx->type }} 
                        <x-warehouse.badge status="pending" />
                    </li>
                @endforeach
            </ul>
        </x-warehouse.card>

        <h2 class="text-xl font-bold mt-6 mb-2">Active Restock Orders</h2>
        <x-warehouse.card>
            <ul>
                @foreach($activeRestocks as $order)
                    <li>
                        {{ $order->po_number }} - {{ $order->supplier->name }} 
                        <x-warehouse.badge status="{{ strtolower($order->status) }}" />
                    </li>
                @endforeach
            </ul>
        </x-warehouse.card>
    @endrole

    {{-- Staff Gudang Dashboard --}}
    @role('Staff')
        <h2 class="text-xl font-bold mb-2">Quick Entry</h2>
        <x-warehouse.card>
            <div class="flex space-x-4">
                <a href="{{ route('staff.transactions.create_incoming') }}">
                    <x-warehouse.button type="primary">Incoming Transaction</x-warehouse.button>
                </a>
                <a href="{{ route('staff.transactions.create_outgoing') }}">
                    <x-warehouse.button type="primary">Outgoing Transaction</x-warehouse.button>
                </a>
            </div>
        </x-warehouse.card>

        <h2 class="text-xl font-bold mt-6 mb-2">Today's Transactions</h2>
        <x-warehouse.card>
            <ul>
                @foreach($todaysTransactions as $trx)
                    <li>
                        {{ $trx->transaction_number }} - {{ $trx->type }} 
                        <x-warehouse.badge status="{{ strtolower($trx->status) }}" />
                    </li>
                @endforeach
            </ul>
        </x-warehouse.card>
    @endrole

    {{-- Supplier Dashboard --}}
    @role('Supplier')
        <h2 class="text-xl font-bold mb-2">Restock Orders to Confirm</h2>
        <x-warehouse.card>
            <ul>
                @foreach($pendingRestocks as $order)
                    <li>
                        {{ $order->po_number }} - {{ $order->notes }}
                        <form method="POST" action="{{ route('supplier.restocks.confirm', $order) }}" class="inline">
                            @csrf
                            <x-warehouse.button type="primary">Confirm</x-warehouse.button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </x-warehouse.card>

        <h2 class="text-xl font-bold mt-6 mb-2">Delivery History</h2>
        <x-warehouse.card>
            <ul>
                @foreach($deliveryHistory as $delivery)
                    <li>
                        {{ $delivery->po_number }} - Delivered on {{ $delivery->expected_delivery_date->format('d M Y') }}
                    </li>
                @endforeach
            </ul>
        </x-warehouse.card>
    @endrole
@endsection