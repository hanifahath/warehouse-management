@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Transactions</h1>

        @can('create', App\Models\Transaction::class)
        <div class="flex gap-3">
            <a href="{{ route('staff.transactions.create.incoming') }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
               + Incoming
            </a>

            <a href="{{ route('staff.transactions.create.outgoing') }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
               + Outgoing
            </a>
        </div>
        @endcan
    </div>


    {{-- TAB --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-6">
            <a href="?type=incoming"
               class="pb-3 text-sm font-medium 
                      {{ request('type') != 'outgoing' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                Incoming
            </a>

            <a href="?type=outgoing"
               class="pb-3 text-sm font-medium
                      {{ request('type') == 'outgoing' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                Outgoing
            </a>
        </nav>
    </div>


    {{-- FILTERS --}}
    <form method="GET" class="mb-6">
        <input type="hidden" name="type" value="{{ request('type') }}">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- SEARCH --}}
            <div>
                <input type="text" name="search" placeholder="Search by product, sku, staff..."
                       value="{{ request('search') }}"
                       class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- DATE FILTER --}}
            <div>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- STATUS FILTER --}}
            <div>
                <select name="status"
                        class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="pending"  {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            {{-- SUBMIT --}}
            <div>
                <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                    Apply Filter
                </button>
            </div>
        </div>
    </form>


    {{-- TABLE --}}
    <div class="bg-white border border-gray-200 shadow rounded-lg overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-4 font-medium text-gray-600">ID</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Product</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Qty</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Type</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Status</th>
                    <th class="py-3 px-4 font-medium text-gray-600">Date</th>
                    <th class="py-3 px-4 font-medium text-gray-600 text-right">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">

                @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50">

                        <td class="py-3 px-4 text-gray-800">
                            #{{ $tx->id }}
                        </td>

                        <td class="py-3 px-4 text-gray-800">
                            {{ $tx->product->name }}
                            <div class="text-gray-500 text-xs">{{ $tx->product->sku }}</div>
                        </td>

                        <td class="py-3 px-4 text-gray-800">
                            {{ $tx->quantity }}
                        </td>

                        <td class="py-3 px-4">
                            @if($tx->type === 'incoming')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Incoming</span>
                            @else
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Outgoing</span>
                            @endif
                        </td>

                        <td class="py-3 px-4">
                            @if($tx->status == 'pending')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                            @elseif($tx->status == 'approved')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Approved</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Rejected</span>
                            @endif
                        </td>

                        <td class="py-3 px-4 text-gray-800">
                            {{ $tx->created_at->format('d M Y') }}
                        </td>

                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('staff.transactions.show', $tx) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm">
                                View
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>


    {{-- PAGINATION --}}
    <div class="mt-6">
        {{ $transactions->links() }}
    </div>

</div>
@endsection
