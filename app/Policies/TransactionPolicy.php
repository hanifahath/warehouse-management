<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->isSupplier()) {
        return false; // Hapus akses
        }
        
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        if ($user->isStaff()) {
            return $transaction->isCreatedBy($user);
        }

        if ($user->isSupplier()) {
            return false; // Hapus akses
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->isStaff() 
            && $transaction->isCreatedBy($user)
            && $transaction->canBeEdited();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->isStaff() 
            && $transaction->isCreatedBy($user)
            && $transaction->canBeDeleted();
    }

    /**
     * Determine whether the user can approve the transaction.
     */
    public function approve(User $user, Transaction $transaction): bool
    {
        return $user->isManager() && $transaction->canBeApproved();
    }

    /**
     * Determine whether the user can reject the transaction.
     */
    public function reject(User $user, Transaction $transaction): bool
    {
        return $user->isManager() && $transaction->canBeApproved();
    }

    /**
     * Determine whether the user can verify incoming transaction.
     */
    public function verify(User $user, Transaction $transaction): bool
    {
        return $user->isManager() 
            && $transaction->isIncoming() 
            && ($transaction->isApproved() || $transaction->isPending());
    }

    /**
     * Determine whether the user can view transaction history.
     */
    public function viewHistory(User $user): bool
    {
        return $user->isStaff();
    }

    /**
     * Determine whether the user can view pending approvals.
     */
    public function viewPendingApprovals(User $user): bool
    {
        return $user->isManager();
    }

    /**
     * Determine whether the user can update stock after transaction.
     */
    public function updateStock(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the supplier can view their transactions.
     */
    public function viewSupplierDashboard(User $user): bool
    {
        return $user->isSupplier();
    }

    /**
     * Determine whether the user can view reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the user can export transactions.
     */
    public function export(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}