<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FolderRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderRequestPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any folder requests.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the folder request.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FolderRequest  $folderRequest
     * @return mixed
     */
    public function view(User $user, FolderRequest $folderRequest)
    {
        //
    }

    /**
     * Determine whether the user can create folder requests.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the folder request.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FolderRequest  $folderRequest
     * @return mixed
     */
    public function update(User $user, FolderRequest $folderRequest)
    {
        //
    }

    /**
     * Determine whether the user can delete the folder request.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FolderRequest  $folderRequest
     * @return mixed
     */
    public function delete(User $user, FolderRequest $folderRequest)
    {
        //
    }

    /**
     * Determine whether the user can restore the folder request.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FolderRequest  $folderRequest
     * @return mixed
     */
    public function restore(User $user, FolderRequest $folderRequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the folder request.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FolderRequest  $folderRequest
     * @return mixed
     */
    public function forceDelete(User $user, FolderRequest $folderRequest)
    {
        //
    }
}
