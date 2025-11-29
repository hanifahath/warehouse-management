<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\RestockOrder;
use App\Models\RestockItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Gunakan DB facade

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // --- 1. INISIALISASI DEFAULT SEMUA VARIABEL ---
        // Ini mencegah error "Undefined Variable" jika peran tidak mendefinisikannya.
        $data = [
            'productsCount' => 0, 
            'transactionsCount' => 0,
            'inventoryValue' => 0,
            'lowStockProducts' => collect(), // Gunakan koleksi kosong
            'totalItems' => 0,
            'lowStockCount' => 0,
            'pendingTransactionsCount' => 0,
            'pendingTransactions' => collect(),
            'activeRestocks' => collect(),
            'todaysTransactions' => collect(),
            'pendingRestocks' => collect(),
            'deliveryHistory' => collect(),
        ];

        // Admin Dashboard
        if ($user->hasRole('Admin')) {
            $data['productsCount'] = Product::count();
            $data['transactionsCount'] = Transaction::whereMonth('date', now()->month)->count();
            // Perbaikan kecil: gunakan DB::raw
            $data['inventoryValue'] = Product::sum(DB::raw('stock * purchase_price')); 
            $data['lowStockProducts'] = Product::whereColumn('stock', '<', 'min_stock')->get();
        }

        // Manager Dashboard
        if ($user->hasRole('Manager')) {
            $data['totalItems'] = Product::sum('stock');
            $data['lowStockCount'] = Product::whereColumn('stock', '<', 'min_stock')->count();
            $data['pendingTransactionsCount'] = Transaction::where('status', 'Pending')->count();
            $data['pendingTransactions'] = Transaction::where('status', 'Pending')->get();
            $data['activeRestocks'] = RestockOrder::whereIn('status', ['Pending','Confirmed'])->get();
            
            // Opsional: Jika Manager juga perlu melihat total produk:
            // $data['productsCount'] = Product::count(); 
        }

        // Staff Dashboard
        if ($user->hasRole('Staff')) {
            $data['todaysTransactions'] = Transaction::whereDate('date', now())->get();
        }

        // Supplier Dashboard
        if ($user->hasRole('Supplier')) {
            $data['pendingRestocks'] = RestockOrder::where('status', 'Pending')
                                             ->where('supplier_id', $user->id)
                                             ->get();
            $data['deliveryHistory'] = RestockOrder::where('status', 'Delivered')
                                             ->where('supplier_id', $user->id)
                                             ->get();
        }

        return view('dashboard.index', $data);
    }
}