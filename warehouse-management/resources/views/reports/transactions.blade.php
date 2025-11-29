@extends('layouts.app')

@section('title', 'Transactions Report')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Transactions Report</h1>

    {{-- Filter --}}
    @include('shared.filter', ['filters' => ['Type', 'Status', 'Date']])

    <x-warehouse.card>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Transaction #</th>
                    <th class="p-2">Type</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Supplier/Customer</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Notes</th>
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
                        <td class="p-2">{{ $trx->notes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-warehouse.card>
@endsection