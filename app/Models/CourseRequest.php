<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRequest extends Model
{
    protected $fillable = [
        'course_id', 'permissions', 'notes', 'lifespan'
    ];

    public $timestamps = false;

    
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }


    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorizer_id')->withDefault([
            'name' => '[none]',
        ]);
    }
}
