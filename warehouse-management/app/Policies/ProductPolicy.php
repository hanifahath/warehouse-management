<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    public function view(User $user, Product $product): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    public function update(User $user, Product $product): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    public function delete(User $user, Product $product): bool
    {
        return in_array($user->role, ['Admin', 'Manager'])
            && $product->stock === 0;
    }

    public function viewCostPrice(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }
}
