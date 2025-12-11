<?php

namespace App\Policies;

use App\Models\User;

abstract class BasePolicy
{
    protected function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    protected function isManager(User $user): bool
    {
        return $user->hasRole('manager');
    }

    protected function isStaff(User $user): bool
    {
        return $user->hasRole('staff');
    }

    protected function isSupplier(User $user): bool
    {
        return $user->hasRole('supplier') && $user->is_approved;
    }

    protected function isAdminOrManager(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}