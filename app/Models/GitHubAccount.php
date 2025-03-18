<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitHubAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'github_id',
        'avatar',
        'github_token',
        'github_refresh_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
