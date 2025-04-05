<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GitHubAccount extends Model
{
    use HasFactory;

    protected $table = 'github_accounts';
    protected $fillable = [
        'user_id',
        'github_id',
        'username',
        'email',
        'token',
        'avatar_url',
        'meta'
    ];


    protected $casts = [
        'meta' => 'json'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public static function hasAccount($user_id) : bool
    {
        return self::where('user_id', $user_id)->exists();
    }

    public function getBasicInfo() : array
    {
        return [
            'username' => $this->username,
            'avatar' => $this->avatar_url,
            'github_id' => $this->github_id
        ];
    }
}
