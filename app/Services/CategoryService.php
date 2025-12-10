<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    // CategoryService.php - GANTI method getAllCategories dengan:
    /**
     * Get filtered categories with advanced filtering
     */
    public function getFilteredCategories(Request $request): LengthAwarePaginator
    {
        $query = Category::withCount('products');

        // 1. Search by name or description
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2. Filter by has products
        if ($hasProducts = $request->get('has_products')) {
            switch ($hasProducts) {
                case 'yes':
                    $query->has('products');
                    break;
                case 'no':
                    $query->doesntHave('products');
                    break;
            }
        }

        // 3. Apply sorting
        $sort = $request->get('sort', 'created_desc');
        
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'products_asc':
                $query->orderBy('products_count', 'asc');
                break;
            case 'products_desc':
                $query->orderBy('products_count', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // 4. Pagination
        $perPage = $request->get('per_page', 10);
        return $query->paginate($perPage)->withQueryString();
    }
    
    /**
     * Apply sorting to query
     */
    private function applySorting($query, ?string $sort): void
    {
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'created_new':
                $query->orderBy('created_at', 'desc');
                break;
            case 'created_old':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
    }
    
    /**
     * Create new category with image upload
     */
    public function create(array $data): Category
    {
        try {
            // Handle image upload if present
            if (isset($data['image_path']) && $data['image_path'] instanceof UploadedFile) {
                $data['image_path'] = $this->uploadImage($data['image_path']);
            }
            
            return Category::create($data);
        } catch (\Exception $e) {
            \Log::error('Error in CategoryService::create: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update category with image handling
     */
    public function update(Category $category, array $data): bool
    {
        try {
            // Handle image removal
            if (isset($data['remove_image']) && $data['remove_image']) {
                $this->deleteImage($category->image_path);
                $data['image_path'] = null;
                unset($data['remove_image']);
            }
            
            // Handle image upload if present
            if (isset($data['image_path']) && $data['image_path'] instanceof UploadedFile) {
                // Delete old image if exists
                $this->deleteImage($category->image_path);
                
                // Upload new image
                $data['image_path'] = $this->uploadImage($data['image_path']);
            }
            
            return $category->update($data);
        } catch (\Exception $e) {
            \Log::error('Error in CategoryService::update: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete category and its image
     */
    public function delete(Category $category): bool
    {
        try {
            // Check if category has products
            if ($category->products()->exists()) {
                throw new \Exception('Tidak dapat menghapus kategori yang memiliki produk. Pindahkan atau hapus produk terlebih dahulu.');
            }
            
            // Delete image if exists
            $this->deleteImage($category->image_path);
            
            return $category->delete();
        } catch (\Exception $e) {
            \Log::error('Error in CategoryService::delete: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Upload image to storage
     */
    private function uploadImage(UploadedFile $image): string
    {
        $path = $image->store('categories', 'public');
        return $path;
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
     * Get image URL for category
     */
    public function getImageUrl(?string $imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }
        
        return Storage::disk('public')->url($imagePath);
    }

    
}