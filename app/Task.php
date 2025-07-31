<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';
    
    protected $fillable = [
        'task_name',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];
}
