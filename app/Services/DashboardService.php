<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\RestockOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardService
{
    public function getDashboardData($user): array
    {
        $data = [
            'productsCount' => 0, 
            'transactionsCount' => 0,
            'inventoryValue' => 0,
            'lowStockProducts' => collect(),
            'totalItems' => 0,
            'lowStockCount' => 0,
            'pendingTransactionsCount' => 0,
            'pendingTransactions' => collect(),
            'activeRestocks' => collect(),
            'todaysTransactions' => collect(),
            'pendingRestocks' => collect(),
            'deliveryHistory' => collect(),
        ];

        if ($user->hasRole('Admin')) {
            $data = array_merge($data, $this->getAdminData());
        }

        if ($user->hasRole('Manager')) {
            $data = array_merge($data, $this->getManagerData());
        }

        if ($user->hasRole('Staff')) {
            $data = array_merge($data, $this->getStaffData());
        }

        if ($user->hasRole('Supplier')) {
            $data = array_merge($data, $this->getSupplierData($user));
        }

        return $data;
    }

    private function getAdminData(): array
    {
        return [
            'productsCount' => Product::count(),
            'transactionsCount' => Transaction::whereMonth('date', now()->month)->count(),
            'inventoryValue' => Product::sum(DB::raw('current_stock * purchase_price')),
            'lowStockProducts' => Product::whereColumn('current_stock', '<', 'min_stock')->get(),
        ];
    }

    private function getManagerData(): array
    {
        return [
            'totalItems' => Product::sum('current_stock'),
            'lowStockCount' => Product::whereColumn('current_stock', '<', 'min_stock')->count(),
            'pendingTransactionsCount' => Transaction::where('status', 'Pending')->count(),
            'pendingTransactions' => Transaction::where('status', 'Pending')->get(),
            'activeRestocks' => RestockOrder::whereIn('status', ['Pending','Confirmed'])->get(),
        ];
    }

    private function getStaffData(): array
    {
        return [
            'todaysTransactions' => Transaction::whereDate('date', now())->get(),
        ];
    }

    private function getSupplierData($user): array
    {
        return [
            'pendingRestocks' => RestockOrder::where('status', 'Pending')
                                            ->where('supplier_id', $user->id)
                                            ->get(),
            'deliveryHistory' => RestockOrder::where('status', 'Delivered')
                                            ->where('supplier_id', $user->id)
                                            ->get(),
        ];
    }
}