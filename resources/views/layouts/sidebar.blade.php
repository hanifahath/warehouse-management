<aside class="w-64 h-screen bg-white border-r border-gray-200 sticky top-0 h-screen overflow-y-auto">
    <nav class="p-4 space-y-2">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
           @if(request()->routeIs('dashboard')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        {{-- ============================================================
            PRODUCT MANAGEMENT (via ProductPolicy)
        ============================================================ --}}
        @can('viewAny', App\Models\Product::class)
            <div class="pt-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Inventory</p>
            </div>
            
            <a href="{{ route('products.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
               @if(request()->routeIs('products.*')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                Products
            </a>
        @endcan

        {{-- ============================================================
            CATEGORY MANAGEMENT (via CategoryPolicy)
        ============================================================ --}}
        @can('viewAny', App\Models\Category::class)
            <a href="{{ route('categories.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
               @if(request()->routeIs('categories.*')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Categories
            </a>
        @endcan

        {{-- ============================================================
            TRANSACTION MANAGEMENT (via TransactionPolicy)
        ============================================================ --}}
        @canany(['create', 'viewPendingApprovals', 'viewAny'], App\Models\Transaction::class)
            <div class="pt-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Transactions</p>
            </div>
            
            {{-- For Staff - Create Transactions --}}
            @can('create', App\Models\Transaction::class)
                <a href="{{ route('transactions.create.incoming') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                   @if(request()->routeIs('transactions.create.*')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Transaction
                </a>
                
                {{-- Sub-menu untuk jenis transaksi --}}
                <div class="ml-6 space-y-1">
                    <a href="{{ route('transactions.create.incoming') }}"
                       class="flex items-center px-3 py-2 text-sm rounded-lg text-gray-600 hover:bg-gray-100
                       @if(request()->routeIs('transactions.create.incoming')) bg-gray-100 text-gray-900 @endif">
                        <span class="w-2 h-2 mr-2 bg-blue-500 rounded-full"></span>
                        Incoming Stock
                    </a>
                    <a href="{{ route('transactions.create.outgoing') }}"
                       class="flex items-center px-3 py-2 text-sm rounded-lg text-gray-600 hover:bg-gray-100
                       @if(request()->routeIs('transactions.create.outgoing')) bg-gray-100 text-gray-900 @endif">
                        <span class="w-2 h-2 mr-2 bg-red-500 rounded-full"></span>
                        Outgoing Stock
                    </a>
                </div>
            @endcan

            {{-- For Staff - View Their Transactions --}}
            @can('viewHistory', App\Models\Transaction::class)
                <a href="{{ route('transactions.history') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                   @if(request()->routeIs('transactions.history')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    My Transactions
                </a>
            @endcan

            {{-- For Manager - Approve Transactions --}}
            @can('viewPendingApprovals', App\Models\Transaction::class)
                <a href="{{ route('transactions.pending.approvals') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                   @if(request()->routeIs('transactions.pending.approvals')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Pending Approvals
                    @php
                        $pendingCount = App\Models\Transaction::pending()->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-2 px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            @endcan

            {{-- For Supplier - View Their Transactions --}}
            @can('viewSupplierDashboard', App\Models\Transaction::class)
                @if(auth()->user()->isApprovedSupplier())
                    <a href="{{ route('transactions.supplier.dashboard') }}"
                       class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                       @if(request()->routeIs('transactions.supplier.dashboard')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Supplier Transactions
                    </a>
                @endif
            @endcan

            {{-- View All Transactions (Admin & Manager) --}}
            @can('viewAny', App\Models\Transaction::class)
                @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                    <a href="{{ route('transactions.index') }}"
                       class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                       @if(request()->routeIs('transactions.index')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        All Transactions
                    </a>
                @endif
            @endcan
        @endcanany

        {{-- ============================================================
            RESTOCK ORDERS (via RestockOrderPolicy)
        ============================================================ --}}
        @canany(['create', 'viewAny'], App\Models\RestockOrder::class)
            <div class="pt-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Restock</p>
            </div>
            
            {{-- For Manager - Create Restock Orders --}}
            @can('create', App\Models\RestockOrder::class)
                <a href="{{ route('restocks.create') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                   @if(request()->routeIs('restocks.create')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Restock Order
                </a>
            @endcan

            {{-- View Restock Orders --}}
            @can('viewAny', App\Models\RestockOrder::class)
                <a href="{{ route('restocks.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                   @if(request()->routeIs('restocks.index') && !request()->routeIs('restocks.create')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Restock Orders
                </a>
            @endcan

            {{-- ============================================================
                RESTOCK ORDERS (via RestockOrderPolicy) - SUPPLIER VIEW
            ============================================================ --}}
            @can('viewSupplierOrders', App\Models\RestockOrder::class)
                @if(auth()->user()->isSupplier() && auth()->user()->is_approved)
                
                @endif
            @endcan
        @endcanany

        {{-- ============================================================
            USER MANAGEMENT (via UserPolicy - Admin only)
        ============================================================ --}}
        @can('viewAny', App\Models\User::class)
            <div class="pt-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Administration</p>
            </div>
            
            <a href="{{ route('users.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
               @if(request()->routeIs('users.*')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1.205a21.997 21.997 0 00-3.432-2.432m-9 5.197v.001"/>
                </svg>
                User Management
                @php
                    $pendingSuppliers = App\Models\User::where('role', 'supplier')
                        ->where('is_approved', false)
                        ->count();
                @endphp
                @if($pendingSuppliers > 0 && auth()->user()->isAdmin())
                    <span class="ml-2 px-2 py-1 text-xs font-semibold bg-orange-100 text-orange-800 rounded-full">
                        {{ $pendingSuppliers }}
                    </span>
                @endif
            </a>
        @endcan

        {{-- ============================================================
            REPORTS (via ReportPolicy)
        ============================================================ --}}
        @canany(['viewInventory', 'viewTransactions', 'viewLowStock', 'viewAll'], App\Models\User::class)
            <div class="pt-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Reports</p>
            </div>
            
            {{-- Inventory Report --}}
            @can('viewInventory', App\Models\User::class)
                <a href="{{ route('reports.inventory') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                   @if(request()->routeIs('reports.inventory')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Inventory Report
                </a>
            @endcan

            {{-- Transactions Report --}}
            @can('viewTransactions', App\Models\User::class)
                <a href="{{ route('reports.transactions') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                   @if(request()->routeIs('reports.transactions')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Transactions Report
                </a>
            @endcan

            {{-- Low Stock Report --}}
            @can('viewLowStock', App\Models\User::class)
                <a href="{{ route('reports.low-stock') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                   @if(request()->routeIs('reports.low-stock')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.308 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Low Stock Report
                    @php
                        $lowStockCount = App\Models\Product::whereRaw('current_stock <= min_stock')
                            ->where('current_stock', '>', 0)
                            ->count();
                    @endphp
                    @if($lowStockCount > 0)
                        <span class="ml-2 px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                            {{ $lowStockCount }}
                        </span>
                    @endif
                </a>
            @endcan

            {{-- Comprehensive Report (Admin only) --}}
            @can('viewAll', App\Models\User::class)
                <a href="{{ route('reports.comprehensive') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
                   @if(request()->routeIs('reports.comprehensive')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Comprehensive Report
                </a>
            @endcan
        @endcanany

        {{-- ============================================================
            STOCK MOVEMENTS (via StockMovementPolicy)
        ============================================================ --}}
        @can('viewAny', App\Models\StockMovement::class)
            <div class="pt-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Audit Trail</p>
            </div>
            
            <a href="{{ route('stock-movements.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
               @if(request()->routeIs('stock-movements.*')) bg-indigo-50 text-indigo-700 border-r-4 border-indigo-600 @endif">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Stock Movements
            </a>
        @endcan

    </nav>
</aside>