<nav class="bg-white shadow px-6 py-3 flex justify-between items-center">
    <div class="text-xl font-bold">Warehouse CMS</div>
    <div>
        <span class="mr-4">{{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:underline">Logout</button>
        </form>
    </div>
</nav>