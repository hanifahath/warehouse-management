@extends('layouts.app')

@section('title', 'Restock Orders')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Restock Orders</h1>

    <x-warehouse.card>
        @role('Manager')
            <div class="mb-4">
                <a href="{{ route('restocks.create') }}">
                    <x-warehouse.button type="primary">Create Restock Order</x-warehouse.button>
                </a>
            </div>
        @endrole

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">PO Number</th>
                    <th class="p-2">Supplier</th>
                    <th class="p-2">Order Date</th>
                    <th class="p-2">Expected Delivery</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($restocks as $order)
                    <tr class="border-b">
                        <td class="p-2">{{ $order->po_number }}</td>
                        <td class="p-2">{{ $order->supplier->name }}</td>
                        <td class="p-2">{{ $order->order_date->format('d M Y') }}</td>
                        <td class="p-2">{{ $order->expected_delivery_date->format('d M Y') }}</td>
                        <td class="p-2">
                            <x-warehouse.badge status="{{ strtolower($order->status) }}" />
                        </td>
                        <td class="p-2 flex space-x-2">
                            <a href="{{ route('restocks.show', $order) }}" class="text-blue-600 hover:underline">View</a>
                            @role('Supplier')
                                @if($order->status === 'Pending')
                                    <form method="POST" action="{{ route('restocks.confirm', $order) }}">
                                        @csrf
                                        <x-warehouse.button type="primary">Confirm</x-warehouse.button>
                                    </form>
                                @endif
                            @endrole
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @include('shared.pagination', ['paginator' => $restocks])
    </x-warehouse.card>
@endsection