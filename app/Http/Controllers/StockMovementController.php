<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    /**
     * Display a listing of stock movements (View Only)
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', StockMovement::class);
        
        $query = StockMovement::with(['product', 'user', 'reference']);
        
        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        // Filter by type (in/out)
        if ($request->has('type')) {
        if ($request->type === 'in') {
            // Filter untuk stock in: restock atau transaction_in
            $query->whereIn('source_type', ['restock', 'transaction_in']);
        } elseif ($request->type === 'out') {
            // Filter untuk stock out: transaction_out
            $query->where('source_type', 'transaction_out');
        }
    }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $movements = $query->orderBy('created_at', 'desc')->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('stock-movements.index', compact('movements', 'products'));
    }

    /**
     * Display product stock history
     */
    public function productHistory(Product $product)
    {
        $this->authorize('viewAny', StockMovement::class);
        
        $movements = StockMovement::with(['user', 'reference'])
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('stock-movements.product-history', compact('movements', 'product'));
    }

    /**
     * Display the specified stock movement
     */
    public function show(StockMovement $movement)
    {
        $this->authorize('view', $movement);
        
        // Eager load relationships
        $movement->load([
            'product.category', 
            'user', 
            'reference'
        ]);
        
        return view('stock-movements.show', compact('movement'));
    }
}