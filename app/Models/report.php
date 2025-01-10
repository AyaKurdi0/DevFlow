<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $date = [
        'Report_date',
    ];

    public function task()
    {
        return $this->belongsTo(tasks::class, 'task_id');
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}
