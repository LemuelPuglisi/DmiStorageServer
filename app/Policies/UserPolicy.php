<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    
    /**
     * Allow to see users list
     */
    public function index(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin(); 
    }
    
    /**
     * Allow to see admins list
     */
    public function indexAdmins(User $user)
    {
        return $user->isSuperAdmin(); 
    }

    /**
     *  Allow to change an user role
     *  Deny the role change of the last superAdmin
     */
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

    /**
     *  Allow to delete an user
     */
    public function delete(User $user, User $target) 
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->id === $target->id; 
    }

    /**
     *  Allow to update an user
     */
    public function update(User $user, User $target)
    {
        return $user->isSuperAdmin() || $user->id === $target->id; 
    }

    /**
     *  Allow to get user portability
     */
    public function getPortability(User $user, User $target)
    {
        return $user->isSuperAdmin() || $user->id === $target->id; 
    }
}
