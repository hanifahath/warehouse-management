<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TransactionController extends Controller
{
    protected $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
        // Otorisasi FIXED: Menggunakan role dengan Case-Sensitive yang benar.
        $this->middleware(['auth', 'role:Admin|Manager|Staff']); 
    }
    
    // --- FUNGSIONALITAS UMUM (Admin/Manager/Staff) ---

    /**
     * Menampilkan daftar transaksi dengan dukungan filter Tipe dan Role-Based.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        // Mendapatkan tipe filter dari URL (e.g., /transactions?type=Incoming)
        $filterType = $request->query('type');
        
        $query = Transaction::query();
        $baseTitle = 'Daftar Semua Transaksi'; // Judul default

        // 1. Filter Berbasis Role (Hanya Staff yang dibatasi)
        if (auth()->user()->role === 'Staff') {
            $query->where('created_by', auth()->id());
            $baseTitle = 'Daftar Transaksi Saya';
        }

        // 2. Filter Berbasis Tipe (dari tombol navigasi Admin/Manager)
        if ($filterType) {
            // Kita menggunakan 'Incoming' dan 'Outgoing' (case-sensitive) sesuai logic store Anda
            if ($filterType === 'Incoming') {
                $query->where('type', 'Incoming');
                $baseTitle = 'Daftar Transaksi Masuk' . (auth()->user()->role === 'Staff' ? ' (Saya)' : '');
            } elseif ($filterType === 'Outgoing') {
                $query->where('type', 'Outgoing');
                $baseTitle = 'Daftar Transaksi Keluar' . (auth()->user()->role === 'Staff' ? ' (Saya)' : '');
            }
        }

        // Eksekusi Kueri dan Pagination
        $transactions = $query->latest()->paginate(10);

        return view('transactions.index', [
            'transactions' => $transactions,
            'currentFilter' => $filterType, // Penting untuk menandai tombol aktif di View
            'title' => $baseTitle, // Judul dinamis
        ]);
    }

    /**
     * Menampilkan detail transaksi.
     */
    public function show(Transaction $transaction): View
    {
        $transaction->load(['items.product', 'creator', 'approver']); 
        
        // Otorisasi sederhana: Hanya pembuat, Manager, atau Admin yang boleh melihat
        if (auth()->user()->role === 'Staff' && $transaction->created_by !== auth()->id()) {
            abort(403, 'Akses Ditolak.');
        }

        return view('transactions.show', compact('transaction'));
    }

    // --- FUNGSIONALITAS STAFF GUDANG (MEMBUAT INCOMING) ---

    /**
     * Menampilkan form pembuatan Transaksi Masuk (Incoming).
     */
    public function createIncoming(): View
    {
        // Otorisasi Tambahan: Hanya Staff yang bisa mengakses form create
        if (auth()->user()->role !== 'Staff') {
            abort(403, 'Akses Ditolak: Hanya Staff yang bisa membuat transaksi baru.');
        }

        // Dapatkan semua Supplier yang sudah Approved
        $suppliers = User::where('role', 'Supplier')->where('is_approved', true)->get();
        $products = Product::all();

        return view('transactions.create_incoming', compact('suppliers', 'products'));
    }

    /**
     * Menyimpan Transaksi Masuk ke database (Status Pending).
     */
    public function storeIncoming(Request $request): RedirectResponse
    {
         // Otorisasi: Hanya Staff
         if (auth()->user()->role !== 'Staff') {
            abort(403, 'Akses Ditolak.');
        }

        // VALIDASI KRITIS
        $request->validate([
            'supplier_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_at_transaction' => 'required|numeric|min:0', // Harga Beli
        ]);
        
        // 1. Buat Header Transaksi
        $transaction = Transaction::create([
            'transaction_number' => 'IN' . strtoupper(Str::random(10)),
            'type' => 'Incoming',
            'supplier_id' => $request->supplier_id,
            'created_by' => auth()->id(),
            'status' => 'Pending',
            'date' => $request->date,
            'notes' => $request->notes,
        ]);

        // 2. Simpan Item Transaksi
        foreach ($request->items as $item) {
            $transaction->items()->create($item);
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi Masuk dibuat, menunggu persetujuan Manager.');
    }

    // --- FUNGSIONALITAS STAFF GUDANG (MEMBUAT OUTGOING) ---

    /**
     * Menampilkan form pembuatan Transaksi Keluar (Outgoing/Sale).
     */
    public function createOutgoing(): View
    {
        // Otorisasi Tambahan: Hanya Staff yang bisa mengakses form create
        if (auth()->user()->role !== 'Staff') {
            abort(403, 'Akses Ditolak: Hanya Staff yang bisa membuat transaksi baru.');
        }
        
        // Kita hanya butuh daftar produk untuk dijual. Customer bisa diisi manual.
        $products = Product::all();
        return view('transactions.create_outgoing', compact('products'));
    }

    /**
     * Menyimpan Transaksi Keluar (Outgoing/Sale) ke database.
     */
    public function storeOutgoing(Request $request): RedirectResponse
    {
        // Otorisasi: Hanya Staff
        if (auth()->user()->role !== 'Staff') {
            abort(403, 'Akses Ditolak.');
        }

        // 1. Validasi Input
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_at_transaction' => 'required|numeric|min:0',
        ]);

        // 2. Transaksi Database (Pastikan Konsistensi)
        DB::beginTransaction();
        try {
            // Cek Stok untuk setiap item sebelum membuat transaksi
            foreach ($request->items as $itemData) {
                $product = Product::find($itemData['product_id']);
                $requestedQuantity = (int) $itemData['quantity'];

                if ($product->stock < $requestedQuantity) {
                    DB::rollBack();
                    // Kirim pesan error kembali ke view
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Stok untuk produk '{$product->name}' ({$product->stock}) tidak mencukupi untuk kuantitas yang diminta ({$requestedQuantity}).");
                }
            }

            // 3. Buat Header Transaksi
            $transaction = Transaction::create([
                'transaction_number' => 'OUT-' . time(), // Contoh: Generasi No. Transaksi
                'type' => 'Outgoing',
                'status' => 'Pending', // Mulai sebagai Pending
                'date' => $request->date,
                'customer_name' => $request->customer_name,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // 4. Buat Detail Item
            foreach ($request->items as $itemData) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price_at_transaction' => $itemData['price_at_transaction'],
                ]);
            }

            DB::commit();

            return redirect()->route('transactions.show', $transaction->id)
                            ->with('success', 'Transaksi Keluar berhasil dibuat dan menunggu persetujuan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage());
        }
    }

    // --- FUNGSIONALITAS MANAGER (MENYETUJUI) ---
    /**
     * Approve a pending transaction (Incoming or Outgoing) and update stock.
     */
    public function approve(Transaction $transaction): RedirectResponse
    {
        // Otorisasi: Pastikan hanya Manager atau Admin yang bisa menyetujui
        if (!in_array(auth()->user()->role, ['Admin', 'Manager'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui transaksi.');
        }

        // Hanya bisa menyetujui status Pending
        if ($transaction->status !== 'Pending') {
            return redirect()->back()->with('error', 'Transaksi sudah disetujui atau dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // 1. Update Stok
            $transaction->load('items.product'); // Pastikan relasi diload
            foreach ($transaction->items as $item) {
                $product = $item->product;

                if ($transaction->type === 'Incoming') {
                    // Incoming: Tambah Stok
                    $product->stock += $item->quantity;
                } elseif ($transaction->type === 'Outgoing') {
                    // Outgoing: Kurangi Stok
                    if ($product->stock < $item->quantity) {
                        DB::rollBack();
                        return redirect()->back()->with('error', "Gagal menyetujui: Stok produk '{$product->name}' ({$product->stock}) tidak mencukupi saat ini. Batalkan dan koreksi transaksi.");
                    }
                    $product->stock -= $item->quantity;
                }
                $product->save();
            }

            // 2. Update Status Transaksi
            $transaction->update([
                'status' => 'Approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('transactions.show', $transaction->id)
                            ->with('success', 'Transaksi berhasil disetujui. Stok produk telah diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyetujui transaksi dan memperbarui stok: ' . $e->getMessage());
        }
    }
    
    // --- FUNGSI REJECT (Asumsi Anda punya route reject) ---
    /**
     * Reject a pending transaction.
     */
    public function reject(Transaction $transaction): RedirectResponse
    {
        // Otorisasi: Manager atau Admin
        if (!in_array(auth()->user()->role, ['Admin', 'Manager'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menolak transaksi.');
        }

        // Hanya bisa menolak status Pending
        if ($transaction->status !== 'Pending') {
            return redirect()->back()->with('error', 'Transaksi sudah disetujui atau dibatalkan sebelumnya.');
        }

        try {
            $transaction->update([
                'status' => 'Rejected',
                'approved_by' => auth()->id(), // Menggunakan approved_by sebagai penanda yang menolak
                'approved_at' => now(),
            ]);

            return redirect()->route('transactions.show', $transaction->id)
                            ->with('success', 'Transaksi berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menolak transaksi: ' . $e->getMessage());
        }
    }
    
    // Tambahkan juga metode edit, update, dan destroy jika diperlukan oleh Staff.
    // ...
}