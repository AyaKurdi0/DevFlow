<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team_Members extends Model
{
    use HasFactory;

    protected $table = 'team__members';

    protected $fillable = [
        'team_id',
        'developer_id',
        'specialization_id',
    ];

    public function specialization()
    {
        return $this->belongsTo(specialization::class, 'specialization_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}
