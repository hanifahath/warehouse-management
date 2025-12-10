@extends('layouts.app')

@section('title', 'Stock Movements')

@section('content')
<div class="px-6 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Stock Movement History</h1>
            <p class="text-sm text-gray-500 mt-1">Track all stock changes from transactions and restocks</p>
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
                <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- TAB NAVIGATION --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-6">
            <a href="{{ route('stock-movements.index') }}"
                class="pb-3 text-sm font-medium {{ !request('type') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                All Movements
            </a>
            <a href="{{ route('stock-movements.index', ['type' => 'in']) }}"
                class="pb-3 text-sm font-medium {{ request('type') === 'in' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-gray-700' }}">
                Stock In
            </a>
            <a href="{{ route('stock-movements.index', ['type' => 'out']) }}"
                class="pb-3 text-sm font-medium {{ request('type') === 'out' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">
                Stock Out
            </a>
        </nav>
    </div>

    {{-- FILTERS --}}
    <form method="GET" class="mb-6">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- PRODUCT --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                    <select name="product_id" 
                            class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- DATE FROM --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- DATE TO --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- ACTIONS --}}
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                        Apply
                    </button>
                    @if(request()->anyFilled(['product_id', 'date_from', 'date_to', 'type']))
                        <a href="{{ route('stock-movements.index') }}" 
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            {{-- ACTIVE FILTERS --}}
            @if(request()->anyFilled(['product_id', 'date_from', 'date_to', 'type']))
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <span class="font-medium">Active Filters:</span>
                        
                        @if(request('type'))
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                {{ request('type') === 'in' ? 'Stock In' : 'Stock Out' }}
                            </span>
                        @endif
                        
                        @if(request('product_id'))
                            @php 
                                $selectedProduct = $products->firstWhere('id', request('product_id')); 
                            @endphp
                            @if($selectedProduct)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                    Product: {{ $selectedProduct->name }}
                                </span>
                            @endif
                        @endif
                        
                        @if(request('date_from'))
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                From: {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}
                            </span>
                        @endif
                        
                        @if(request('date_to'))
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                To: {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </form>

    {{-- TABLE --}}
    <div class="bg-white border border-gray-200 shadow rounded-lg overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-4 font-semibold text-gray-700">Date & Time</th>
                    <th class="py-3 px-4 font-semibold text-gray-700">Product</th>
                    <th class="py-3 px-4 font-semibold text-gray-700">Type</th>
                    <th class="py-3 px-4 font-semibold text-gray-700">Change</th>
                    <th class="py-3 px-4 font-semibold text-gray-700">Before → After</th>
                    <th class="py-3 px-4 font-semibold text-gray-700">Source</th>
                    <th class="py-3 px-4 font-semibold text-gray-700">User</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($movements as $movement)
                    <tr class="hover:bg-gray-50">
                        {{-- DATE --}}
                        <td class="py-3 px-4">
                            <div class="text-gray-900 font-medium">{{ $movement->created_at->format('d M Y') }}</div>
                            <div class="text-gray-500 text-xs">{{ $movement->created_at->format('H:i:s') }}</div>
                        </td>

                        {{-- PRODUCT --}}
                        <td class="py-3 px-4">
                            <div class="font-medium text-gray-900">{{ $movement->product->name }}</div>
                            <div class="text-gray-500 text-xs">SKU: {{ $movement->product->sku }}</div>
                        </td>

                        {{-- TYPE --}}
                        <td class="py-3 px-4">
                            @if($movement->type === 'in')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Stock In
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Stock Out
                                </span>
                            @endif
                        </td>

                        {{-- CHANGE --}}
                        <td class="py-3 px-4">
                            <span class="font-mono text-sm {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                            </span>
                        </td>

                        {{-- BEFORE/AFTER --}}
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600 font-mono">{{ $movement->before_quantity }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="font-semibold font-mono {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->after_quantity }}
                                </span>
                            </div>
                        </td>

                        {{-- SOURCE --}}
                        <td class="py-3 px-4">
                            @if($movement->reference_type === 'App\\Models\\Transaction')
                                <div class="text-xs">
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-blue-100 text-blue-800 font-medium">
                                        Transaction
                                    </span>
                                    @if($movement->reference)
                                        <div class="text-gray-500 mt-1">{{ $movement->reference->transaction_number }}</div>
                                    @endif
                                </div>
                            @elseif($movement->reference_type === 'App\\Models\\RestockOrder')
                                <div class="text-xs">
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-purple-100 text-purple-800 font-medium">
                                        Restock
                                    </span>
                                    @if($movement->reference)
                                        <div class="text-gray-500 mt-1">{{ $movement->reference->po_number }}</div>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs font-medium">
                                    System
                                </span>
                            @endif
                        </td>

                        {{-- USER --}}
                        <td class="py-3 px-4">
                            <div class="text-gray-900">{{ $movement->user->name }}</div>
                            <div class="text-gray-500 text-xs capitalize">{{ $movement->user->role }}</div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-gray-500 text-lg font-medium mb-2">No stock movements found</p>
                            <p class="text-gray-400 text-sm">Stock movements will appear here when transactions or restocks are completed</p>
                            @if(request()->anyFilled(['product_id', 'date_from', 'date_to', 'type']))
                                <div class="mt-4">
                                    <a href="{{ route('stock-movements.index') }}" 
                                       class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                        Clear all filters
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
    @if($movements->hasPages())
        <div class="mt-6">
            {{ $movements->withQueryString()->links() }}
        </div>
    @endif

</div>
@endsection