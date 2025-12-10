<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Get filtered products with advanced filtering
     */
    public function getFilteredProducts(Request $request): LengthAwarePaginator
    {
        $query = Product::with('category');

        // 1. Search by name or SKU
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2. Filter by category
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // 3. Filter by stock status
        if ($stockStatus = $request->get('stock_status')) {
            switch ($stockStatus) {
                case 'safe':
                    $query->whereColumn('current_stock', '>', 'min_stock');
                    break;
                case 'low':
                    $query->whereColumn('current_stock', '<=', 'min_stock')
                          ->where('current_stock', '>', 0);
                    break;
                case 'empty':
                    $query->where('current_stock', 0);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        // 5. Apply sorting
        $sort = $request->get('sort', 'latest');
        
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'stock_asc':
            case 'lowest_stock':
                $query->orderBy('current_stock', 'asc');
                break;
            case 'stock_desc':
            case 'highest_stock':
                $query->orderBy('current_stock', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        // 6. Pagination
        $perPage = $request->get('per_page', 10);
        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Create new product with image handling
     */
    public function create(array $data): Product
    {
        DB::beginTransaction();
        try {
            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                // store uploaded file and persist path in `image_path` column
                $data['image_path'] = $this->uploadImage($data['image']);
                unset($data['image']);
            }

            // Ensure is_active has default value
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }

            $product = Product::create($data);
            
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update product with image handling
     */
    public function update(Product $product, array $data): bool
    {
        DB::beginTransaction();
        try {
            // Handle image removal
            if (isset($data['remove_image']) && $data['remove_image']) {
                $this->deleteImage($product->image_path);
                $data['image_path'] = null;
                unset($data['remove_image']);
            }

            // Handle new image upload
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                // Delete old image if exists
                $this->deleteImage($product->image_path);

                // Upload new image and save to image_path column
                $data['image_path'] = $this->uploadImage($data['image']);
                unset($data['image']);
            }

            // Ensure SKU is not changed
            unset($data['sku']);

            $result = $product->update($data);
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete product (only if stock is 0)
     */
    public function delete(Product $product): bool
    {
        if ($product->current_stock > 0) {
            throw new \Exception('Produk tidak dapat dihapus karena stok masih tersedia.');
        }

        DB::beginTransaction();
        try {
            // Delete image if exists
            $this->deleteImage($product->image_path);
            
            $result = $product->delete();
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Upload image to storage
     */
    private function uploadImage(\Illuminate\Http\UploadedFile $image): string
    {
        return $image->store('products', 'public');
    }

    /**
     * Delete image from storage
     */
    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    /**
     * Get product cost price
     */
    public function getCostPrice(Product $product): float
    {
        return $product->purchase_price;
    }

    /**
     * Get product details with recent transactions
     */
    public function getProductDetailsWithTransactions(Product $product, int $limit = 10): array
    {
        // Load product with category
        $product->load('category');
        
        // Get recent transactions for this product
        $recentTransactions = $this->getRecentTransactionsForProduct($product->id, $limit);
        
        // Calculate statistics
        $stats = [
            'inventory_value' => $this->calculateInventoryValue($product),
            'profit_margin' => $this->calculateProfitMargin($product),
            'stock_status' => $this->getStockStatus($product),
            'stock_percentage' => $this->calculateStockPercentage($product),
            'is_low_stock' => $product->current_stock <= $product->min_stock,
            'is_out_of_stock' => $product->current_stock == 0,
        ];

        return [
            'product' => $product,
            'recent_transactions' => $recentTransactions,
            'stats' => $stats,
        ];
    }

    public function calculateProductStats(Product $product): array
    {
        return [
            'inventory_value' => $this->calculateInventoryValue($product),
            'profit_margin' => $this->calculateProfitMargin($product),
            'stock_status' => $this->getStockStatus($product),
            'stock_percentage' => $this->calculateStockPercentage($product),
            'is_low_stock' => $product->current_stock <= $product->min_stock,
            'is_out_of_stock' => $product->current_stock == 0,
            
            // Additional stats yang mungkin dibutuhkan di view
            'current_stock' => $product->current_stock,
            'minimum_stock' => $product->min_stock,
            'selling_price' => $product->selling_price ?? 0,
            'purchase_price' => $product->purchase_price ?? 0,
            'unit' => $product->unit ?? 'pcs',
        ];
    }

    /**
     * Get recent transactions for product
     */
    private function getRecentTransactionsForProduct(int $productId, int $limit = 10)
    {
        return TransactionItem::where('product_id', $productId)
            ->with(['transaction' => function($query) {
                $query->with(['creator', 'approver']);
            }])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'transaction_id' => $item->transaction_id,
                    'transaction_number' => $item->transaction->transaction_number,
                    'type' => $item->transaction->type,
                    'quantity' => $item->quantity,
                    'price_at_transaction' => $item->price_at_transaction,
                    'status' => $item->transaction->status,
                    'date' => $item->transaction->date,
                    'created_at' => $item->created_at,
                    'creator_name' => $item->transaction->creator->name ?? 'System',
                    'approver_name' => $item->transaction->approver->name ?? '-',
                ];
            });
    }

    /**
     * Calculate inventory value
     */
    private function calculateInventoryValue(Product $product): float
    {
        return $product->current_stock * $product->selling_price;
    }

    /**
     * Calculate profit margin
     */
    private function calculateProfitMargin(Product $product): ?array
    {
        if (!$product->purchase_price || $product->purchase_price == 0) {
            return null;
        }

        $profit = $product->selling_price - $product->purchase_price;
        $margin = ($profit / $product->purchase_price) * 100;

        return [
            'percentage' => round($margin, 2),
            'amount' => $profit,
            'is_positive' => $profit >= 0,
        ];
    }

    /**
     * Get stock status
     */
    private function getStockStatus(Product $product): array
    {
        if ($product->current_stock == 0) {
            return [
                'label' => 'Stok Habis',
                'color' => 'danger',
                'icon' => 'fas fa-times-circle',
                'class' => 'text-danger',
            ];
        } elseif ($product->current_stock <= $product->min_stock) {
            return [
                'label' => 'Stok Rendah',
                'color' => 'warning',
                'icon' => 'fas fa-exclamation-triangle',
                'class' => 'text-warning',
            ];
        } else {
            return [
                'label' => 'Stok Aman',
                'color' => 'success',
                'icon' => 'fas fa-check-circle',
                'class' => 'text-success',
            ];
        }
    }

    /**
     * Calculate stock percentage
     */
    private function calculateStockPercentage(Product $product): float
    {
        $max = max($product->min_stock * 3, $product->current_stock);
        
        if ($max == 0) {
            return 0;
        }

        return min(100, ($product->current_stock / $max) * 100);
    }

    /**
     * Get products needing restock
     */
    public function getProductsNeedingRestock(int $limit = 10)
    {
        return Product::whereColumn('current_stock', '<=', 'min_stock')
            ->where('current_stock', '>', 0)
            ->where('is_active', true)
            ->orderByRaw('current_stock / min_stock ASC')
            ->limit($limit)
            ->get();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts(int $limit = 10)
    {
        return Product::where('current_stock', 0)
            ->where('is_active', true)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get product statistics
     */
    public function getProductStats(): array
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'inactive_products' => Product::where('is_active', false)->count(),
            'low_stock_products' => Product::whereColumn('current_stock', '<=', 'min_stock')
                ->where('current_stock', '>', 0)
                ->where('is_active', true)
                ->count(),
            'out_of_stock_products' => Product::where('current_stock', 0)
                ->where('is_active', true)
                ->count(),
            'total_inventory_value' => Product::where('is_active', true)
                ->sum(DB::raw('current_stock * selling_price')),
        ];
    }

    /**
     * Toggle product active status
     */
    public function toggleStatus(Product $product): bool
    {
        return $product->update(['is_active' => !$product->is_active]);
    }

    /**
     * Update product stock manually
     */
    public function updateStock(Product $product, int $quantity, string $notes = ''): bool
    {
        if ($quantity < 0 && abs($quantity) > $product->current_stock) {
            throw new \Exception('Stok tidak mencukupi untuk pengurangan.');
        }

        DB::beginTransaction();
        try {
            $oldStock = $product->current_stock;
            $newStock = $oldStock + $quantity;
            
            $product->update(['current_stock' => $newStock]);
            
            // Log stock adjustment if needed
            if ($notes) {
                // You can create a StockAdjustment model here
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get image URL for product
     */
    public function getImageUrl(?string $imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }
        
        return Storage::disk('public')->url($imagePath);
    }
}