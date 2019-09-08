<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\File;
use App\Models\CourseRequest;
use App\Models\FolderRequest;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function isUser()
    {
        return $this->role == 1;
    }

    public function isAdmin()
    {
        return $this->role == 2;
    }

    public function isSuperAdmin()
    {
        return $this->role == 3;
    }

    /**
     * Reset password customization
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    public function courseRequests()
    {
        return $this->hasMany(CourseRequest::class);
    }

    public function courseRequestsByStatus(string $status)
    {
        return $this->hasMany(CourseRequest::class)->where('status', $status)->get();
    }

    public function folderRequests()
    {
        return $this->hasMany(FolderRequest::class);
    }

    public function folderRequestsByStatus(string $status)
    {
        return $this->hasMany(FolderRequest::class)->where('status', $status)->get();
    }

    public function getCoursePermission(Course $course)
    {
        $request = CourseRequest::where('user_id', $this->id)
        ->where('status', 'active')
        ->where('course_id', $course->id)
        ->get();

        if ($request->isEmpty()) {
            return false;
        }
        return true;
    }
}
