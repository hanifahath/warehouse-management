<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    public function view(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    public function updateStock(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function viewLowStock(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function export(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}