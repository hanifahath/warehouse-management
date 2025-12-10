@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Transactions</h1>
    </div>

    {{-- TAB --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-6">
            <a href="?type=incoming"
                class="pb-3 text-sm font-medium
                    {{ (request('type') != 'outgoing') ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-gray-700' }}">
                Incoming (Barang Masuk)
            </a>

            <a href="?type=outgoing"
                class="pb-3 text-sm font-medium
                    {{ request('type') == 'outgoing' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                Outgoing (Barang Keluar)
            </a>
        </nav>
    </div>

    {{-- FILTERS --}}
    <form method="GET" class="mb-6">
        @if(request('type'))
            <input type="hidden" name="type" value="{{ request('type') }}">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- SEARCH --}}
            <div>
                <input type="text" name="search" placeholder="Search transaction number..."
                        value="{{ request('search') }}"
                        class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- DATE FILTER --}}
            <div>
                <input type="date" name="date" value="{{ request('date') }}"
                        class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- STATUS FILTER --}}
            <div>
                <select name="status"
                        class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    @php
                        // Sesuai requirement project
                        $statuses = [
                            'pending' => 'Pending',
                            'verified' => 'Verified',
                            'completed' => 'Completed',
                            'approved' => 'Approved',
                            'shipped' => 'Shipped',
                            'rejected' => 'Rejected'
                        ];
                    @endphp
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SUPPLIER FILTER (for manager/admin) --}}
            @if(in_array(auth()->user()->role, ['manager', 'admin']) && isset($suppliers))
            <div>
                <select name="supplier_id"
                        class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- SUBMIT --}}
            <div class="flex gap-2">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                    Apply Filter
                </button>
                <a href="{{ route('transactions.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- ACTIVE FILTERS --}}
    @if(request()->anyFilled(['search', 'date', 'status', 'type', 'supplier_id']))
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm">
        <strong>Active Filters:</strong>
        @if(request('type')) <span class="ml-2 px-2 py-1 bg-blue-100 rounded">Type: {{ request('type') }}</span> @endif
        @if(request('search')) <span class="ml-2 px-2 py-1 bg-blue-100 rounded">Search: "{{ request('search') }}"</span> @endif
        @if(request('date')) <span class="ml-2 px-2 py-1 bg-blue-100 rounded">Date: {{ request('date') }}</span> @endif
        @if(request('status')) <span class="ml-2 px-2 py-1 bg-blue-100 rounded">Status: {{ request('status') }}</span> @endif
        @if(request('supplier_id') && isset($suppliers))
            @php $selectedSupplier = $suppliers->where('id', request('supplier_id'))->first(); @endphp
            @if($selectedSupplier)
                <span class="ml-2 px-2 py-1 bg-blue-100 rounded">Supplier: {{ $selectedSupplier->name }}</span>
            @endif
        @endif
    </div>
    @endif

    {{-- TABLE --}}
    <div class="bg-white border border-gray-200 shadow rounded-lg overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-4 font-medium text-gray-600">Transaction #</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Type</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Items</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Total Qty</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Status</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Date</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Created By</th>
                    <th class="py-3 px-4 font-medium text-gray-600 text-right">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($transactions as $tx)
                    @php
                        $totalQuantity = $tx->items->sum('quantity');
                        $itemCount = $tx->items->count();
                        $firstItem = $tx->items->first();
                        $product = $firstItem?->product;
                        
                        // Tentukan warna dan label untuk type
                        $typeLower = strtolower($tx->type);
                        if($typeLower === 'incoming') {
                            $typeColor = 'green';
                            $typeLabel = 'Incoming';
                            $typeFull = 'Barang Masuk';
                        } else {
                            $typeColor = 'blue';
                            $typeLabel = 'Outgoing';
                            $typeFull = 'Barang Keluar';
                        }
                        
                        // Tentukan warna dan label untuk status
                        $statusConfig = [
                            'pending' => ['color' => 'yellow', 'label' => 'Pending'],
                            'verified' => ['color' => 'green', 'label' => 'Verified'],
                            'completed' => ['color' => 'green', 'label' => 'Completed'],
                            'approved' => ['color' => 'blue', 'label' => 'Approved'],
                            'shipped' => ['color' => 'indigo', 'label' => 'Shipped'],
                            'rejected' => ['color' => 'red', 'label' => 'Rejected'],
                        ];
                        
                        $statusInfo = $statusConfig[$tx->status] ?? ['color' => 'gray', 'label' => $tx->status];
                    @endphp
                    
                    <tr class="hover:bg-gray-50">
                        {{-- Transaction Number --}}
                        <td class="py-3 px-4 font-mono text-gray-800 text-xs">
                            {{ $tx->transaction_number }}
                            <div class="text-gray-500 text-xs mt-1">ID: {{ $tx->id }}</div>
                        </td>

                        {{-- Type --}}
                        <td class="py-3 px-4">
                            <div class="flex flex-col gap-1">
                                <span class="bg-{{ $typeColor }}-100 text-{{ $typeColor }}-800 px-2 py-1 rounded text-xs inline-block w-fit">
                                    {{ $typeLabel }}
                                </span>
                                <div class="text-gray-500 text-xs">{{ $typeFull }}</div>
                            </div>
                        </td>

                        {{-- Items --}}
                        <td class="py-3 px-4 text-gray-800">
                            @if($product)
                                <div class="font-medium">{{ $product->name }}</div>
                                <div class="text-gray-500 text-xs">{{ $product->sku }}</div>
                            @else
                                <div class="text-gray-400">No product</div>
                            @endif
                            @if($itemCount > 1)
                                <div class="text-gray-500 text-xs mt-1">+ {{ $itemCount - 1 }} more items</div>
                            @endif
                        </td>
                        
                        {{-- Total Quantity --}}
                        <td class="py-3 px-4 text-gray-800">
                            <div class="font-medium">{{ $totalQuantity }}</div>
                            <div class="text-gray-500 text-xs">{{ $itemCount }} item(s)</div>
                        </td>

                        {{-- Status --}}
                        <td class="py-3 px-4">
                            <span class="bg-{{ $statusInfo['color'] }}-100 text-{{ $statusInfo['color'] }}-800 px-2 py-1 rounded text-xs">
                                {{ $statusInfo['label'] }}
                            </span>
                            @if($typeLower === 'incoming')
                                <div class="text-gray-500 text-xs mt-1">
                                    @if($tx->status === 'pending')
                                        Menunggu verifikasi manager
                                    @elseif($tx->status === 'verified')
                                        Sudah diverifikasi
                                    @elseif($tx->status === 'completed')
                                        Selesai
                                    @endif
                                </div>
                            @else
                                <div class="text-gray-500 text-xs mt-1">
                                    @if($tx->status === 'pending')
                                        Menunggu approval manager
                                    @elseif($tx->status === 'approved')
                                        Sudah disetujui
                                    @elseif($tx->status === 'shipped')
                                        Sudah dikirim
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- Date --}}
                        <td class="py-3 px-4 text-gray-800">
                            <div>{{ $tx->date->format('d M Y') }}</div>
                            <div class="text-gray-500 text-xs">{{ $tx->created_at->format('H:i') }}</div>
                        </td>

                        {{-- Created By --}}
                        <td class="py-3 px-4 text-gray-800">
                            @if($tx->creator)
                                <div>{{ $tx->creator->name }}</div>
                                <div class="text-gray-500 text-xs">{{ $tx->creator->role }}</div>
                            @else
                                <div class="text-gray-400">-</div>
                            @endif
                        </td>

                        {{-- Action --}}
                        <td class="py-3 px-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('transactions.show', $tx) }}"
                                   class="px-3 py-1 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded text-sm">
                                    View
                                </a>
                                
                                @if($tx->status === 'pending' && auth()->id() === $tx->created_by)
                                <a href="{{ route('transactions.edit', $tx) }}"
                                   class="px-3 py-1 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded text-sm">
                                    Edit
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">
                            <div class="mb-2">
                                <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            No transactions found.
                            @if(request()->anyFilled(['search', 'date', 'status', 'type', 'supplier_id']))
                                <div class="mt-2">
                                    <a href="{{ route('transactions.index') }}" 
                                       class="text-indigo-600 hover:underline text-sm">
                                        Clear filters
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($transactions->hasPages())
    <div class="mt-6">
        {{ $transactions->withQueryString()->links() }}
    </div>
    @endif

</div>
@endsection