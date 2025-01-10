<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tasks extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'description',
        'status',
        'estimated_time_inDays',
        'actual_time_inDays',
        'project_id',
        'priority',
    ];

    protected $date = [
        'estimated_end_date',
        'due_date',
        'start_date',
        'estimated_start_date',
    ];

    public function document()
    {
        return $this->hasMany(documents::class);
    }

    public function report()
    {
        return $this->hasMany(report::class);
    }

    public function user_task()
    {
        return $this->belongsToMany(User::class, 'user_task', 'task_id', 'developer_id');
    }

    public function project()
    {
        return $this->belongsTo(projects::class, 'project_id');
    }
}
