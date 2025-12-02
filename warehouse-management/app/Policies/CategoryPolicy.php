<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    public function view(User $user, Category $category): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    public function update(User $user, Category $category): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    public function delete(User $user, Category $category): bool
    {
        return in_array($user->role, ['Admin', 'Manager'])
            && $category->products()->count() === 0;
    }
}
