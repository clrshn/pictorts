<?php

namespace App\Policies;

use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FinancialRecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view financial records list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FinancialRecord $financial): bool
    {
        return true; // All authenticated users can view financial records
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create financial records
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FinancialRecord $financial): bool
    {
        return true; // All authenticated users can update financial records
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FinancialRecord $financial): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can route the financial record.
     */
    public function route(User $user, FinancialRecord $financial): bool
    {
        return true; // All authenticated users can route financial records
    }

    /**
     * Determine whether the user can receive the financial record.
     */
    public function receive(User $user, FinancialRecord $financial): bool
    {
        return true; // All authenticated users can receive financial records
    }
}
