<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name', 'author', 'folder_id'
    ];

    public static $sortableFields = [
        'id', 'influence', 'name', 'created_at', 'updated_at'
    ];

    public function increaseInfluence()
    {
        $this->influence ++;
        $this->save();
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => '[deleted user]',
        ]);
    }
}
