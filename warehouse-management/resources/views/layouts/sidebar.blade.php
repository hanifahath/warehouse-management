<aside class="w-64 h-screen bg-white border-r border-gray-200">
    <nav class="p-4 space-y-2">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 
           {{ request()->is('dashboard') ? 'bg-indigo-600 text-white' : '' }}">
            Dashboard
        </a>

        {{-- Product --}}
        <a href="{{ route('admin.products.index') }}"
           class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
           {{ request()->is('products*') ? 'bg-indigo-600 text-white' : '' }}">
            Products
        </a>

        {{-- Categories --}}
        <a href="{{ route('admin.categories.index') }}"
           class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
           {{ request()->is('categories*') ? 'bg-indigo-600 text-white' : '' }}">
            Categories
        </a>

        {{-- Users Management (TAMBAHAN BARU) --}}
        <a href="{{ route('admin.users.index') }}"
           class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
           {{ request()->is('users*') ? 'bg-indigo-600 text-white' : '' }}">
            Users Management
        </a>

        {{-- Transactions --}}
        <a href="{{ route('manager.transactions.index') }}"
           class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
           {{ request()->is('transactions*') ? 'bg-indigo-600 text-white' : '' }}">
            Transactions
        </a>

        {{-- Restock Orders --}}
        <a href="{{ route('supplier.restocks.index') }}"
           class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100
           {{ request()->is('restock-orders*') ? 'bg-indigo-600 text-white' : '' }}">
            Restock Orders
        </a>

    </nav>
</aside>