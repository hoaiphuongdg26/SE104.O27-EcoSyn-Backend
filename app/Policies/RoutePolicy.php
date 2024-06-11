<?php

namespace App\Policies;

use App\Models\Route;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoutePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->id === $user->staff_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Route $route): bool
    {
        return $user->id === $route->staff_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
         // Chỉ admin có quyền tạo Route
         return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Route $route): bool
    {
        // Chỉ admin có quyền cập nhật Route
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Route $route): bool
    {
        // Chỉ admin có quyền xóa Route
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Route $route): bool
    {
        // Chỉ admin có quyền khôi phục Route
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Route $route): bool
    {
        // Chỉ admin có quyền xóa vĩnh viễn Route
        return $user->isAdmin();
    }
}
