@extends('layouts.app')

@section('title', 'Restock Detail')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded shadow p-6 border">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Restock Order #{{ $restock->id }}</h2>

            @can('update', $restock)
                <a href="{{ route('restocks.edit', $restock->id) }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Edit
                </a>
            @endcan
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">

            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Basic Info</h3>
                <p><span class="font-medium">Supplier:</span> {{ $restock->supplier->name }}</p>
                <p><span class="font-medium">Status:</span>
                    <span class="px-2 py-1 rounded text-white
                        @if($restock->status === 'pending') bg-yellow-500
                        @elseif($restock->status === 'confirmed') bg-blue-500
                        @elseif($restock->status === 'in_transit') bg-purple-600
                        @elseif($restock->status === 'received') bg-green-600
                        @endif">
                        {{ ucfirst($restock->status) }}
                    </span>
                </p>
                <p><span class="font-medium">Created at:</span> {{ $restock->created_at->format('d M Y H:i') }}</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Metadata</h3>
                <p><span class="font-medium">Order Code:</span> {{ $restock->order_code }}</p>
                <p><span class="font-medium">Notes:</span> {{ $restock->notes ?? '-' }}</p>
            </div>

        </div>

        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4">Items</h3>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 border">
                        <th class="border px-3 py-2 text-left">Product</th>
                        <th class="border px-3 py-2 text-left">Quantity</th>
                        <th class="border px-3 py-2 text-left">Price</th>
                        <th class="border px-3 py-2 text-left">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($restock->items as $item)
                        <tr class="border">
                            <td class="border px-3 py-2">
                                {{ $item->product->name }}
                            </td>
                            <td class="border px-3 py-2">
                                {{ $item->quantity }}
                            </td>
                            <td class="border px-3 py-2">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </td>
                            <td class="border px-3 py-2 font-medium">
                                Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end mt-4">
                <div class="text-right">
                    <p class="text-lg font-semibold">
                        Total: Rp {{ number_format($restock->items->sum(fn($i) => $i->quantity * $i->price), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-between">

            <a href="{{ route('restocks.index') }}"
               class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                Back
            </a>

            @can('update', $restock)
                @if($restock->status === 'pending')
                    <form action="{{ route('restocks.confirm', $restock->id) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Confirm
                        </button>
                    </form>
                @endif

                @if($restock->status === 'confirmed')
                    <form action="{{ route('restocks.transit', $restock->id) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                            Mark as In Transit
                        </button>
                    </form>
                @endif

                @if($restock->status === 'in_transit')
                    <form action="{{ route('restocks.receive', $restock->id) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Mark as Received
                        </button>
                    </form>
                @endif
            @endcan

        </div>
    </div>
</div>
@endsection
