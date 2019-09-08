<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CourseRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAll(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }


    public function view(User $user, CourseRequest $courseRequest)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->id == $courseRequest->user_id;
    }


    public function create(User $user)
    {
        return !$user->isAdmin() && !$user->isSuperAdmin();
    }


    public function upgrade(User $user, CourseRequest $courseRequest)
    {
        return !$user->isAdmin() && !$user->isSuperAdmin();
    }


    public function delete(User $user, CourseRequest $courseRequest)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->id == $courseRequest->user_id;
    }


    public function manage(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}
