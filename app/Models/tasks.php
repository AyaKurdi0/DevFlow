<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'estimated_end_date',
        'due_date',
        'start_date',
        'estimated_start_date',
    ];


    public function document(): HasMany
    {
        return $this->hasMany(documents::class, 'task_id', 'id');
    }

    public function report(): HasMany
    {
        return $this->hasMany(report::class, 'task_id', 'id');
    }

    public function developers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tasks', 'task_id', 'developer_id')->distinct();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(projects::class, 'project_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(review::class, 'task_id');
    }
}
