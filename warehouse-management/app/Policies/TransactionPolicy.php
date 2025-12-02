<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['Staff', 'Manager', 'Admin']);
    }

    public function view(User $user, Transaction $transaction): bool
    {
        if (in_array($user->role, ['Manager', 'Admin'])) {
            return true;
        }

        return $user->role === 'Staff' && $transaction->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'Staff';
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->role === 'Staff'
            && $transaction->status === 'Pending'
            && $transaction->created_by === $user->id;
    }

    public function approve(User $user, Transaction $transaction): bool
    {
        return in_array($user->role, ['Manager', 'Admin'])
            && $transaction->status === 'Pending Approval';
    }

    public function reject(User $user, Transaction $transaction): bool
    {
        return in_array($user->role, ['Manager', 'Admin'])
            && $transaction->status === 'Pending Approval';
    }
}
