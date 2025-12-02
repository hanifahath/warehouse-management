@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-900">Transaction Details</h1>

    <a href="{{ route('staff.transactions.index') }}"
       class="text-indigo-600 hover:underline text-sm">
        ← Back to Transactions
    </a>
</div>

{{-- Header Card --}}
<div class="bg-white p-5 rounded-xl shadow mb-6 border border-gray-200">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <h2 class="text-gray-900 font-semibold mb-2">General Info</h2>
            <p class="text-gray-700"><span class="font-semibold">ID:</span> {{ $transaction->id }}</p>
            <p class="text-gray-700"><span class="font-semibold">Type:</span> 
                <span class="
                    px-2 py-1 rounded text-sm
                    {{ $transaction->type === 'incoming' ? 'bg-indigo-100 text-indigo-700' : 'bg-yellow-100 text-yellow-800' }}
                ">
                    {{ ucfirst($transaction->type) }}
                </span>
            </p>
            <p class="text-gray-700"><span class="font-semibold">Status:</span> 
                <span class="px-2 py-1 rounded text-sm
                    @if($transaction->status === 'approved') bg-green-100 text-green-800
                    @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($transaction->status) }}
                </span>
            </p>
            <p class="text-gray-700"><span class="font-semibold">Created At:</span> {{ $transaction->created_at->format('Y-m-d H:i') }}</p>
        </div>

        <div>
            <h2 class="text-gray-900 font-semibold mb-2">Actor Info</h2>
            <p class="text-gray-700"><span class="font-semibold">Created By:</span> {{ $transaction->createdBy->name ?? '-' }}</p>
            <p class="text-gray-700"><span class="font-semibold">Verified By:</span> {{ $transaction->verifiedBy->name ?? '-' }}</p>
        </div>
    </div>
</div>

{{-- Items Section --}}
<div class="bg-white p-5 rounded-xl shadow border border-gray-200">
    <h2 class="text-gray-900 font-semibold mb-4">Transaction Items</h2>

    <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-gray-700 font-medium border-b">Product</th>
                <th class="px-4 py-2 text-left text-gray-700 font-medium border-b">Quantity</th>
                <th class="px-4 py-2 text-left text-gray-700 font-medium border-b">Current Stock</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach ($transaction->items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-900">
                        {{ $item->product->name }} 
                        <span class="text-gray-500 text-sm">({{ $item->product->sku }})</span>
                    </td>
                    <td class="px-4 py-2 text-gray-700">{{ $item->quantity }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ $item->product->stock }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Action Buttons for Manager --}}
@if(auth()->user()->hasRole('Warehouse Manager') && $transaction->status === 'pending')
<div class="mt-6 flex space-x-3">
    <form action="{{ route('manager.transactions.approve', $transaction->id) }}" method="POST">
        @csrf
        <button class="px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-lg">
            Approve
        </button>
    </form>

    <form action="{{ route('manager.transactions.reject', $transaction->id) }}" method="POST">
        @csrf
        <button class="px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg">
            Reject
        </button>
    </form>
</div>
@endif

@endsection
