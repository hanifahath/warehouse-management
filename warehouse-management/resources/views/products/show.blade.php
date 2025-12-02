@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>

        <div class="flex items-center space-x-3">
            {{-- EDIT --}}
            @can('update', $product)
                <a href="{{ route('admin.products.edit', $product) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Edit
                </a>
            @endcan
        </div>
    </div>


    {{-- DETAIL CARD --}}
    <div class="bg-white shadow rounded-lg border border-gray-200 p-6 mb-8">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-12">

            {{-- SKU --}}
            <div>
                <p class="text-gray-500 text-sm">SKU</p>
                <p class="text-gray-900 font-medium">{{ $product->sku }}</p>
            </div>

            {{-- Category --}}
            <div>
                <p class="text-gray-500 text-sm">Category</p>
                <p class="text-gray-900 font-medium">
                    {{ $product->category->name ?? '-' }}
                </p>
            </div>

            {{-- Stock --}}
            <div>
                <p class="text-gray-500 text-sm">Stock</p>

                @if($product->stock === 0)
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded text-sm">
                        Out of Stock
                    </span>
                @elseif($product->stock <= $product->min_stock)
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded text-sm">
                        Low Stock ({{ $product->stock }})
                    </span>
                @else
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm">
                        {{ $product->stock }}
                    </span>
                @endif
            </div>

            {{-- Rack Location --}}
            <div>
                <p class="text-gray-500 text-sm">Rack Location</p>
                <p class="text-gray-900 font-medium">
                    {{ $product->location ?? '-' }}
                </p>
            </div>

            {{-- Purchase Price --}}
            <div>
                <p class="text-gray-500 text-sm">Purchase Price</p>
                <p class="text-gray-900 font-medium">
                    Rp {{ number_format($product->purchase_price ?? 0, 0, ',', '.') }}
                </p>
            </div>

            {{-- Selling Price --}}
            <div>
                <p class="text-gray-500 text-sm">Selling Price</p>
                <p class="text-gray-900 font-medium">
                    Rp {{ number_format($product->selling_price ?? 0, 0, ',', '.') }}
                </p>
            </div>

            {{-- Min Stock --}}
            <div>
                <p class="text-gray-500 text-sm">Minimum Stock</p>
                <p class="text-gray-900 font-medium">{{ $product->min_stock }}</p>
            </div>

            {{-- Description --}}
            <div class="md:col-span-2 mt-4">
                <p class="text-gray-500 text-sm mb-1">Description</p>
                <p class="text-gray-700 leading-relaxed">
                    {{ $product->description ?? '-' }}
                </p>
            </div>

        </div>

        {{-- RESTOCK BUTTON --}}
        @can('create', App\Models\RestockOrder::class)
            @if($product->stock <= $product->min_stock)
                <div class="mt-6">
                    <a href="{{ route('restocks.create', ['product' => $product->id]) }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Create Restock Order
                    </a>
                </div>
            @endif
        @endcan

    </div>



    {{-- TRANSACTION HISTORY --}}
    <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Transactions</h2>

    <div class="bg-white shadow rounded-lg border border-gray-200 overflow-hidden">

        <table class="w-full">
            <thead class="bg-gray-50 text-gray-600 text-sm border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Type</th>
                    <th class="px-4 py-3 text-left font-medium">Quantity</th>
                    <th class="px-4 py-3 text-left font-medium">Status</th>
                    <th class="px-4 py-3 text-left font-medium">Date</th>
                    <th class="px-4 py-3 text-right font-medium">Details</th>
                </tr>
            </thead>

            <tbody class="text-sm text-gray-700">

                @forelse ($recentTransactions as $tr)
                    <tr class="border-b last:border-0 hover:bg-gray-50">

                        {{-- TYPE --}}
                        <td class="px-4 py-3">
                            @if(strtolower($tr->type) === 'incoming')
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                    Incoming
                                </span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                    Outgoing
                                </span>
                            @endif
                        </td>

                        {{-- QUANTITY --}}
                        <td class="px-4 py-3">{{ $tr->quantity }}</td>

                        {{-- STATUS --}}
                        <td class="px-4 py-3">
                            @if(strtolower($tr->status) === 'approved' || strtolower($tr->status) === 'completed')
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                    Approved
                                </span>
                            @elseif(strtolower($tr->status) === 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">
                                    Pending
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">
                                    Rejected
                                </span>
                            @endif
                        </td>

                        {{-- DATE --}}
                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($tr->date ?? $tr->created_at)->format('d M Y H:i') }}
                        </td>

                        {{-- DETAILS --}}
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('transactions.show', $tr) }}"
                               class="text-indigo-600 hover:underline">
                                View
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No recent transactions found.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>

</div>
@endsection
