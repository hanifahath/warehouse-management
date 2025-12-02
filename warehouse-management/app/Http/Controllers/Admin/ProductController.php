<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Services\ProductService;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        // Pastikan service di-inject di sini
        $this->middleware(['auth', 'role:Admin,Manager']);
        $this->service = $service;
    }

    /**
     * Menampilkan daftar produk yang sudah difilter dan disortir oleh Service Layer.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $categories = Category::all();
        
        // DELEGASI PENUH: Controller hanya memanggil service dan meneruskan request.
        // Semua logika query, filter, dan sort ada di dalam ProductService.
        $products = $this->service->getFilteredProducts($request);

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(ProductStoreRequest $request)
    {
        $this->authorize('create', Product::class);

        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $this->service->create($data);

        return redirect()->route('admin.products.index')
                        ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);

        $product->load('category');
        $recentTransactions = $product->transactionDetails()->with('transaction')
                                        ->latest()
                                        ->take(10)
                                        ->get();

        return view('products.show', compact('product', 'recentTransactions'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $this->service->update($product, $data);

        return redirect()->route('admin.products.index')
                        ->with('success', 'Produk berhasil diperbarui.');
    }

    public function getCostPrice(Product $product)
    {
        $this->authorize('viewCostPrice', Product::class);

        return response()->json([
            'id' => $product->id,
            'unit_price' => $this->service->getCostPrice($product),
        ]);
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        try {
            $this->service->delete($product);
            return redirect()->route('admin.products.index')
                            ->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}