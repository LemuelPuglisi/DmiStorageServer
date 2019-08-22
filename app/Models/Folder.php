<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = [
        'display_name, subfolder_of, course_id'
    ];

    public static $sortableFields = [
        'id', 'influence'
    ];


    public function course()
    {
        return $this->belongsTo(Course::class);
    }


    public function files()
    {
        return $this->hasMany(File::class);
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
