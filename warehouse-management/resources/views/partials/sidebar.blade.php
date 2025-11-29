<aside class="w-64 bg-gray-800 text-white min-h-screen p-4">
    <ul class="space-y-2">
        <li><a href="{{ route('dashboard') }}" class="block hover:bg-gray-700 p-2 rounded">Dashboard</a></li>

        @role('Admin')
            <li><a href="{{ route('users.index') }}" class="block hover:bg-gray-700 p-2 rounded">Users</a></li>
            <li><a href="{{ route('products.index') }}" class="block hover:bg-gray-700 p-2 rounded">Products</a></li>
            <li><a href="{{ route('categories.index') }}" class="block hover:bg-gray-700 p-2 rounded">Categories</a></li>
        @endrole

        @role('Manager')
            <li><a href="{{ route('reports.inventory') }}" class="block hover:bg-gray-700 p-2 rounded">Reports</a></li>
            <li><a href="{{ route('transactions.index') }}" class="block hover:bg-gray-700 p-2 rounded">Transactions</a></li>
            <li><a href="{{ route('restocks.index') }}" class="block hover:bg-gray-700 p-2 rounded">Restocks</a></li>
        @endrole

        @role('Staff')
            <li><a href="{{ route('transactions.create_incoming') }}" class="block hover:bg-gray-700 p-2 rounded">Incoming</a></li>
            <li><a href="{{ route('transactions.create_outgoing') }}" class="block hover:bg-gray-700 p-2 rounded">Outgoing</a></li>
        @endrole

        @role('Supplier')
            <li><a href="{{ route('restocks.index') }}" class="block hover:bg-gray-700 p-2 rounded">Restock Orders</a></li>
        @endrole
    </ul>
</aside>