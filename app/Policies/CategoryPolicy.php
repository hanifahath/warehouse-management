<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Admin & Manager can view all categories
     * Staff can view categories (for product operations)
     * Supplier can view categories (for restock operations)
     */
    public function viewAny(User $user): bool
    {
        // Only internal roles may view categories globally.
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    /**
     * Admin & Manager can view category detail
     * Staff and Supplier can view but with limited info
     */
    public function view(User $user, Category $category): bool
    {
        // Category details limited to internal roles.
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    /**
     * Only Admin & Manager can create category
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Only Admin & Manager can update category
     */
    public function update(User $user, Category $category): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Only Admin & Manager can delete category
     */
    public function delete(User $user, Category $category): bool
    {
        // Hanya admin atau manager yang bisa delete
        // Dan pastikan kategori tidak punya produk
        return ($user->isAdmin() || $user->isManager()) && !$category->products()->exists();
    }

    /**
     * Check if user can manage category images
     */
    public function manageImages(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}