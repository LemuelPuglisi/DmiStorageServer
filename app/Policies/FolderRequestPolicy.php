<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FolderRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAll(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin(); 
    }


    public function view(User $user, FolderRequest $folderRequest)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->id == $folderRequest->user_id; 
    }


    public function create(User $user)
    {
        return !$user->isAdmin() && !$user->isSuperAdmin(); 
    }


    public function upgrade(User $user, FolderRequest $folderRequest)
    {
        return !$user->isAdmin() && !$user->isSuperAdmin() && $user->id == $folderRequest->user_id;
    }


    public function delete(User $user, FolderRequest $folderRequest)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->id == $folderRequest->user_id;         
    }


    public function manage(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}
