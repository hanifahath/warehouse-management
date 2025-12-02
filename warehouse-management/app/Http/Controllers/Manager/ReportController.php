<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct()
    {
        // Otorisasi: Hanya Admin dan Manager yang diizinkan melihat laporan.
        $this->middleware(['auth', 'role:Admin,Manager']);
    }

    /**
     * Menampilkan laporan Inventori (Daftar semua produk dengan status stok).
     */
    public function inventory()
    {
        // Ambil semua produk, eager load kategori, dan tampilkan 20 per halaman.
        $products = Product::with('category')
                            ->latest()
                            ->paginate(20);

        return view('manager.reports.inventory', compact('products'));
    }

    /**
     * Menampilkan laporan Transaksi (Daftar semua transaksi).
     * Mendukung filter dasar berdasarkan tanggal.
     */
    public function transactions(Request $request)
    {
        $query = Transaction::with('user', 'transactionDetails');

        // Logika Filter Tanggal
        if ($startDate = $request->get('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        
        // Logika Filter Status (Asumsi ada kolom 'status' di model Transaction)
        if ($status = $request->get('status')) {
             $query->where('status', $status);
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return view('manager.reports.transactions', compact('transactions'));
    }

    /**
     * Menampilkan laporan Stok Rendah (Produk yang stoknya di bawah batas minimum).
     */
    public function lowStock()
    {
        // Ambil produk di mana 'stock' lebih kecil dari 'min_stock', urutkan dari stok terendah.
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<', 'min_stock')
            ->orderBy('stock', 'asc')
            ->paginate(20);
            
        return view('manager.reports.low_stock', compact('lowStockProducts'));
    }
}