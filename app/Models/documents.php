<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class documents extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'uploaded_by',
        'title',
        'description',
        'document_type',
        'path',
    ];

    protected $date = [
        'uploaded_date',
    ];

    public function task()
    {
        return $this->belongsTo(tasks::class, 'task_id');
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
