<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'title',
        'task_id',
        'leader_id',
        'developer_id',
        'reviewStatus',
        'comment',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(tasks::class, 'task_id');
    }

//    public function leader(): BelongsTo
//    {
//        return $this->belongsTo(User::class, 'leader_id');
//    }
}
