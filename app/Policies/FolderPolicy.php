<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Folder;
use App\Models\Course;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderPolicy
{
    use HandlesAuthorization;
    

    public function viewAny(User $user)
    {
        return true;
    }


    public function view(User $user, Folder $folder)
    {
        return true;
    }


    public function create(User $user, $course_id)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->getCoursePermission(Course::find($course_id));
    }


    public function update(User $user, Folder $folder)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->getCoursePermission($folder->course);
    }


    public function delete(User $user, Folder $folder)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->getCoursePermission($folder->course);
        ;
    }

    public function getRequests(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}
