<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = [
        'team_name',
        'Description',
        'member_count',
        'user_id',
    ];

    public function projects()
    {
        return $this->hasMany(projects::class);
    }

    public function team_member()
    {
        return $this->hasMany(Team_Members::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
