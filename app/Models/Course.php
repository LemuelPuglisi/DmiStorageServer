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
}
