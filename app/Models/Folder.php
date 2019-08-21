<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = [
        'display_name'
    ];

    public static $sortableFields = [
        'id', 'influence'
    ];



    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
