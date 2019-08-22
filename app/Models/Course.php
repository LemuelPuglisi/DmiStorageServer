<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name', 'year', 'cfu'
    ];

    public static $sortableFields = [
        'id', 'year', 'cfu'
    ];


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
        return DB::table('folders')
                ->join('files', 'folders.id', '=', 'files.folder_id')
                ->where('folders.course_id', '=', $this->id)
                ->orderBy('files_influence', 'desc')
                ->limit($limit)
                ->get();
    }
}
