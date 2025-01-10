<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'task_id',
        'leader_id',
        'comment',
    ];

    public function task()
    {
        return $this->belongsTo(tasks::class, 'task_id');
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }
}
