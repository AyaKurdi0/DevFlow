<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected array $date = [
        'uploaded_date',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(tasks::class, 'task_id');
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
