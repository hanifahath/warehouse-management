@extends('layouts.app')

@section('title', 'Detail Transaksi - ' . $transaction->transaction_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Transaksi</h1>
            <p class="text-gray-600 mt-1">#{{ $transaction->transaction_number }}</p>
        </div>
        <div>
            <a href="{{ route('transactions.pending.approvals') }}" 
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-150">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- Transaction Info --}}
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Jenis Transaksi</h3>
                <p class="mt-1 text-lg font-semibold">
                    @if($transaction->type === 'Incoming')
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full">
                            Barang Masuk
                        </span>
                    @else
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                            Barang Keluar
                        </span>
                    @endif
                </p>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <p class="mt-1">
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
                    <span class="px-3 py-1 text-sm font-semibold leading-tight text-{{ $color }}-700 bg-{{ $color }}-100 rounded-full">
                        {{ $transaction->status }}
                    </span>
                </p>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500">Tanggal</h3>
                <p class="mt-1 text-lg">{{ $transaction->created_at->format('d F Y H:i') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">
                    @if($transaction->type === 'Incoming')
                        Supplier
                    @else
                        Customer
                    @endif
                </h3>
                <p class="mt-1 text-lg">
                    @if($transaction->type === 'Incoming')
                        {{ $transaction->supplier?->name ?? 'N/A' }}
                    @else
                        {{ $transaction->customer_name ?? 'N/A' }}
                    @endif
                </p>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500">Dibuat Oleh</h3>
                <p class="mt-1 text-lg">{{ $transaction->creator->name ?? 'System' }}</p>
            </div>
        </div>

        @if($transaction->notes)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Catatan</h3>
                <p class="mt-1 p-3 bg-gray-50 rounded-lg">{{ $transaction->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Items Table --}}
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Detail Item</h2>
        
        @if($transaction->items->count())
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-4 py-3 border-b-2 border-gray-200">Produk</th>
                            <th class="px-4 py-3 border-b-2 border-gray-200 text-center">SKU</th>
                            <th class="px-4 py-3 border-b-2 border-gray-200 text-center">Kuantitas</th>
                            <th class="px-4 py-3 border-b-2 border-gray-200 text-center">Harga Satuan</th>
                            <th class="px-4 py-3 border-b-2 border-gray-200 text-center">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">{{ $item->product->name ?? 'Produk Dihapus' }}</p>
                                            @if($item->product)
                                                <p class="text-sm text-gray-500">{{ $item->product->category->name ?? '' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200 text-center text-sm">
                                    {{ $item->product->sku ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200 text-center font-bold">
                                    {{ number_format($item->quantity) }}
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200 text-center">
                                    Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200 text-center font-bold">
                                    Rp {{ number_format(($item->quantity * ($item->price ?? 0)), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        
                        {{-- Total Row --}}
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-4 py-3 text-right font-bold">Total</td>
                            <td class="px-4 py-3 text-center font-bold text-lg">
                                Rp {{ number_format($transaction->items->sum(fn($item) => $item->quantity * ($item->price ?? 0)), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center py-4 text-gray-500">Tidak ada item dalam transaksi ini.</p>
        @endif
    </div>

    {{-- Approval Actions --}}
    @if($transaction->status === 'Pending')
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Approval Action</h2>
            
            <div class="flex flex-wrap gap-4">
                @if($transaction->type === 'Incoming')
                    {{-- Verify Incoming Transaction --}}
                    <form action="{{ route('transactions.verify', $transaction) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Verifikasi transaksi barang masuk ini? Stok akan ditambahkan.')"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-150 font-medium">
                            ✅ Verify & Tambah Stok
                        </button>
                    </form>
                @else
                    {{-- Approve Outgoing Transaction --}}
                    <form action="{{ route('transactions.approve', $transaction) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Setujui transaksi barang keluar ini? Stok akan dikurangi.')"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-150 font-medium">
                            ✅ Approve & Kurangi Stok
                        </button>
                    </form>
                @endif

                {{-- Reject Button with Modal Trigger --}}
                <button type="button" 
                        onclick="showRejectModal()"
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-150 font-medium">
                    ❌ Tolak Transaksi
                </button>
            </div>

            <div class="mt-4 text-sm text-gray-600">
                <p>
                    @if($transaction->type === 'Incoming')
                        <strong>Verify:</strong> Menambahkan stok produk berdasarkan jumlah yang diterima.
                    @else
                        <strong>Approve:</strong> Mengurangi stok produk berdasarkan jumlah yang dikirim.
                    @endif
                </p>
                <p class="mt-1"><strong>Reject:</strong> Menolak transaksi tanpa mengubah stok.</p>
            </div>
        </div>
    @endif

    {{-- Status History --}}
    @if($transaction->approved_at || $transaction->approver)
        <div class="bg-white shadow-lg rounded-xl p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Status History</h2>
            <div class="space-y-3">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium">
                            @if($transaction->type === 'Incoming')
                                Diverifikasi
                            @else
                                Disetujui
                            @endif
                            oleh {{ $transaction->approver->name ?? 'System' }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $transaction->approved_at?->format('d F Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <form action="{{ route('transactions.reject', $transaction) }}" method="POST">
                @csrf
                @method('POST') {{-- Explicit method POST --}}
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
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

// Close modal on outside click
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target.id === 'rejectModal') {
        hideRejectModal();
    }
});
</script>
@endsection