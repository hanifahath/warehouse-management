<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;

class StockMovementPolicy extends BasePolicy
{
    /**
     * Admin & Manager can view stock movements
     */
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $this->isManager($user);
    }

    /**
     * Admin & Manager can view stock movement detail
     */
    public function view(User $user, StockMovement $movement): bool
    {
        return $this->isAdmin($user) || $this->isManager($user);
    }

}