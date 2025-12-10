<?php

namespace App\Policies;

use App\Models\User;

abstract class BasePolicy
{
    /**
     * Check if user is admin
     */
    protected function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Check if user is manager
     */
    protected function isManager(User $user): bool
    {
        return $user->hasRole('manager');
    }

    /**
     * Check if user is staff
     */
    protected function isStaff(User $user): bool
    {
        return $user->hasRole('staff');
    }

    /**
     * Check if user is supplier (and approved)
     */
    protected function isSupplier(User $user): bool
    {
        return $user->hasRole('supplier') && $user->is_approved;
    }

    /**
     * Check if user is admin or manager
     */
    protected function isAdminOrManager(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}