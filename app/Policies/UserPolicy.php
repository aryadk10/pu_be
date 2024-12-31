<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->role === 'super_admin' || $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'admin' && $model->role === 'user';
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // Superadmin dapat membuat admin atau user, admin hanya bisa membuat user
        return $user->role === 'super_admin' || $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {

        // Superadmin dapat mengubah admin atau user
        // Admin hanya dapat mengubah user biasa, tidak dapat mengubah admin
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->id == $model->id) {
            return true;
        }

        return $user->role === 'admin' && $model->role === 'user';
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        // Superadmin dapat menghapus admin atau user
        // Admin hanya bisa menghapus user biasa, tidak dapat menghapus admin
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->id == $model->id) {
            return true;
        }

        return $user->role === 'admin' && $model->role === 'user';
    }
}
