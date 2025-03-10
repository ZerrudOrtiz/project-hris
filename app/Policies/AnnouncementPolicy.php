<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
   /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Announcement $model): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Announcement $model): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Announcement $model): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Announcement $model): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Announcement $model): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }
}
