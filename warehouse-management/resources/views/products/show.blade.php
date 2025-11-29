@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Product Details</h1>

    <x-warehouse.card>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <strong>Name:</strong> {{ $product->name }}
            </div>
            <div>
                <strong>SKU:</strong> {{ $product->sku }}
            </div>
            <div>
                <strong>Category:</strong> {{ $product->category->name }}
            </div>
            <div>
                <strong>Unit:</strong> {{ $product->unit }}
            </div>
            <div>
                <strong>Purchase Price:</strong> Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
            </div>
            <div>
                <strong>Selling Price:</strong> Rp {{ number_format($product->selling_price, 0, ',', '.') }}
            </div>
            <div>
                <strong>Stock:</strong>
                <x-warehouse.badge status="{{ $product->stock < $product->min_stock ? 'low' : 'ok' }}" />
                {{ $product->stock }}
            </div>
            <div>
                <strong>Minimum Stock:</strong> {{ $product->min_stock }}
            </div>
            <div>
                <strong>Location:</strong> {{ $product->location }}
            </div>
            <div>
                <strong>Description:</strong> {{ $product->description }}
            </div>
            <div class="col-span-2">
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" 
                         alt="{{ $product->name }}" 
                         class="w-48 h-48 object-cover rounded">
                @endif
            </div>
        </div>
    </x-warehouse.card>

    {{-- Tombol Restock Order untuk Manager jika stok rendah --}}
    @role('Manager')
        @if($product->stock < $product->min_stock)
            <div class="mt-4">
                <form method="POST" action="{{ route('supplier.restocks.createFromProduct', $product) }}">
                    @csrf
                    <x-warehouse.button type="primary">
                        Create Restock Order
                    </x-warehouse.button>
                </form>
            </div>
        @endif
    @endrole

    {{-- Riwayat Transaksi Produk --}}
    <h2 class="text-xl font-bold mt-6 mb-2">Recent Transactions</h2>
    <x-warehouse.card>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Transaction #</th>
                    <th class="p-2">Type</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Quantity</th>
                    <th class="p-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $trx)
                    <tr class="border-b">
                        <td class="p-2">{{ $trx->transaction->transaction_number }}</td>
                        <td class="p-2">{{ $trx->transaction->type }}</td>
                        <td class="p-2">{{ $trx->transaction->date->format('d M Y') }}</td>
                        <td class="p-2">{{ $trx->quantity }}</td>
                        <td class="p-2">
                            <x-warehouse.badge status="{{ strtolower($trx->transaction->status) }}" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center p-4 text-gray-500">
                            No recent transactions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-warehouse.card>
@endsection