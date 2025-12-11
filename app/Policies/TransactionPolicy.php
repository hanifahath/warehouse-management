<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isSupplier()) {
        return false; 
        }
        
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    public function view(User $user, Transaction $transaction): bool
    {
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        if ($user->isStaff()) {
            return $transaction->isCreatedBy($user);
        }

        if ($user->isSupplier()) {
            return false; 
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->isStaff() 
            && $transaction->isCreatedBy($user)
            && $transaction->canBeEdited();
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->isStaff() 
            && $transaction->isCreatedBy($user)
            && $transaction->canBeDeleted();
    }

    public function approve(User $user, Transaction $transaction): bool
    {
        return $user->isManager() && $transaction->canBeApproved();
    }


    public function reject(User $user, Transaction $transaction): bool
    {
        return $user->isManager() && $transaction->canBeApproved();
    }


    public function verify(User $user, Transaction $transaction): bool
    {
        return $user->isManager() 
            && $transaction->isIncoming() 
            && ($transaction->isApproved() || $transaction->isPending());
    }


    public function viewHistory(User $user): bool
    {
        return $user->isStaff();
    }

    public function viewPendingApprovals(User $user): bool
    {
        return $user->isManager();
    }

    public function updateStock(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function viewSupplierDashboard(User $user): bool
    {
        return $user->isSupplier();
    }

    public function viewReports(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function export(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}