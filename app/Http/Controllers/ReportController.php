<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
        $this->middleware('auth');
    }

    /**
     * Display inventory report
     */
    public function inventory(Request $request)
    {
        $this->authorize('viewInventory', User::class);
        
        $data = $this->reportService->getInventoryReport($request->all(), Auth::user());
        
        return view('reports.inventory', $data);
    }

    /**
     * Display transactions report
     */
    public function transactions(Request $request)
    {
        $this->authorize('viewTransactions', User::class);
        
        $data = $this->reportService->getTransactionsReport($request->all(), Auth::user());
        
        // Get additional data for filters if needed
        $data['staff_users'] = User::where('role', 'staff')
            ->orderBy('name')
            ->get();
            
        $data['supplier_users'] = User::where('role', 'supplier')
            ->orderBy('company_name')
            ->get();
        
        return view('reports.transactions', $data);
    }

    /**
     * Display low stock report
     */
    public function lowStock(Request $request)
    {
        $this->authorize('viewLowStock', User::class);
        
        $data = $this->reportService->getLowStockReport($request->all(), Auth::user());
        
        return view('reports.low-stock', $data);
    }

    /**
     * Display comprehensive report (admin only)
     */
    public function comprehensive(Request $request)
    {
        $this->authorize('viewAll', User::class);
        
        $data = [
            'inventory_stats' => $this->reportService->getInventoryStats(),
            'transaction_stats' => $this->reportService->getTransactionStats(),
            'low_stock_stats' => $this->reportService->getLowStockStats(),
            'dashboard_summary' => $this->reportService->getDashboardSummary(Auth::user()),
        ];
        
        return view('reports.comprehensive', $data);
    }

    /**
     * Export inventory report
     */
    public function exportInventory(Request $request)
    {
        $this->authorize('viewInventory', User::class);
        
        // Use the same filters for export
        $data = $this->reportService->getInventoryReport($request->all(), Auth::user());
        
        // Logic untuk export ke Excel
        // return Excel::download(new InventoryExport($data['products']), 'inventory-report.xlsx');
        
        // For now, return view
        return view('exports.inventory', $data);
    }

    /**
     * Export transactions report
     */
    public function exportTransactions(Request $request)
    {
        $this->authorize('viewTransactions', User::class);
        
        $data = $this->reportService->getTransactionsReport($request->all(), Auth::user());
        
        // Logic untuk export ke Excel
        // return Excel::download(new TransactionsExport($data['transactions']), 'transactions-report.xlsx');
        
        return view('exports.transactions', $data);
    }

    /**
     * Export low stock report
     */
    public function exportLowStock(Request $request)
    {
        $this->authorize('viewLowStock', User::class);
        
        $data = $this->reportService->getLowStockReport($request->all(), Auth::user());
        
        // Logic untuk export ke Excel
        // return Excel::download(new LowStockExport($data['lowStockProducts']), 'low-stock-report.xlsx');
        
        return view('exports.low-stock', $data);
    }

    /**
     * Get dashboard data for admin/manager
     */
    public function dashboardSummary(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isManager()) {
            $data = $this->reportService->getDashboardSummary($user);
            return response()->json($data);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}