<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'user', 'reference']);
    
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            if ($request->type === 'in') {
                $query->in(); 
            } elseif ($request->type === 'out') {
                $query->out(); 
            }
        }
        
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

    public function productHistory(Product $product)
    {
        $this->authorize('viewAny', StockMovement::class);
        
        $movements = StockMovement::with(['user', 'reference'])
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('stock-movements.product-history', compact('movements', 'product'));
    }

    public function show(StockMovement $movement)
    {
        $this->authorize('view', $movement);
        $movement->load([
            'product.category', 
            'user', 
            'reference'
        ]);
        
        return view('stock-movements.show', compact('movement'));
    }
}