<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class report extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
        'task_id',
        'title',
        'description',
        'developer_id',
        'content',
        'Report_time',
    ];

    protected array $date = [
        'Report_date',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(tasks::class, 'task_id');
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}
