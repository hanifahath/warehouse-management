<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    /**
     * Admin & Manager can view inventory reports
     */
    public function viewInventory(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Admin & Manager can view transaction reports
     */
    public function viewTransactions(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Admin & Manager can view low stock reports
     */
    public function viewLowStock(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Admin can view all reports
     */
    public function viewAll(User $user): bool
    {
        return $user->isAdmin();
    }
}