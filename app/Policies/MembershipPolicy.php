<?php

namespace App\Policies;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembershipPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-memberships'); // Admin permission
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Membership $membership): bool
    {
        return $user->id === $membership->user_id || $user->can('view-memberships');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can attempt to apply
    }

    /**
     * Determine whether the user can update the model.
     * This 'manage' can be used for payment form access etc.
     */
    public function update(User $user, Membership $membership): bool
    {
         return $user->id === $membership->user_id || $user->can('manage-memberships');
    }

    public function manage(User $user, Membership $membership): bool
    {
         return $user->id === $membership->user_id; // Only owner can manage (e.g. make payment for) their application
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Membership $membership): bool
    {
        return $user->can('manage-memberships'); // Admin only
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Membership $membership): bool
    {
         return $user->can('manage-memberships');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Membership $membership): bool
    {
        return $user->can('manage-memberships');
    }
}
