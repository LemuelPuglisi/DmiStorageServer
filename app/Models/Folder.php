<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = [
        'display_name', 'subfolder_of', 'course_id',
    ];

    public static $sortableFields = [
        'id', 'influence', 'created_at', 'updated_at'
    ];


    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }


    public function orderedFiles($param, $order)
    {
        return $this->files()->orderBy($param, $order)->get();
    }


    public function parent()
    {
        return $this->belongsTo(Folder::class, 'subfolder_of');
    }

    
    public function subfolders()
    {
        return $this->hasMany(Folder::class, 'subfolder_of');
    }


    public function increaseInfluence()
    {
        $this->influence ++;
        $this->save();
    }
}
