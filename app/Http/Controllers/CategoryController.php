<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Category::class);
            
            // Pakai method baru yang menerima Request object
            $categories = $this->categoryService->getFilteredCategories($request);
            
            return view('categories.index', compact('categories'));

        } catch (\Exception $e) {
            \Log::error('CategoryController@index Error: ' . $e->getMessage());
            return view('categories.index', [
                'categories' => collect(),
                'error' => 'Error loading categories: ' . $e->getMessage()
            ]);
        }
    }

    public function create(): View
    {
        $this->authorize('create', Category::class);
        return view('categories.create');
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        try {
            $this->categoryService->create($request->validated());
            return redirect()->route('categories.index')
                         ->with('success', 'Kategori baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating category: ' . $e->getMessage());
        }
    }

    public function show(Category $category): View
    {
        $this->authorize('view', $category);
        
        // Eager load products with count
        $category->load(['products' => function($query) {
            $query->orderBy('name')->take(10);
        }]);
        
        // Products count will be available via relationship
        // Image URL sudah ada di accessor

        return view('categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);
        
        // Get image URL if exists
        $category->image_url = $this->categoryService->getImageUrl($category->image_path);

        return view('categories.edit', compact('category'));
    }

    public function update(CategoryUpdateRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        try {
            $this->categoryService->update($category, $request->validated());
            return redirect()->route('categories.index')
                         ->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating category: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        try {
            $this->categoryService->delete($category);
            return redirect()->route('categories.index')
                             ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}