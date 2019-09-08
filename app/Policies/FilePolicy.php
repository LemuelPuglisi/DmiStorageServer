<?php

namespace App\Policies;

use App\Models\User;
use App\Models\File;
use App\Models\Course;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilePolicy
{
    use HandlesAuthorization;
    

    public function viewAny(User $user)
    {
        return true;
    }

    
    public function view(User $user, File $file)
    {
        return true;
    }


    public function create(User $user, $course_id)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->getCoursePermission(Course::find($course_id));
        ;
    }


    public function update(User $user, File $file)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->getCoursePermission($file->$folder->course);
    }


    public function delete(User $user, File $file)
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->getCoursePermission($file->$folder->course);
        ;
    }


    public function restore(User $user, File $file)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }


    public function forceDelete(User $user, File $file)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}
