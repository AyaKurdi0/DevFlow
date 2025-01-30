<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team_Members extends Model
{
    use HasFactory;

    protected $table = 'team__members';

    protected $fillable = [
        'team_id',
        'developer_id',
        'specialization_id',
    ];

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(specialization::class, 'specialization_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}
