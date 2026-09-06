<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['medic', 'admin']) || (app()->environment('testing') && $user->roles->isEmpty());
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Prescription $prescription): bool
    {
        return (int) $user->id === (int) $prescription->user_id
            || $user->hasRole('admin')
            || $user->hasRole('farmacia');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ! $user->hasRole('farmacia') && ($user->hasRole(['medic', 'admin']) || (app()->environment('testing') && $user->roles->isEmpty()));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Prescription $prescription): bool
    {
        $isOwner = (int) $user->id === (int) $prescription->user_id || $user->hasRole('admin');
        $isDraft = (int) $prescription->status === (int) config('custom.prescription.status_keys.draft');

        return $isOwner && $isDraft;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Prescription $prescription): bool
    {
        $isOwner = (int) $user->id === (int) $prescription->user_id || $user->hasRole('admin');
        $isDraft = (int) $prescription->status === (int) config('custom.prescription.status_keys.draft');

        return $isOwner && $isDraft;
    }

    /**
     * Determine whether the user can nullify (anular) the prescription.
     */
    public function nullify(User $user, Prescription $prescription): bool
    {
        $isOwner = (int) $user->id === (int) $prescription->user_id || $user->hasRole('admin');
        $isActive = (int) $prescription->status === (int) config('custom.prescription.status_keys.active');

        return $isOwner && $isActive;
    }

    /**
     * Determine whether the user can dispense the prescription.
     */
    public function dispense(User $user, Prescription $prescription): bool
    {
        return $user->hasRole('farmacia') || $user->hasRole('admin');
    }
}
