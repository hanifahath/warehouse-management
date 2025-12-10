@extends('layouts.app')

@section('title', $product->name . ' - Product Details')

@section('content')
<div class="px-6 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('products.index') }}" 
                           class="inline-flex items-center text-sm text-gray-700 hover:text-indigo-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            Products
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $product->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
        </div>

        <div class="flex items-center space-x-3">
            {{-- DELETE --}}
            @can('delete', $product)
                <form method="POST" action="{{ route('products.destroy', $product) }}"
                      onsubmit="return confirm('Delete product {{ $product->name }}? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                        Delete
                    </button>
                </form>
            @endcan
            
            {{-- EDIT --}}
            @can('update', $product)
                <a href="{{ route('products.edit', $product) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
                    Edit Product
                </a>
            @endcan
            
            {{-- STOCK ADJUSTMENT - Hanya Admin & Manager --}}
            @can('update', $product)
                <a href="{{ route('products.adjust-stock', $product) }}"
                   class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">
                    Adjust Stock
                </a>
            @endcan
        </div>
    </div>
    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    {{-- QUICK STATS FROM SERVICE --}}
    @php
        // Define default stats jika $stats tidak ada atau incomplete
        $defaultStats = [
            'stock_status' => [
                'bg_color' => 'gray-100',
                'text_color' => 'gray-800',
                'label' => 'Normal',
                'icon' => 'check-circle'
            ],
            'inventory_value' => ($product->current_stock ?? 0) * ($product->selling_price ?? 0),
            'profit_margin' => [
                'is_positive' => true,
                'percentage' => 0,
                'amount' => 0
            ],
            'stock_percentage' => $product->min_stock > 0 ? min(($product->current_stock / $product->min_stock) * 100, 100) : 0
        ];
        
        // Merge dengan $stats jika ada
        $stats = array_merge($defaultStats, $stats ?? []);
        
        // Pastikan nested arrays juga ter-merge
        if (isset($stats['stock_status'])) {
            $stats['stock_status'] = array_merge($defaultStats['stock_status'], $stats['stock_status']);
        }
        
        if (isset($stats['profit_margin'])) {
            $stats['profit_margin'] = array_merge($defaultStats['profit_margin'], $stats['profit_margin']);
        }
        
        // Calculate stock percentage
        $stockPercentage = $product->min_stock > 0 
            ? min(($product->current_stock / $product->min_stock) * 100, 100) 
            : 0;
            
        // Determine stock status color based on current stock
        $stockStatusColor = 'gray';
        $stockStatusText = 'Normal';
        $stockStatusIcon = 'check-circle';
        
        if ($product->current_stock <= 0) {
            $stockStatusColor = 'red';
            $stockStatusText = 'Out of Stock';
            $stockStatusIcon = 'x-circle';
        } elseif ($product->current_stock <= $product->min_stock) {
            $stockStatusColor = 'yellow';
            $stockStatusText = 'Low Stock';
            $stockStatusIcon = 'exclamation';
        } elseif ($product->current_stock > $product->min_stock) {
            $stockStatusColor = 'green';
            $stockStatusText = 'In Stock';
            $stockStatusIcon = 'check-circle';
        }
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        {{-- Stock Card --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-2 rounded-lg 
                        @if($product->current_stock <= 0) bg-red-100 text-red-800
                        @elseif($product->current_stock <= $product->min_stock) bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800
                        @endif">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Current Stock</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $product->current_stock ?? 0 }}</p>
                    <p class="text-xs text-gray-500">{{ $product->unit ?? 'pcs' }}</p>
                </div>
            </div>
        </div>

        {{-- Min Stock Card --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-2 rounded-lg bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Minimum Stock</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $product->min_stock ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Alert threshold</p>
                </div>
            </div>
        </div>

        {{-- Inventory Value Card --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-2 rounded-lg bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Inventory Value</p>
                    <p class="text-lg font-bold text-gray-900">
                        Rp {{ number_format($stats['inventory_value'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">@ Rp {{ number_format($product->sell_price ?? 0, 0, ',', '.') }}/unit</p>
                </div>
            </div>
        </div>

        {{-- Status Card --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-2 rounded-lg {{ ($product->is_active ?? true) ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <p class="text-lg font-bold text-gray-900">
                        {{ ($product->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $stockStatusText }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- PRODUCT DETAILS --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg border border-gray-200 p-6">
                
                {{-- Product Image --}}
                @if(isset($product->image_path) && $product->image_path)
                <div class="mb-6">
                    <div class="relative rounded-lg overflow-hidden border border-gray-200">
                        {{-- Ganti asset() dengan route() --}}
                        <img src="{{ asset('storage/' . $product->image_path) }}"
                            alt="{{ $product->name }}"
                            class="w-full h-64 object-cover">
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-black bg-opacity-50 text-white text-xs rounded-full">
                                {{ $product->sku }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- DETAILS GRID --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Left Column: Basic Info --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Basic Information</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">SKU</label>
                            <p class="mt-1 text-gray-900 font-mono">{{ $product->sku ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Category</label>
                            <p class="mt-1 text-gray-900">
                                @if(isset($product->category) && $product->category)
                                    @can('view', $product->category)
                                        <a href="{{ route('categories.show', $product->category) }}"
                                           class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-sm hover:bg-indigo-100 hover:text-indigo-800 transition-colors">
                                            {{ $product->category->name }}
                                        </a>
                                    @else
                                        <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-sm">
                                            {{ $product->category->name }}
                                        </span>
                                    @endcan
                                @else
                                    <span class="text-gray-400">Uncategorized</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Unit</label>
                            <p class="mt-1 text-gray-900">{{ $product->unit ?? 'pcs' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Created At</label>
                            <p class="mt-1 text-gray-900">{{ isset($product->created_at) ? $product->created_at->format('d M Y, H:i') : 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="mt-1 text-gray-900">{{ isset($product->updated_at) ? $product->updated_at->format('d M Y, H:i') : 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- Right Column: Pricing & Location --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Pricing & Location</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Rack Location</label>
                            <p class="mt-1 text-gray-900">
                                @if(isset($product->rack_location) && $product->rack_location)
                                    <span class="font-mono bg-gray-100 px-3 py-1 rounded">
                                        {{ $product->rack_location }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Purchase Price</label>
                            <p class="mt-1 text-lg font-bold text-gray-900">
                                Rp {{ number_format($product->purchase_price ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Selling Price</label>
                            <p class="mt-1 text-lg font-bold text-green-600">
                                Rp {{ number_format($product->selling_price ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        
                        {{-- Profit Margin from Service --}}
                        @if(isset($stats['profit_margin']) && $stats['profit_margin'])
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Profit Margin</label>
                            <p class="mt-1 text-gray-900">
                                <span class="{{ $stats['profit_margin']['is_positive'] ? 'text-green-600' : 'text-red-600' }} font-bold">
                                    {{ number_format($stats['profit_margin']['percentage'], 1) }}%
                                </span>
                                <span class="text-sm text-gray-500 ml-2">
                                    (Rp {{ number_format($stats['profit_margin']['amount'], 0, ',', '.') }}/unit)
                                </span>
                            </p>
                        </div>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Description</h3>
                        <div class="prose max-w-none">
                            @if(isset($product->description) && $product->description)
                                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                            @else
                                <p class="text-gray-400 italic">No description provided.</p>
                                {{-- Add description button for those who can edit --}}
                                @can('update', $product)
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Add description
                                    </a>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SIDEBAR: Stock History & Actions --}}
        <div class="space-y-6">
            
            {{-- Stock Level Progress Bar --}}
            <div class="bg-white shadow rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Stock Level</h3>
                
                <div class="mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Current: {{ $product->current_stock ?? 0 }}</span>
                        <span class="text-sm font-medium text-gray-700">Min: {{ $product->min_stock ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full 
                            @if($stockPercentage <= 25) bg-red-600
                            @elseif($stockPercentage <= 50) bg-yellow-600
                            @else bg-green-600
                            @endif" 
                            style="width: {{ min($stockPercentage, 100) }}%"></div>
                    </div>
                </div>
                
                {{-- Stock Status --}}
                <div class="p-3 rounded-lg 
                    @if($stockStatusColor === 'red') bg-red-100 text-red-800
                    @elseif($stockStatusColor === 'yellow') bg-yellow-100 text-yellow-800
                    @elseif($stockStatusColor === 'green') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($stockStatusIcon == 'x-circle')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @elseif($stockStatusIcon == 'exclamation')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.342 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @endif
                        </svg>
                        <span class="font-medium">
                            {{ $stockStatusText }}
                        </span>
                    </div>
                </div>
                
                {{-- Restock Button for Manager --}}
                @can('create', App\Models\RestockOrder::class)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('restocks.create') }}?product_id={{ $product->id }}"
                           class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Restock Order
                        </a>
                    </div>
                @endcan
            </div>

            {{-- Recent Transactions --}}
            <div class="bg-white shadow rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Transactions</h3>
                
                @php
                    // Default empty array jika $recent_transactions tidak ada
                    $recent_transactions = $recent_transactions ?? collect();
                @endphp
                
                @if($recent_transactions->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_transactions as $transaction)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs px-2 py-1 rounded 
                                    @if(($transaction->type ?? '') === 'incoming') bg-green-100 text-green-800 
                                    @else bg-blue-100 text-blue-800 
                                    @endif">
                                    {{ ucfirst($transaction->type ?? 'unknown') }}
                                </span>
                                <span class="text-sm font-medium">{{ $transaction->quantity ?? 0 }} {{ $product->unit ?? 'pcs' }}</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                @if(isset($transaction->date) || isset($transaction->created_at))
                                    {{ \Carbon\Carbon::parse($transaction->date ?? $transaction->created_at)->format('M d, H:i') }}
                                @endif
                                {{-- Gunakan creator relationship --}}
                                @if(isset($transaction->creator) && $transaction->creator)
                                    • by {{ $transaction->creator->name }}
                                @endif
                            </div>
                        </div>
                        @if(isset($transaction->id))
                            {{-- Hanya tampilkan view jika user punya permission --}}
                            @can('view', $transaction)
                                <a href="{{ route('transactions.show', $transaction->id) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-sm">
                                    View
                                </a>
                            @endcan
                        @endif
                    </div>
                    @endforeach
                    
                    <div class="text-center pt-2">
                        @php
                            $user = auth()->user();
                            $params = ['search' => $product->sku];
                            
                            if ($user->isStaff()) {
                                $link = route('transactions.history', $params);
                            } elseif ($user->isSupplier()) {
                                $link = route('transactions.supplier.dashboard', $params);
                            } else {
                                $link = route('transactions.index', $params);
                            }
                        @endphp
                        
                        <a href="{{ $link }}"
                        class="text-sm text-indigo-600 hover:text-indigo-800">
                            View all transactions →
                        </a>
                    </div>
                </div>
                @else
                <div class="text-center py-4">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No recent transactions</p>
                    
                    {{-- Quick action untuk create transaction jika bisa --}}
                    @can('create', App\Models\Transaction::class)
                        <div class="mt-4">
                            <a href="{{ route('transactions.create.outgoing') }}?product_id={{ $product->id }}"
                               class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Create Transaction
                            </a>
                        </div>
                    @endcan
                </div>
                @endif
            </div>

            {{-- Quick Actions Card --}}
            <div class="bg-white shadow rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    {{-- EDIT PRODUCT --}}
                    @can('update', $product)
                        <a href="{{ route('products.edit', $product) }}"
                           class="w-full flex items-center justify-between px-4 py-3 border border-yellow-300 rounded-md hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Edit Product</span>
                            </div>
                            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <div class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-md bg-gray-50 opacity-50 cursor-not-allowed">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-500">Edit Product</span>
                            </div>
                            <span class="text-xs text-gray-500 px-2 py-1 bg-gray-200 rounded">Admin/Manager Only</span>
                        </div>
                    @endcan
                    
                    {{-- ADJUST STOCK --}}
                    @can('update', $product)
                        <a href="{{ route('products.adjust-stock', $product) }}"
                           class="w-full flex items-center justify-between px-4 py-3 border border-blue-300 rounded-md hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Adjust Stock</span>
                            </div>
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endcan
                    
                    {{-- VIEW CATEGORY --}}
                    @if(isset($product->category) && $product->category)
                        @can('view', $product->category)
                            <a href="{{ route('categories.show', $product->category) }}"
                               class="w-full flex items-center justify-between px-4 py-3 border border-indigo-300 rounded-md hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">View Category</span>
                                </div>
                                <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection