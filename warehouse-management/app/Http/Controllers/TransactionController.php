<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\User; // Untuk mendapatkan Supplier
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Untuk membuat nomor transaksi
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    protected $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
        // Otorisasi: Staff membuat, Manager menyetujui, Admin/Manager melihat semua.
        $this->middleware(['auth', 'role:admin|manager|staff']); 
    }
    
    // --- FUNGSIONALITAS UMUM (Admin/Manager/Staff) ---

    /**
     * Menampilkan daftar transaksi yang perlu diuji (Pending, Approved).
     */
    public function index()
    {
        // Manager dan Admin melihat semua. Staff hanya melihat miliknya.
        if (auth()->user()->role === 'Staff') {
            $transactions = Transaction::where('created_by', auth()->id())->latest()->paginate(10);
        } else {
            $transactions = Transaction::latest()->paginate(10);
        }

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Menampilkan detail transaksi (sebelum/sesudah approval).
     */
    public function show(Transaction $transaction)
    {
        // Pastikan relasi items dimuat
        $transaction->load(['items.product', 'creator', 'approver']); 
        
        // Otorisasi sederhana: Hanya pembuat, Manager, atau Admin yang boleh melihat
        if (auth()->user()->role === 'Staff' && $transaction->created_by !== auth()->id()) {
            abort(403, 'Akses Ditolak.');
        }

        return view('transactions.show', compact('transaction'));
    }

    // --- FUNGSIONALITAS STAFF GUDANG (MEMBUAT) ---

    /**
     * Menampilkan form pembuatan Transaksi Masuk (Incoming).
     */
    public function createIncoming()
    {
        // Dapatkan semua Supplier yang sudah Approved
        $suppliers = User::where('role', 'Supplier')->where('is_approved', true)->get();
        $products = Product::all();

        return view('transactions.create_incoming', compact('suppliers', 'products'));
    }

    /**
     * Menyimpan Transaksi Masuk ke database (Status Pending).
     */
    public function storeIncoming(Request $request)
    {
        // *VALIDASI KRITIS: Pastikan array items tidak kosong*
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

    /**
     * Handle POST request for Outgoing (Sale) transactions.
     */
    public function store_outgoing(Request $request)
    {
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
            // Cek Stok untuk setiap item
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
    public function approve(Transaction $transaction)
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
            foreach ($transaction->items as $item) {
                $product = $item->product;

                if ($transaction->type === 'Incoming') {
                    // Incoming: Tambah Stok
                    $product->stock += $item->quantity;
                } elseif ($transaction->type === 'Outgoing') {
                    // Outgoing: Kurangi Stok
                    $product->stock -= $item->quantity;

                    // Cek sekali lagi (walaupun seharusnya sudah divalidasi saat store)
                    if ($product->stock < 0) {
                        DB::rollBack();
                        return redirect()->back()->with('error', "Gagal menyetujui: Stok produk '{$product->name}' menjadi minus. Batalkan dan koreksi transaksi.");
                    }
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
}