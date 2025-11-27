<?php

namespace App\Http\Controllers;

use App\Models\RestockOrder;
use App\Models\User;
use App\Services\RestockService; // Kita akan asumsikan ada RestockService
use App\Services\TransactionService; // Service yang sudah ada
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RestockController extends Controller
{
    protected $restockService;
    protected $transactionService; // Inject TransactionService

    public function __construct(RestockService $restockService, TransactionService $transactionService)
    {
        $this->restockService = $restockService;
        $this->transactionService = $transactionService;
        
        // Otorisasi: Manager membuat/menerima, Supplier mengkonfirmasi.
        $this->middleware('auth');
    }

    // --- UMUM & LISTING ---

    public function index()
    {
        // Logic filter/scope untuk Manager/Supplier (hanya order mereka)
        $orders = RestockOrder::with(['supplier', 'creator'])->latest()->paginate(10);
        return view('restock.index', compact('orders'));
    }
    
    // --- AKSI MANAGER (CREATE & RECEIVE) ---
    
    /**
     * Menampilkan form untuk membuat Purchase Order (PO)
     */
    public function create()
    {
        if (auth()->user()->role !== 'Manager') { abort(403); }

        $suppliers = User::where('role', 'Supplier')->where('is_approved', true)->get();
        $products = \App\Models\Product::all();
        
        return view('restock.create', compact('suppliers', 'products'));
    }

    /**
     * Menyimpan PO baru (Status: Pending)
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'Manager') { abort(403); }

        // TODO: Lakukan Validasi KRITIS di Form Request
        
        $poNumber = 'PO-' . strtoupper(Str::random(8));
        
        // Asumsikan logika create ada di RestockService
        $this->restockService->createOrder(
            $request->validated(), 
            auth()->id(), 
            $poNumber
        );

        return redirect()->route('restock.index')->with('success', "Purchase Order {$poNumber} berhasil dibuat dan dikirim ke Supplier.");
    }


    /**
     * MANAGER/STAFF MENERIMA BARANG (LOGIKA KRITIS UPDATE STOK)
     */
    public function receive(RestockOrder $order)
    {
        // Otorisasi: Hanya Manager atau Staff Gudang yang boleh menerima
        if (!in_array(auth()->user()->role, ['Manager', 'Staff'])) { abort(403); }
        
        // Cek Status: Hanya order yang Confirmed atau In Transit yang bisa diterima
        if ($order->status !== 'Confirmed by Supplier' && $order->status !== 'In Transit') {
            return back()->with('error', "Order belum dikonfirmasi atau status tidak valid untuk penerimaan.");
        }
        
        // Panggil Restock Service untuk memproses penerimaan
        try {
            // Logika ini harus ada di RestockService
            $this->restockService->processReceiving($order, auth()->id());
            
            return back()->with('success', 'Barang diterima. Stok produk telah diperbarui melalui Transaksi Masuk.');

        } catch (\Exception $e) {
            return back()->with('error', 'Penerimaan gagal: ' . $e->getMessage());
        }
    }

    // --- AKSI SUPPLIER ---

    /**
     * Supplier mengkonfirmasi PO (Status: Confirmed by Supplier)
     */
    public function confirm(RestockOrder $order)
    {
        // Otorisasi: Hanya Supplier yang namanya ada di order dan order masih Pending
        if (auth()->user()->role !== 'Supplier' || $order->supplier_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak mengkonfirmasi order ini.');
        }

        if ($order->status === 'Pending') {
            $order->status = 'Confirmed by Supplier';
            $order->save();
            return back()->with('success', 'Order berhasil dikonfirmasi dan siap dikirim.');
        }

        return back()->with('error', 'Order sudah dikonfirmasi atau tidak lagi Pending.');
    }
}