<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard (Role: {{ $role }})
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                
                <!-- --- NAVIGASI UTAMA BERDASARKAN ROLE --- -->
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Navigasi Sistem</h3>
                <nav class="flex flex-wrap gap-4 mb-6">
                    
                    {{-- 1. Akses Admin & Manager: Produk & PO --}}
                    @if (in_array($role, ['Admin', 'Manager']))
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            Manajemen Produk
                        </x-nav-link>
                        <x-nav-link :href="route('restocks.create')" :active="request()->routeIs('restocks.create')">
                            Buat PO Restock
                        </x-nav-link>
                    @endif

                    {{-- 2. Akses Admin: Pengguna --}}
                    @if ($role === 'Admin')
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            Manajemen User & Supplier
                        </x-nav-link>
                    @endif

                    {{-- 3. Akses Staff, Manager, Admin: Transaksi Gudang (LOGIC BARU DI SINI) --}}
                    @if (in_array($role, ['Admin', 'Manager', 'Staff']))
                        
                        {{-- Transaksi INDEX Default (Semua Transaksi) - Terlihat oleh semua role gudang --}}
                        <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.index') && !request()->query('type') && !request()->routeIs('transactions.create_*')">
                            Semua Transaksi
                        </x-nav-link>

                        @if (in_array($role, ['Admin', 'Manager']))
                            {{-- Admin & Manager: Melihat daftar yang difilter --}}
                            <x-nav-link :href="route('transactions.index', ['type' => 'Incoming'])" :active="request()->routeIs('transactions.index') && request()->query('type') === 'Incoming'">
                                Transaksi Masuk
                            </x-nav-link>
                            <x-nav-link :href="route('transactions.index', ['type' => 'Outgoing'])" :active="request()->routeIs('transactions.index') && request()->query('type') === 'Outgoing'">
                                Transaksi Keluar
                            </x-nav-link>
                        @elseif ($role === 'Staff')
                            {{-- Staff: Melihat form pembuatan (karena Staff yang bertugas membuat transaksi) --}}
                            <x-nav-link :href="route('transactions.create_incoming')" :active="request()->routeIs('transactions.create_incoming')">
                                Transaksi Masuk (Buat)
                            </x-nav-link>
                            <x-nav-link :href="route('transactions.create_outgoing')" :active="request()->routeIs('transactions.create_outgoing')">
                                Transaksi Keluar (Buat)
                            </x-nav-link>
                        @endif
                    @endif
                    
                    {{-- 4. Akses Semua Role Restock: Lihat Status PO --}}
                    @if (in_array($role, ['Admin', 'Manager', 'Staff', 'Supplier']))
                        <x-nav-link :href="route('restocks.index')" :active="request()->routeIs('restocks.index')">
                            Semua Restock Orders
                        </x-nav-link>
                    @endif

                </nav>

                <!-- --- RINGKASAN DASHBOARD BERDASARKAN ROLE --- -->
                <h3 class="text-lg font-bold mb-4 border-b pb-2 mt-8">Statistik Cepat</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    {{-- Statistik untuk Admin dan Manager --}}
                    @if (in_array($role, ['Admin', 'Manager']))
                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded shadow">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Produk</p>
                            <p class="text-2xl font-bold">{{ $stats['total_products'] ?? 0 }}</p>
                        </div>
                        <div class="p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 rounded shadow">
                            <p class="text-sm">Stok Rendah (Low Stock)</p>
                            <p class="text-2xl font-bold">{{ $stats['low_stock_count'] ?? 0 }}</p>
                        </div>
                        <div class="p-4 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 rounded shadow">
                            <p class="text-sm">Transaksi Pending Approval</p>
                            <p class="text-2xl font-bold">{{ $stats['pending_transactions'] ?? 0 }}</p>
                        </div>
                        @if ($role === 'Admin')
                        <div class="p-4 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300 rounded shadow col-span-full">
                            <p class="text-sm">Supplier Menunggu Approval</p>
                            <p class="text-2xl font-bold">{{ $stats['unapproved_suppliers'] ?? 0 }}</p>
                        </div>
                        @endif

                    {{-- Statistik untuk Staff --}}
                    @elseif ($role === 'Staff')
                        <div class="p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded shadow">
                            <p class="text-sm">Transaksi Dibuat Hari Ini</p>
                            <p class="text-2xl font-bold">{{ $stats['total_transactions_today'] ?? 0 }}</p>
                        </div>
                        <div class="p-4 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 rounded shadow">
                            <p class="text-sm">PO Siap Diterima (Receive)</p>
                            <p class="text-2xl font-bold">{{ $stats['restock_to_receive'] ?? 0 }}</p>
                        </div>

                    {{-- Statistik untuk Supplier --}}
                    @elseif ($role === 'Supplier')
                        <div class="p-4 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-300 rounded shadow">
                            <p class="text-sm">PO Menunggu Konfirmasi Anda</p>
                            <p class="text-2xl font-bold">{{ $stats['pending_confirmation'] ?? 0 }}</p>
                        </div>
                        <div class="p-4 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300 rounded shadow">
                            <p class="text-sm">Total PO Keseluruhan</p>
                            <p class="text-2xl font-bold">{{ $stats['total_orders'] ?? 0 }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout>