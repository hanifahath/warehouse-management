<?php

namespace App\Policies;

use App\Models\RestockOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RestockOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RestockOrder $restockOrder): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RestockOrder $restockOrder): bool
    {
        return false;
    }

    public function confirm(User $user, RestockOrder $order): bool
    {
        return $user->role === 'Supplier';
    }

    public function receive(User $user, RestockOrder $order): bool
    {
        return in_array($user->role, ['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RestockOrder $restockOrder): bool
    {
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RestockOrder $restockOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RestockOrder $restockOrder): bool
    {
        return false;
    }


}
