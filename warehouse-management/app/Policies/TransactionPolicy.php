<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Hanya Manager/Admin boleh approve transaksi.
     */
    public function approve(User $user, Transaction $transaction): bool
    {
        return in_array($user->role, ['Manager', 'Admin']);
    }

    /**
     * Hanya Manager/Admin boleh reject transaksi.
     */
    public function reject(User $user, Transaction $transaction): bool
    {
        return in_array($user->role, ['Manager', 'Admin']);
    }

    /**
     * Staff boleh membuat transaksi, Admin/Manager boleh delete.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

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
    public function view(User $user, Transaction $transaction): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['Staff', 'Manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return false;
    }
}
