<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Request;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestPolicy
{
    use HandlesAuthorization;
    
    public function see(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin(); 
    }


    public function view(User $user, Request $request)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->id === $request->user_id; 
    }


    public function create(User $user)
    {
        return !$user->isAdmin() && !$user->isSuperAdmin(); 
    }


    public function update(User $user, Request $request)
    {
        //
    }


    public function delete(User $user, Request $request)
    {
        //
    }

}
