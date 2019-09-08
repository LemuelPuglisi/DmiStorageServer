<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;
    

    public function viewAny(User $user)
    {
        return true;
    }

 
    public function view(User $user, Course $course)
    {
        return true;
    }

 
    public function create(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }


    public function update(User $user, Course $course)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->getCoursePermission($course);
    }


    public function delete(User $user, Course $course)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

     
    public function getRequests(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}
