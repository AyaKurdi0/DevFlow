<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class projects extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'team_id',
        'status',
        'priority',
    ];

    protected $date = [
        'start_date',
        'end_date',
    ];

    public function task()
    {
        return $this->hasMany(tasks::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
