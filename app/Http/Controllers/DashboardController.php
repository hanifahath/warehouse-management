<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use App\Models\RestockOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = strtolower($user->role);
        
        switch ($role) {
            case 'admin':
                return $this->adminDashboard();
            case 'manager':
                return $this->managerDashboard();
            case 'staff':
                return $this->staffDashboard();
            case 'supplier':
                return $this->supplierDashboard();
            default:
                abort(403, 'Role tidak dikenali');
        }
        
    }

    private function adminDashboard()
    {
        // STATISTIK UTAMA
        $stats = [
            'total_products' => Product::count(),
            'monthly_transactions' => Transaction::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'inventory_value' => Product::sum(DB::raw('current_stock * purchase_price')),
            'total_users' => User::count(),
            'pending_suppliers' => User::where('role', 'supplier')
                ->where('status', 'pending') // asumsi ada kolom status
                ->count(),
        ];

        // LOW STOCK ALERTS (top 5)
        $lowStock = Product::with('category')
            ->whereRaw('current_stock <= min_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        // RECENT ACTIVITIES (5 terbaru)
        $recentActivities = Transaction::with(['creator', 'items'])
            ->latest()
            ->limit(5)
            ->get();

        // PENDING SUPPLIERS
        $pendingSuppliers = User::where('role', 'supplier')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats', 
            'lowStock',
            'recentActivities',
            'pendingSuppliers'
        ));
    }

    private function managerDashboard()
    {
        // STATISTIK UTAMA
        $stats = [
            'total_items' => Product::count(),
            'total_products' => Product::count(),
            'inventory_value' => Product::sum(DB::raw('current_stock * purchase_price')),
            'low_stock_count' => Product::whereRaw('current_stock <= min_stock')->count(),
            'pending_transactions' => Transaction::where('status', 'Pending')->count(),
            'active_restocks' => RestockOrder::whereIn('status', ['pending', 'confirmed', 'in_transit'])->count(),
            'monthly_transactions' => Transaction::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // LOW STOCK ALERTS (top 5)
        $lowStock = Product::with('category')
            ->whereRaw('current_stock <= min_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        // PENDING TRANSACTIONS untuk approval - HANYA 3 SAJA
        $pending = Transaction::with(['items.product', 'creator'])
            ->where('status', 'Pending')
            ->latest()
            ->limit(3)
            ->get();

        // ACTIVE RESTOCK ORDERS (3 terbaru)
        $restocks = RestockOrder::with('supplier')
            ->whereIn('status', ['pending', 'confirmed', 'in_transit'])
            ->latest()
            ->limit(3)
            ->get();

        // TOP 5 PRODUCTS BY STOCK VALUE
        $topProducts = Product::select('id', 'name', 'sku', 'current_stock', 'purchase_price')
            ->selectRaw('current_stock * purchase_price as stock_value')
            ->orderBy('stock_value', 'desc')
            ->limit(5)
            ->get();

        // CATEGORY DISTRIBUTION
        $categories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats', 
            'lowStock', 
            'pending', 
            'restocks',
            'topProducts',
            'categories'
        ));
    }

    private function staffDashboard()
    {
        $user = auth()->user();
        
        // STATISTIK
        $stats = [
            'today_transactions' => Transaction::where('created_by', $user->id)
                ->whereDate('created_at', today())
                ->count(),
            'pending_transactions' => Transaction::where('created_by', $user->id)
                ->where('status', 'Pending')
                ->count(),
            'completed_today' => Transaction::where('created_by', $user->id)
                ->whereIn('status', ['Verified', 'Approved', 'Completed', 'Shipped'])
                ->whereDate('created_at', today())
                ->count(),
        ];

        // TODAY'S TRANSACTIONS (buatan staff sendiri)
        $todayTransactions = Transaction::with(['items.product', 'supplier'])
            ->where('created_by', $user->id)
            ->whereDate('created_at', today())
            ->latest()
            ->limit(10)
            ->get();

        // LOW STOCK PRODUCTS (untuk info)
        $lowStockInfo = Product::whereRaw('current_stock <= min_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get(['id', 'name', 'sku', 'current_stock', 'min_stock']);

        return view('dashboard', compact(
            'stats',
            'todayTransactions',
            'lowStockInfo'
        ));
    }

    private function supplierDashboard()
    {
        $user = auth()->user();
        
        // STATISTIK
        $stats = [
            'pending_orders' => RestockOrder::where('supplier_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'confirmed_orders' => RestockOrder::where('supplier_id', $user->id)
                ->where('status', 'confirmed')
                ->count(),
            'in_transit_orders' => RestockOrder::where('supplier_id', $user->id)
                ->where('status', 'in_transit')
                ->count(),
            'delivered_this_month' => RestockOrder::where('supplier_id', $user->id)
                ->where('status', 'received')
                ->whereMonth('updated_at', now()->month)
                ->count(),
        ];

        // PENDING ORDERS (perlu konfirmasi)
        $pendingOrders = RestockOrder::with(['items.product'])
            ->where('supplier_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        // DELIVERY HISTORY (5 terbaru)
        $deliveryHistory = RestockOrder::with(['items.product'])
            ->where('supplier_id', $user->id)
            ->whereIn('status', ['in_transit', 'received'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'pendingOrders',
            'deliveryHistory'
        ));
    }
}