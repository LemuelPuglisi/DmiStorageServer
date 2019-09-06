<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    protected $fillable = [
        'name', 'year', 'cfu'
    ];

    public static $sortableFields = [
        'id', 'year', 'cfu', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id')->withDefault([
                'name' => '[deleted user]',            
        ]);
    }


    public function folders()
    {
        return $this->hasMany(Folder::class);
    }


    public function rootFolders()
    {
        return $this->folders()->where('subfolder_of', null);
    }


    public function orderedFolders($param, $order)
    {
        return $this->folders()->orderBy($param, $order)->get();
    }


    public function mostViewedFiles($limit)
    {
        return DB::table('files')
                ->select('files.*')
                ->join('folders', 'folders.id', '=', 'files.folder_id')
                ->where('folders.course_id', '=', $this->id)
                ->orderBy('files.influence', 'desc')
                ->limit($limit)
                ->get();
    }

    
    public function requests()
    {
        return $this->hasMany(CourseRequest::class);
    }


    public function requestsByStatus($status) 
    {
        return CourseRequest::all()->where('status', $status);
    }
}
