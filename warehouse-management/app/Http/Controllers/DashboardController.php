<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\RestockOrder;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard dengan data dan navigasi sesuai peran pengguna.
     */
    public function index()
    {
        // 1. Ambil Pengguna dan Peran
        $user = Auth::user();
        $role = $user->role; // Variabel $role diambil di sini
        $data = []; // Variabel $data akan menjadi $stats di view

        // 2. Logika Pengambilan Statistik Berdasarkan Peran
        if (in_array($role, ['Admin', 'Manager'])) {
            $data = [
                'total_products' => Product::count(),
                'low_stock_count' => Product::lowStock()->count(),
                'pending_transactions' => Transaction::where('status', 'Pending')->count(),
                'pending_restocks' => RestockOrder::where('status', 'Pending')->count(),
                // Pastikan kolom status ada di tabel users jika menggunakan status untuk approval supplier
                'unapproved_suppliers' => User::where('role', 'Supplier') 
                                              ->where('status', 'Pending')->count(),
            ];
        } 
        
        // Logika untuk Dashboard Staff
        elseif ($role === 'Staff') {
            $data = [
                'total_transactions_today' => Transaction::where('created_by', $user->id)
                                                         ->whereDate('created_at', today())
                                                         ->count(),
                'restock_to_receive' => RestockOrder::where('status', 'Confirmed by Supplier')->count(),
            ];
        }

        // Logika untuk Dashboard Supplier
        elseif ($role === 'Supplier') {
            $data = [
                'pending_confirmation' => RestockOrder::where('supplier_id', $user->id)
                                                     ->where('status', 'Pending')->count(),
                'total_orders' => RestockOrder::where('supplier_id', $user->id)->count(),
            ];
        }

        // 3. Teruskan variabel $role dan $data ke view 'dashboard'
        return view('dashboard.dashboard', [
            'role' => $role, // <--- Variabel $role DIKIRIM ke view
            'stats' => $data,
        ]);
    }
}