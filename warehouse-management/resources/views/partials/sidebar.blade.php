<aside class="w-64 bg-gray-800 text-white min-h-screen p-4">
    <ul class="space-y-2">
        <li><a href="{{ route('dashboard') }}" class="block hover:bg-gray-700 p-2 rounded">Dashboard</a></li>

        @role('Admin')
            <!-- Admin Section -->
            <li><a href="{{ route('admin.users.index') }}" class="block hover:bg-gray-700 p-2 rounded">Users</a></li>
            <!-- PERBAIKAN: Menggunakan 'admin.products.index' -->
            <li><a href="{{ route('admin.products.index') }}" class="block hover:bg-gray-700 p-2 rounded">Products</a></li>
            <!-- PERBAIKAN: Menggunakan 'admin.categories.index' -->
            <li><a href="{{ route('admin.categories.index') }}" class="block hover:bg-gray-700 p-2 rounded">Categories</a></li>
            <li><a href="{{ route('restocks.create') }}" class="block hover:bg-gray-700 p-2 rounded">Create Restock Order</a></li>
        @endrole

        @role('Manager')
            <!-- Manager Section -->
            <li><a href="{{ route('manager.reports.inventory') }}" class="block hover:bg-gray-700 p-2 rounded">Reports</a></li>
            <!-- PERBAIKAN: Menggunakan 'staff.transactions.index' karena route ini berada di luar grup manager -->
            <li><a href="{{ route('staff.transactions.index') }}" class="block hover:bg-gray-700 p-2 rounded">Transactions</a></li>
            <!-- PERBAIKAN: Menggunakan 'restocks.create' untuk membuat pesanan restock -->
            <li><a href="{{ route('restocks.create') }}" class="block hover:bg-gray-700 p-2 rounded">Create Restock Order</a></li>
            <li><a href="{{ route('admin.products.index') }}" class="block hover:bg-gray-700 p-2 rounded">Products (CRUD)</a></li>
        @endrole

        @role('Staff')
            <!-- Staff Section -->
            <li><a href="{{ route('staff.transactions.index') }}" class="block hover:bg-gray-700 p-2 rounded">All Transactions</a></li>
            <li><a href="{{ route('staff.transactions.create_incoming') }}" class="block hover:bg-gray-700 p-2 rounded">Incoming Stock</a></li>
            <li><a href="{{ route('staff.transactions.create_outgoing') }}" class="block hover:bg-gray-700 p-2 rounded">Outgoing Stock</a></li>
            <li><a href="{{ route('supplier.restocks.index') }}" class="block hover:bg-gray-700 p-2 rounded">Receive Restock</a></li>
        @endrole

        @role('Supplier')
            <!-- Supplier Section -->
            <li><a href="{{ route('supplier.restocks.index') }}" class="block hover:bg-gray-700 p-2 rounded">My Restock Orders</a></li>
        @endrole
    </ul>
</aside>