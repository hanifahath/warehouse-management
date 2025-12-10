@extends('layouts.app')
@section('title', 'Approval Transaksi')
@section('content')
<div class="p-6">
    @if ($transactions->count())
                    @foreach ($transactions as $transaction)
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-800">
                                    <form action="{{ route('transactions.verify', $transaction) }}" method="POST" class="inline">
                                    <form action="{{ route('transactions.approve', $transaction) }}" method="POST" class="inline">
                                <a href="{{ route('transactions.show', $transaction) }}" 
                                    <form action="{{ route('transactions.verify', $transaction) }}" method="POST" class="inline">
                                    <form action="{{ route('transactions.approve', $transaction) }}" method="POST" class="inline">
@extends('layouts.app')

@section('title', 'Approval Transaksi')

@section('content')
<div class="p-6">
    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Total Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['total_pending'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Pending Masuk</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['pending_incoming'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Pending Keluar</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['pending_outgoing'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <p class="text-sm text-gray-500">Disetujui Hari Ini</p>
            <p class="text-2xl font-bold text-green-600">
                {{ ($stats['verified_today'] ?? 0) + ($stats['approved_today'] ?? 0) }}
            </p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-6 shadow-lg rounded-xl mb-6">
        <form action="{{ route('transactions.pending.approvals') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4 items-end">
            <div class="flex-1">
                <label for="q" class="block text-sm font-medium text-gray-700">Cari Transaksi</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}"
                       placeholder="No. Transaksi atau Nama..."
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
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150">
                    Filter
                </button>
                <a href="{{ route('transactions.pending.approvals') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-150">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if ($transactions->count())
    <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan/Supplier</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($transactions as $transaction)
                    <tr class="hover:bg-yellow-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $transaction->transaction_number }}
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $transaction->created_at->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $transaction->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaction->type === 'Incoming')
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    Masuk
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    Keluar
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($transaction->type === 'Incoming')
                                {{ $transaction->supplier?->name ?? 'N/A' }}
                            @else
                                {{ $transaction->customer_name ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->creator->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                            <span class="font-semibold">{{ $transaction->items->count() }}</span> item
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('transactions.show', $transaction) }}" 
                                   class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition duration-150 font-medium">
                                    Review
                                </a>
                                @if($transaction->type === 'Incoming')
                                    <form action="{{ route('transactions.verify', $transaction) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Verifikasi transaksi barang masuk ini? Stok akan ditambahkan.')"
                                                class="px-3 py-1.5 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition duration-150 font-medium">
                                            Verify
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('transactions.approve', $transaction) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Setujui transaksi barang keluar ini? Stok akan dikurangi.')"
                                                class="px-3 py-1.5 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition duration-150 font-medium">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                                <button type="button" 
                                        onclick="showRejectModal('{{ $transaction->id }}')"
                                        class="px-3 py-1.5 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition duration-150 font-medium">
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION -->
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-lg shadow">
        <div class="flex-1 flex justify-between sm:hidden">
            @if ($transactions->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                    Previous
                </span>
            @else
                <a href="{{ $transactions->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </a>
            @endif
            
            @if ($transactions->hasMorePages())
                <a href="{{ $transactions->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </a>
            @else
                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>
        
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Menampilkan
                    <span class="font-medium">{{ $transactions->firstItem() }}</span>
                    sampai
                    <span class="font-medium">{{ $transactions->lastItem() }}</span>
                    dari
                    <span class="font-medium">{{ $transactions->total() }}</span>
                    hasil
                </p>
            </div>
            
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    {{-- Previous Page Link --}}
                    @if ($transactions->onFirstPage())
                        <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-400 cursor-not-allowed">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $transactions->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                        @if ($page == $transactions->currentPage())
                            <span aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($transactions->hasMorePages())
                        <a href="{{ $transactions->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-400 cursor-not-allowed">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </nav>
            </div>
        </div>
    </div>
    <!-- END PAGINATION -->

    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="mt-4 text-lg text-gray-500">Tidak ada transaksi pending untuk disetujui.</p>
            @if(request()->hasAny(['q', 'type']))
                <p class="mt-2 text-sm text-gray-400">Coba ubah filter pencarian Anda</p>
            @endif
        </div>
    @endif
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <form id="rejectForm" method="POST">
                @csrf
                @method('POST')
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tolak Transaksi</h3>
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="4"
                                  class="w-full border border-gray-300 rounded-lg p-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Berikan alasan penolakan..."
                                  required></textarea>
                        @error('rejection_reason')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="hideRejectModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-150">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-150">
                            Tolak Transaksi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Tambahkan transactionId ke form action
function showRejectModal(transactionId) {
    const form = document.getElementById('rejectForm');
    form.action = `/transactions/${transactionId}/reject`;
    document.getElementById('rejection_reason').value = '';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target.id === 'rejectModal') {
        hideRejectModal();
    }
});
</script>
@endsection