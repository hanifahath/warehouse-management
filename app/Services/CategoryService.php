<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function getFilteredCategories(Request $request): LengthAwarePaginator
    {
        $query = Category::withCount('products');

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

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

        $perPage = $request->get('per_page', 10);
        return $query->paginate($perPage)->withQueryString();
    }
    
    public function create(array $data): Category
    {
        if (isset($data['image_path']) && $data['image_path'] instanceof UploadedFile) {
            $data['image_path'] = $this->uploadImage($data['image_path']);
        }
        
        return Category::create($data);
    }
    
    public function update(Category $category, array $data): bool
    {
        if (isset($data['remove_image']) && $data['remove_image']) {
            $this->deleteImage($category->image_path);
            $data['image_path'] = null;
            unset($data['remove_image']);
        }
        
        if (isset($data['image_path']) && $data['image_path'] instanceof UploadedFile) {
            $this->deleteImage($category->image_path);
            $data['image_path'] = $this->uploadImage($data['image_path']);
        }
        
        return $category->update($data);
    }
    
    public function delete(Category $category): bool
    {
        if ($category->products()->exists()) {
            throw new \Exception('Tidak dapat menghapus kategori yang memiliki produk. Pindahkan atau hapus produk terlebih dahulu.');
        }
        
        $this->deleteImage($category->image_path);
        
        return $category->delete();
    }
    
    private function uploadImage(UploadedFile $image): string
    {
        $path = $image->store('categories', 'public');
        return $path;
    }
    
    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
    
    public function getImageUrl(?string $imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }
        
        return Storage::disk('public')->url($imagePath);
    }
}