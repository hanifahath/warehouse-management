<form method="GET" action="{{ url()->current() }}" class="mb-4 flex">
    <input 
        type="text" 
        name="search" 
        value="{{ request('search') }}" 
        placeholder="Search by name or SKU..." 
        class="flex-1 border rounded-l px-3 py-2 focus:outline-none"
    >
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r">
        Search
    </button>
</form>