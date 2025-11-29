@props(['filters' => []])

<form method="GET" action="{{ url()->current() }}" class="mb-4 flex space-x-4">
    @foreach($filters as $filter)
        <div>
            <label class="block text-sm font-medium">{{ $filter }}</label>
            <select name="{{ strtolower(str_replace(' ', '_', $filter)) }}" 
                    class="border rounded px-2 py-1">
                <option value="">All</option>
                {{-- Isi opsi filter sesuai kebutuhan --}}
                @if($filter === 'Category')
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                @endif
                @if($filter === 'Status')
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="completed">Completed</option>
                @endif
                @if($filter === 'Type')
                    <option value="incoming">Incoming</option>
                    <option value="outgoing">Outgoing</option>
                @endif
            </select>
        </div>
    @endforeach

    <div class="flex items-end">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Apply
        </button>
    </div>
</form>