<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Supplier should not have global product list access.
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        // Viewing product details is limited to internal roles.
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        // Hanya admin yang bisa delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update stock manually.
     */
    public function updateStock(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the user can view low stock report.
     */
    public function viewLowStock(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the user can export products.
     */
    public function export(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}