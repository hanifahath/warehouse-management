<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    protected $service;

    public function __construct(CategoryService $service)
    {
        $this->middleware(['auth', 'role:Admin,Manager']);
        $this->service = $service;
    }

    public function index(): View
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::latest()->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        $this->authorize('create', Category::class);
        return view('categories.create');
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $this->service->create($request->validated());

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function show(Category $category): View
    {
        $this->authorize('view', $category);
        $category->load('products');

        return view('categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    public function update(CategoryUpdateRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $this->service->update($category, $request->validated());

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        try {
            $this->service->delete($category);
            return redirect()->route('admin.categories.index')
                             ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
