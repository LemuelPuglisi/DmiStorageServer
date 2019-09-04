<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'course_id', 'folder_id', 'permissions', 'notes', 'lifespan'    
    ]; 


    public $permissionSchema; 

    public function __constructor()
    {
        $this->$permissionSchema = [
            'course' => [
                'update' => false, 
                'delete' => false, 
                'create' => false, 
                'global' => false, 
            ], 
            'folder' => [
                'update' => false, 
                'delete' => false,
                'manage' => false, 
                'remove' => false,
            ],
        ];
    }


    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }


    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorizer_id')->withDefault([
            'name' => '[none]',            
        ]); 
    }


    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id'); 
    }    

    
    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id')->withDefault([
            'folder' => '[none]'
        ]);
    }
}
