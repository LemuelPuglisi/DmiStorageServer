<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolderRequest extends Model
{
    protected $fillable = [
         'folder_id', 'permissions', 'notes', 'lifespan'
    ];

    public $timestamps = false;

    
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function previousRequest()
    {
        return $this->belongsTo(FolderRequest::class, 'is_upgrade_of')->withDefault(null);
    }

    public function upgrade()
    {
        return $this->hasOne(FolderRequest::class, 'is_upgrade_of')->withDefault(null);
    }

    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorizer_id')->withDefault([
            'name' => '[none]',
        ]);
    }
}
