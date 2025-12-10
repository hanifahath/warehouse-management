@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Laporan Transaksi</h1>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Total Transaksi</p>
            <p class="text-2xl font-bold">{{ $stats['total_transactions'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Barang Masuk</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['total_incoming'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Barang Keluar</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_outgoing'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['total_pending'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white p-6 shadow-lg rounded-xl mb-6">
        <form action="{{ route('reports.transactions') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4 items-end">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                       class="mt-1 block w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                       class="mt-1 block w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Jenis</label>
                <select id="type" name="type"
                        class="mt-1 block w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="Incoming" {{ request('type') == 'Incoming' ? 'selected' : '' }}>Barang Masuk</option>
                    <option value="Outgoing" {{ request('type') == 'Outgoing' ? 'selected' : '' }}>Barang Keluar</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status"
                        class="mt-1 block w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Verified" {{ request('status') == 'Verified' ? 'selected' : '' }}>Verified</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Shipped" {{ request('status') == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150">Filter</button>
                <a href="{{ route('reports.transactions') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-150">Reset</a>
            </div>
        </form>
    </div>

    @if ($transactions->count())
    <div class="overflow-x-auto bg-white shadow-lg rounded-xl">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-5 py-3 border-b-2 border-gray-200">No. Transaksi</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Tanggal</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Jenis</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Pelanggan/Supplier</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Staff</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Jumlah Item</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-5 py-5 border-b border-gray-200 text-sm font-medium">
                        <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $transaction->transaction_number }}
                        </a>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                        @if($transaction->type === 'Incoming')
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                Masuk
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                Keluar
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                        @if($transaction->type === 'Incoming')
                            {{ $transaction->supplier?->name ?? 'N/A' }}
                        @else
                            {{ $transaction->customer_name ?? 'N/A' }}
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">{{ $transaction->creator->name ?? 'System' }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                        <span class="font-bold">{{ $transaction->items->count() }}</span> item
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                        @php
                            $color = match($transaction->status) {
                                'Pending' => 'yellow',
                                'Verified' => 'green',
                                'Approved' => 'blue',
                                'Completed' => 'green',
                                'Shipped' => 'purple',
                                default => 'gray',
                            };
                        @endphp
                        <span class="px-3 py-1 text-xs font-semibold leading-tight text-{{ $color }}-700 bg-{{ $color }}-100 rounded-full">
                            {{ $transaction->status }}
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                <a href="{{ route('transactions.show', $transaction) }}" 
                                    class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition duration-150">
                            Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transactions->withQueryString()->links() }}
    </div>

    @else
        <div class="text-center py-10 bg-white rounded-lg shadow">
            <p class="text-gray-500">Tidak ada data transaksi ditemukan berdasarkan filter ini.</p>
            @if(request()->hasAny(['start_date', 'end_date', 'type', 'status']))
                <p class="text-sm text-gray-400 mt-2">Coba ubah filter pencarian Anda</p>
            @endif
        </div>
    @endif
</div>
@endsection