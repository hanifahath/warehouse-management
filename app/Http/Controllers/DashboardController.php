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
        $stats = [
            'total_products' => Product::count(),
            'monthly_transactions' => Transaction::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'inventory_value' => Product::sum(DB::raw('current_stock * purchase_price')),
            'total_users' => User::count(),
            'pending_suppliers' => User::where('role', 'supplier')
                ->where('status', 'inactive') 
                ->count(),
        ];

        $lowStock = Product::with('category')
            ->whereRaw('current_stock <= min_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        $recentActivities = Transaction::with(['creator', 'items'])
            ->latest()
            ->limit(5)
            ->get();

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

        $lowStock = Product::with('category')
            ->whereRaw('current_stock <= min_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        $pending = Transaction::with(['items.product', 'creator'])
            ->where('status', 'Pending')
            ->latest()
            ->limit(3)
            ->get();

        $restocks = RestockOrder::with('supplier')
            ->whereIn('status', ['pending', 'confirmed', 'in_transit'])
            ->latest()
            ->limit(3)
            ->get();

        $topProducts = Product::select('id', 'name', 'sku', 'current_stock', 'purchase_price')
            ->selectRaw('current_stock * purchase_price as stock_value')
            ->orderBy('stock_value', 'desc')
            ->limit(5)
            ->get();

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

        $todayTransactions = Transaction::with(['items.product', 'supplier'])
            ->where('created_by', $user->id)
            ->whereDate('created_at', today())
            ->latest()
            ->limit(10)
            ->get();

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

        $stats = [
            'pending_orders' => RestockOrder::where('supplier_id', $user->id)
                ->where('status', 'Pending')
                ->count(),
            'confirmed_orders' => RestockOrder::where('supplier_id', $user->id)
                ->where('status', 'Confirmed')
                ->count(),
            'in_transit_orders' => RestockOrder::where('supplier_id', $user->id)
                ->where('status', 'In Transit')
                ->count(),
            'delivered_this_month' => RestockOrder::where('supplier_id', $user->id)
                ->where('status', 'Received')
                ->whereMonth('updated_at', now()->month)
                ->count(),
        ];

        $pendingOrders = RestockOrder::with(['items.product'])
            ->where('supplier_id', $user->id)
            ->where('status', 'Pending')
            ->latest()
            ->limit(5)
            ->get();

        $deliveryHistory = RestockOrder::with(['items.product'])
            ->where('supplier_id', $user->id)
            ->whereIn('status', ['In Transit', 'Received'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'pendingOrders',
            'deliveryHistory'
        ))->with('role', 'supplier');
    }
}