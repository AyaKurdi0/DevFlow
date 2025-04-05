<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = [
        'team_name',
        'member_count',
        'user_id',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(projects::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }


    public function members() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team__members', 'team_id', 'developer_id');
    }

    public function messages() : HasMany
    {
        return $this->hasMany(Message::class);
    }
}
