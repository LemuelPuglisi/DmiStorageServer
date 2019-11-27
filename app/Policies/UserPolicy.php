<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    

    public function show(User $user, User $target)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->id === $target->id;
    }


    public function index(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }


    public function indexAdmins(User $user)
    {
        return $user->isSuperAdmin();
    }


    public function changeRoles(User $user, User $target)
    {
        if ($target->isSuperAdmin()) {
            $superAdminsLefts = User::where('role', '3')->count();
            if ($superAdminsLefts < 2) {
                return false;
            }
        }

        return $user->isSuperAdmin();
    }


    public function delete(User $user, User $target)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->id === $target->id;
    }


    public function update(User $user, User $target)
    {
        return $user->isSuperAdmin() || $user->id === $target->id;
    }


    public function deleteTokens(User $user, User $target)
    {
        return $user->isSuperAdmin() || $user->id === $target->id;
    }

 
    public function getPortability(User $user, User $target)
    {
        return $user->isSuperAdmin() || $user->id === $target->id;
    }



    public function getRequests(User $user, User $target)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->id === $target->id;
    }
}
