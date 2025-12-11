@extends('layouts.app')

@section('title', $product->name . ' - Product Details')

@section('content')
<div class="page-container">

    {{-- HEADER --}}
    <div class="header-section">
        <div>
            <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                <ol class="breadcrumb-list">
                    <li class="breadcrumb-item">
                        <a href="{{ route('products.index') }}" 
                           class="breadcrumb-link">
                            <svg class="icon-sm icon-mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            Products
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="icon-lg text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $product->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="page-title">{{ $product->name }}</h1>
        </div>

        <div class="action-buttons-group">
            
            @can('delete', $product)
                <form method="POST" action="{{ route('products.destroy', $product) }}"
                      onsubmit="return confirm('Delete product {{ $product->name }}? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button-danger">
                        Delete
                    </button>
                </form>
            @endcan
            
            @can('update', $product)
                <a href="{{ route('products.edit', $product) }}"
                   class="button-primary">
                    Edit Product
                </a>
            @endcan
            
        </div>
    </div>
    
    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="alert-success">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    {{-- QUICK STATS --}}
    @php
        $purchasePrice = $product->purchase_price ?? 0;
        $sellingPrice = $product->selling_price ?? 0;
        $currentStock = $product->current_stock ?? 0;
        $minStock = $product->min_stock ?? 0;

        $inventoryValue = $currentStock * $sellingPrice;
        
        $stockPercentage = $minStock > 0 
            ? min(($currentStock / $minStock) * 100, 100) 
            : 0;
            
        $stockStatusColor = 'gray';
        $stockStatusText = 'Normal';
        $stockStatusIcon = 'check-circle';
        $stockIconClasses = 'bg-green-100 text-green-800';

        if ($currentStock <= 0) {
            $stockStatusColor = 'red';
            $stockStatusText = 'Out of Stock';
            $stockStatusIcon = 'x-circle';
            $stockIconClasses = 'bg-red-100 text-red-800';
        } elseif ($currentStock <= $minStock) {
            $stockStatusColor = 'yellow';
            $stockStatusText = 'Low Stock';
            $stockStatusIcon = 'exclamation';
            $stockIconClasses = 'bg-yellow-100 text-yellow-800';
        }
        
        $profitAmount = $sellingPrice - $purchasePrice;
        $profitPercentage = $purchasePrice > 0 ? ($profitAmount / $purchasePrice) * 100 : 0;
        $isProfitPositive = $profitAmount >= 0;

        // Merge $stats from controller with calculated values (for completeness as per original code)
        $stats = array_merge([
            'inventory_value' => $inventoryValue,
            'profit_margin' => [
                'is_positive' => $isProfitPositive,
                'percentage' => $profitPercentage,
                'amount' => $profitAmount
            ]
        ], $stats ?? []);
    @endphp

    <div class="stats-grid">
        
        {{-- Stock Card --}}
        <div class="stat-card">
            <div class="flex items-center">
                <div class="stat-icon-wrapper {{ $stockIconClasses }}">
                    <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="stat-label">Current Stock</p>
                    <p class="stat-value">{{ $currentStock }}</p>
                    <p class="stat-unit">{{ $product->unit ?? 'pcs' }}</p>
                </div>
            </div>
        </div>

        {{-- Min Stock Card --}}
        <div class="stat-card">
            <div class="flex items-center">
                <div class="stat-icon-wrapper bg-blue-100 text-blue-600">
                    <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="stat-label">Minimum Stock</p>
                    <p class="stat-value">{{ $minStock }}</p>
                    <p class="stat-unit">Alert threshold</p>
                </div>
            </div>
        </div>

        {{-- Inventory Value Card --}}
        <div class="stat-card">
            <div class="flex items-center">
                <div class="stat-icon-wrapper bg-purple-100 text-purple-600">
                    <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="stat-label">Inventory Value</p>
                    <p class="stat-value-lg">
                        Rp {{ number_format($stats['inventory_value'], 0, ',', '.') }}
                    </p>
                    <p class="stat-unit">@ Rp {{ number_format($sellingPrice, 0, ',', '.') }}/unit</p>
                </div>
            </div>
        </div>

        {{-- Status Card --}}
        <div class="stat-card">
            <div class="flex items-center">
                <div class="stat-icon-wrapper {{ ($product->is_active ?? true) ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                    <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="stat-label">Status</p>
                    <p class="stat-value-lg">
                        {{ ($product->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </p>
                    <p class="stat-unit">
                        {{ $stockStatusText }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="main-content-grid">
        
        {{-- PRODUCT DETAILS --}}
        <div class="lg:col-span-2">
            <div class="detail-panel">
                
                {{-- Product Image --}}
                @if(isset($product->image_path) && $product->image_path)
                <div class="mb-6">
                    <div class="product-image-wrapper">
                        <img src="{{ asset('storage/' . $product->image_path) }}"
                             alt="{{ $product->name }}"
                             class="product-image">
                        <div class="product-sku-badge-position">
                            <span class="product-sku-badge">
                                {{ $product->sku }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- DETAILS GRID --}}
                <div class="detail-sub-grid">
                    
                    {{-- Left Column: Basic Info --}}
                    <div class="detail-column-space">
                        <h3 class="detail-heading">Basic Information</h3>
                        
                        <div>
                            <label class="detail-label">SKU</label>
                            <p class="detail-text font-mono">{{ $product->sku ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="detail-label">Category</label>
                            <p class="mt-1 text-gray-900">
                                @if(isset($product->category) && $product->category)
                                    @can('view', $product->category)
                                        <a href="{{ route('categories.show', $product->category) }}"
                                           class="category-tag-link">
                                            {{ $product->category->name }}
                                        </a>
                                    @else
                                        <span class="category-tag-span">
                                            {{ $product->category->name }}
                                        </span>
                                    @endcan
                                @else
                                    <span class="text-gray-400">Uncategorized</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="detail-label">Unit</label>
                            <p class="detail-text">{{ $product->unit ?? 'pcs' }}</p>
                        </div>
                        
                        <div>
                            <label class="detail-label">Created At</label>
                            <p class="detail-text">{{ isset($product->created_at) ? $product->created_at->format('d M Y, H:i') : 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="detail-label">Last Updated</label>
                            <p class="detail-text">{{ isset($product->updated_at) ? $product->updated_at->format('d M Y, H:i') : 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- Right Column: Pricing & Location --}}
                    <div class="detail-column-space">
                        <h3 class="detail-heading">Pricing & Location</h3>
                        
                        <div>
                            <label class="detail-label">Rack Location</label>
                            <p class="mt-1 text-gray-900">
                                @if(isset($product->rack_location) && $product->rack_location)
                                    <span class="rack-location-badge">
                                        {{ $product->rack_location }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="detail-label">Purchase Price</label>
                            <p class="detail-text-lg font-bold">
                                Rp {{ number_format($purchasePrice, 0, ',', '.') }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="detail-label">Selling Price</label>
                            <p class="detail-text-lg font-bold text-green-600">
                                Rp {{ number_format($sellingPrice, 0, ',', '.') }}
                            </p>
                        </div>
                        
                        {{-- Profit Margin --}}
                        <div>
                            <label class="detail-label">Profit Margin</label>
                            <p class="mt-1 text-gray-900">
                                <span class="{{ $isProfitPositive ? 'text-green-600' : 'text-red-600' }} font-bold">
                                    {{ number_format($stats['profit_margin']['percentage'], 1) }}%
                                </span>
                                <span class="text-sm text-gray-500 ml-2">
                                    (Rp {{ number_format($stats['profit_margin']['amount'], 0, ',', '.') }}/unit)
                                </span>
                            </p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <h3 class="detail-heading mb-4">Description</h3>
                        <div class="prose max-w-none">
                            @if(isset($product->description) && $product->description)
                                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                            @else
                                <p class="text-gray-400 italic">No description provided.</p>
                                @can('update', $product)
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="no-description-link">
                                        <svg class="icon-sm icon-mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="sidebar-column">
            
            {{-- Stock Level Progress Bar --}}
            <div class="detail-panel">
                <h3 class="detail-heading mb-4">Stock Level</h3>
                
                <div class="mb-4">
                    <div class="progress-bar-labels">
                        <span class="text-sm font-medium text-gray-700">Current: {{ $currentStock }}</span>
                        <span class="text-sm font-medium text-gray-700">Min: {{ $minStock }}</span>
                    </div>
                    <div class="progress-bar-container">
                        @php
                            $progressBarColor = 'bg-green-600';
                            if ($stockPercentage <= 25) $progressBarColor = 'bg-red-600';
                            elseif ($stockPercentage <= 50) $progressBarColor = 'bg-yellow-600';
                        @endphp
                        <div class="progress-bar-fill {{ $progressBarColor }}" 
                             style="width: {{ min($stockPercentage, 100) }}%"></div>
                    </div>
                </div>
                
                {{-- Stock Status Display --}}
                <div class="stock-status-box 
                    @if($stockStatusColor === 'red') bg-red-100 text-red-800
                    @elseif($stockStatusColor === 'yellow') bg-yellow-100 text-yellow-800
                    @elseif($stockStatusColor === 'green') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    <div class="flex items-center">
                        <svg class="icon-sm icon-mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                           class="restock-button">
                            <svg class="icon-sm icon-mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Restock Order
                        </a>
                    </div>
                @endcan
            </div>

            {{-- Recent Transactions --}}
            <div class="detail-panel">
                <h3 class="detail-heading mb-4">Recent Transactions</h3>
                
                @php
                    $recent_transactions = $recent_transactions ?? collect();
                @endphp
                
                @if($recent_transactions->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_transactions as $transaction)
                    <div class="transaction-item">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="transaction-type-badge 
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
                                @if(isset($transaction->creator) && $transaction->creator)
                                    • by {{ $transaction->creator->name }}
                                @endif
                            </div>
                        </div>
                        @if(isset($transaction->id))
                            @can('view', $transaction)
                                <a href="{{ route('transactions.show', $transaction->id) }}"
                                   class="text-link-sm">
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
                            
                            $link = route('transactions.index', $params); // Default
                            if ($user && method_exists($user, 'isStaff') && $user->isStaff()) {
                                $link = route('transactions.history', $params);
                            } elseif ($user && method_exists($user, 'isSupplier') && $user->isSupplier()) {
                                $link = route('transactions.supplier.dashboard', $params);
                            }
                        @endphp
                        
                        <a href="{{ $link }}" class="text-link">
                            View all transactions →
                        </a>
                    </div>
                </div>
                @else
                <div class="text-center py-4">
                    <svg class="icon-xl text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No recent transactions</p>
                    
                    @can('create', App\Models\Transaction::class)
                        <div class="mt-4">
                            <a href="{{ route('transactions.create.outgoing') }}?product_id={{ $product->id }}"
                               class="button-secondary-indigo">
                                <svg class="icon-sm icon-mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="detail-panel">
                <h3 class="detail-heading mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    {{-- EDIT PRODUCT --}}
                    @can('update', $product)
                        <a href="{{ route('products.edit', $product) }}"
                           class="quick-action-button button-yellow">
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
                        <div class="quick-action-disabled">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-500">Edit Product</span>
                            </div>
                            <span class="permission-badge-gray">Admin/Manager Only</span>
                        </div>
                    @endcan
                    
                    {{-- VIEW CATEGORY --}}
                    @if(isset($product->category) && $product->category)
                        @can('view', $product->category)
                            <a href="{{ route('categories.show', $product->category) }}"
                               class="quick-action-button button-indigo">
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