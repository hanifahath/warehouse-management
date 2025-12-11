<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController; 
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);
        $categories = Category::orderBy('name')->get();

        if ($request->has('low_stock')) {
            $products = Product::with('category')
                ->whereColumn('current_stock', '<=', 'min_stock')                    
                ->orderByRaw('(min_stock - current_stock) DESC')   
                ->paginate(10);
            return view('products.index', compact('products', 'categories'));
        }

        $products = $this->productService->getFilteredProducts($request);

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        \Log::info('🟢 ProductController::create METHOD CALLED');
        \Log::info('URL: ' . request()->fullUrl());
        \Log::info('User ID: ' . auth()->id());

        $this->authorize('create', Product::class);

        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductStoreRequest $request)
    {
        $this->authorize('create', Product::class);

        try {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $this->productService->create($data);

            return redirect()->route('products.index')
                            ->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);
        
        $product->load(['category']);
        
        $user = auth()->user();
        $query = Transaction::whereHas('items', function($q) use ($product) {
            $q->where('product_id', $product->id);
        });

        if ($user->isStaff()) {
            $query->where('created_by', $user->id); 
        }
        
        if ($user->isSupplier()) {
            $query->where('supplier_id', $user->id); 
        }
    
        $recent_transactions = $query->with([
            'creator',  
            'items',
            'supplier', 
            'approver'  
        ])
        ->latest()
        ->limit(5)
        ->get();
        
        $stats = $this->productService->calculateProductStats($product);
        
        return view('products.show', compact('product', 'stats', 'recent_transactions'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        try {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $this->productService->update($product, $data);

            return redirect()->route('products.index')
                            ->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        try {
            $this->productService->delete($product);
            return redirect()->route('products.index')
                            ->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get product cost price (for transaction calculation)
     */
    public function getCostPrice(Product $product)
    {
        // Hanya admin dan manager yang bisa lihat harga cost
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json([
            'id' => $product->id,
            'cost_price' => $this->productService->getCostPrice($product),
        ]);
    }

    /**
     * Get products needing restock (for manager dashboard)
     */
    public function needingRestock(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = $this->productService->getProductsNeedingRestock($limit);

        if ($request->ajax()) {
            return response()->json($products);
        }

        return view('products.needing-restock', compact('products'));
    }

    /**
     * Get out of stock products
     */
    public function outOfStock(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $limit = $request->get('limit', 10);
        $products = $this->productService->getOutOfStockProducts($limit);

        if ($request->ajax()) {
            return response()->json($products);
        }

        return view('products.out-of-stock', compact('products'));
    }

    /**
     * Get product statistics for dashboard
     */
    public function stats(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $stats = $this->productService->getProductStats();

        if ($request->ajax()) {
            return response()->json($stats);
        }

        return view('products.stats', compact('stats'));
    }
}