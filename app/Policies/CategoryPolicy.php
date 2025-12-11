<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    public function view(User $user, Category $category): bool
    {
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }


    public function update(User $user, Category $category): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function delete(User $user, Category $category): bool
    {
        return ($user->isAdmin() || $user->isManager()) && !$category->products()->exists();
    }

    public function manageImages(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}