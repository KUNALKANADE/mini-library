<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function view(User $user, Loan $loan): bool
    {
        return $user->id === $loan->user_id || $user->canManageCatalog();
    }

    public function create(User $user): bool
    {
        return true; // any authenticated member can attempt to borrow
    }

    public function update(User $user, Loan $loan): bool
    {
        // "update" here covers returning a book
        return $user->id === $loan->user_id || $user->canManageCatalog();
    }
}
