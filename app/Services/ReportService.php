<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    /**
     * Get inventory report data
     */
    public function getInventoryReport(array $filters = [], User $user = null)
    {
        $query = Product::with('category');

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter by stock status
        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'low':
                    $query->whereRaw('current_stock <= min_stock');
                    break;
                case 'out':
                    $query->where('current_stock', 0);
                    break;
                case 'healthy':
                    $query->whereRaw('current_stock > min_stock');
                    break;
            }
        }

        // Search
        if (!empty($filters['search'])) {
            $query->where(function(Builder $q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('sku', 'like', "%{$filters['search']}%")
                  ->orWhereHas('category', function(Builder $categoryQuery) use ($filters) {
                      $categoryQuery->where('name', 'like', "%{$filters['search']}%");
                  });
            });
        }

        return [
            'products' => $query->latest()->paginate(20)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'stats' => $this->getInventoryStats()
        ];
    }

    /**
     * Get inventory statistics
     */
    private function getInventoryStats(): array
    {
        return [
            'total_products' => Product::count(),
            'total_value' => Product::sum(DB::raw('current_stock * purchase_price')),
            'low_stock_count' => Product::whereRaw('current_stock <= min_stock AND current_stock > 0')->count(),
            'out_of_stock_count' => Product::where('current_stock', 0)->count(),
            'healthy_stock_count' => Product::whereRaw('current_stock > min_stock')->count(),
        ];
    }

    /**
     * Get transactions report data
     */
    public function getTransactionsReport(array $filters = [], User $user = null)
    {
        $query = Transaction::with(['creator', 'items.product', 'supplier']);

        // Apply filters
        $query = $this->applyTransactionFilters($query, $filters);

        return [
            'transactions' => $query->latest()->paginate(20)->withQueryString(),
            'stats' => $this->getTransactionStats($filters),
            'filters' => $filters
        ];
    }

    /**
     * Apply transaction filters
     */
    private function applyTransactionFilters(Builder $query, array $filters): Builder
    {
        // Date range filter
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }
        
        // Status filter
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        // Type filter
        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        // Search
        if (!empty($filters['search'])) {
            $query->where(function(Builder $q) use ($filters) {
                $q->where('transaction_number', 'like', "%{$filters['search']}%")
                  ->orWhere('customer_name', 'like', "%{$filters['search']}%")
                  ->orWhere('reference_number', 'like', "%{$filters['search']}%")
                  ->orWhere('id', $filters['search']);
            });
        }

        // Filter by staff (for admin only)
        if (!empty($filters['staff_id'])) {
            $query->where('created_by', $filters['staff_id']);
        }

        // Filter by supplier (for incoming transactions)
        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        return $query;
    }

    /**
     * Get transaction statistics
     */
    private function getTransactionStats(array $filters = []): array
    {
        $baseQuery = Transaction::query();
        
        // Apply same filters to stats
        if (!empty($filters['start_date'])) {
            $baseQuery->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $baseQuery->whereDate('created_at', '<=', $filters['end_date']);
        }

        return [
            'total_transactions' => (clone $baseQuery)->count(),
            'total_incoming' => (clone $baseQuery)->where('type', 'incoming')->count(),
            'total_outgoing' => (clone $baseQuery)->where('type', 'outgoing')->count(),
            'total_pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'total_approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'total_completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'total_rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'total_revenue' => (clone $baseQuery)->where('type', 'outgoing')
                ->whereIn('status', ['completed', 'shipped'])
                ->sum('total_amount'),
            'total_purchase' => (clone $baseQuery)->where('type', 'incoming')
                ->whereIn('status', ['completed', 'verified'])
                ->sum('total_amount'),
        ];
    }

    /**
     * Get low stock report data
     */
    public function getLowStockReport(array $filters = [], User $user = null)
    {
        $query = Product::with('category')
            ->whereRaw('current_stock <= min_stock');

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Search
        if (!empty($filters['search'])) {
            $query->where(function(Builder $q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('sku', 'like', "%{$filters['search']}%");
            });
        }

        // Calculate stock deficit for ordering
        $query->select('*', DB::raw('(min_stock - current_stock) as stock_deficit'));

        return [
            'lowStockProducts' => $query->orderBy('stock_deficit', 'DESC')
                ->paginate(20)
                ->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'stats' => $this->getLowStockStats($filters)
        ];
    }

    /**
     * Get low stock statistics
     */
    private function getLowStockStats(array $filters = []): array
    {
        $query = Product::whereRaw('current_stock <= min_stock');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        $totalValueAtRisk = $query->sum(DB::raw('(min_stock - current_stock) * purchase_price'));

        return [
            'total_low_stock' => $query->count(),
            'critical_count' => Product::whereRaw('current_stock <= (min_stock * 0.5)')->count(),
            'out_of_stock_count' => Product::where('current_stock', 0)->count(),
            'total_value_at_risk' => $totalValueAtRisk,
        ];
    }

    /**
     * Get dashboard summary for admin/manager
     */
    public function getDashboardSummary(User $user): array
    {
        $recentPeriod = now()->subDays(30);

        return [
            'recent_transactions' => Transaction::where('created_at', '>=', $recentPeriod)
                ->latest()
                ->limit(10)
                ->get(),
            'top_low_stock' => Product::whereRaw('current_stock <= min_stock')
                ->orderByRaw('(min_stock - current_stock) DESC')
                ->limit(5)
                ->get(),
            'monthly_summary' => $this->getMonthlySummary(),
        ];
    }

    /**
     * Get monthly transaction summary
     */
    private function getMonthlySummary(): array
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        return [
            'current_month' => [
                'incoming' => Transaction::where('type', 'incoming')
                    ->where('created_at', '>=', $currentMonth)
                    ->whereIn('status', ['completed', 'verified'])
                    ->count(),
                'outgoing' => Transaction::where('type', 'outgoing')
                    ->where('created_at', '>=', $currentMonth)
                    ->whereIn('status', ['completed', 'shipped'])
                    ->count(),
                'revenue' => Transaction::where('type', 'outgoing')
                    ->where('created_at', '>=', $currentMonth)
                    ->whereIn('status', ['completed', 'shipped'])
                    ->sum('total_amount'),
            ],
            'last_month' => [
                'incoming' => Transaction::where('type', 'incoming')
                    ->whereBetween('created_at', [$lastMonth, $currentMonth])
                    ->whereIn('status', ['completed', 'verified'])
                    ->count(),
                'outgoing' => Transaction::where('type', 'outgoing')
                    ->whereBetween('created_at', [$lastMonth, $currentMonth])
                    ->whereIn('status', ['completed', 'shipped'])
                    ->count(),
                'revenue' => Transaction::where('type', 'outgoing')
                    ->whereBetween('created_at', [$lastMonth, $currentMonth])
                    ->whereIn('status', ['completed', 'shipped'])
                    ->sum('total_amount'),
            ],
        ];
    }
}