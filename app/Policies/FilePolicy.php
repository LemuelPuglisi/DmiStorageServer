<?php

namespace App\Policies;

use App\Models\User;
use App\Models\File;
use App\Models\Course;
use App\Models\Folder;
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


    public function create(User $user, $course_id, $folder_id)
    {
        $permissions = $user->getFolderPermission(Folder::find($folder_id));
        if ($permissions != null && $permissions['manage']) {
            return true;
        }
        return $user->isAdmin() || $user->isSuperAdmin() || $user->getCoursePermission(Course::find($course_id));
    }


    public function update(User $user, File $file)
    {
        $permissions = $user->getFolderPermission($file->folder);
        if ($permissions !== null && $permissions['manage']) {
            return true;
        }
        return $user->isAdmin() || $user->isSuperAdmin() || $user->getCoursePermission($file->$folder->course);
    }


    public function delete(User $user, File $file)
    {
        $permissions = $user->getFolderPermission($file->folder);
        if ($permissions !== null && $permissions['remove']) {
            return true;
        }
        return $user->isAdmin() || $user->isSuperAdmin() || $user->getCoursePermission($file->$folder->course);
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
