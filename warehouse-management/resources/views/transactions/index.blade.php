@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Transactions</h1>

    {{-- Tabs untuk Incoming & Outgoing --}}
    <div class="flex space-x-4 mb-4">
        <a href="{{ route('transactions.index', ['type' => 'incoming']) }}" 
           class="px-4 py-2 rounded {{ request('type') === 'incoming' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
            Incoming
        </a>
        <a href="{{ route('transactions.index', ['type' => 'outgoing']) }}" 
           class="px-4 py-2 rounded {{ request('type') === 'outgoing' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
            Outgoing
        </a>
    </div>

    @include('shared.filter', ['filters' => ['Status', 'Date']])

    <x-warehouse.card>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Transaction #</th>
                    <th class="p-2">Type</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Supplier/Customer</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $trx)
                    <tr class="border-b">
                        <td class="p-2">{{ $trx->transaction_number }}</td>
                        <td class="p-2">{{ $trx->type }}</td>
                        <td class="p-2">{{ $trx->date->format('d M Y') }}</td>
                        <td class="p-2">
                            {{ $trx->type === 'Incoming' ? $trx->supplier->name : $trx->customer_name }}
                        </td>
                        <td class="p-2">
                            <x-warehouse.badge status="{{ strtolower($trx->status) }}" />
                        </td>
                        <td class="p-2 flex space-x-2">
                            <a href="{{ route('transactions.show', $trx) }}" class="text-blue-600 hover:underline">View</a>
                            <a href="{{ route('transactions.edit', $trx) }}" class="text-yellow-600 hover:underline">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @include('shared.pagination', ['paginator' => $transactions])
    </x-warehouse.card>
@endsection