<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;

class FilePolicy
{
    /**
     * Determine whether the user can view the file.
     */
    public function view(User $user, File $file): bool
    {
        return (int) $user->id === (int) $file->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the file metadata.
     */
    public function update(User $user, File $file): bool
    {
        return (int) $user->id === (int) $file->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the file.
     */
    public function delete(User $user, File $file): bool
    {
        return (int) $user->id === (int) $file->user_id || $user->hasRole('admin');
    }
}
