<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class projects extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'team_id',
        'due_date',
        'status',
        'priority',
        'start_date',
        'end_date',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(tasks::class, 'project_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
