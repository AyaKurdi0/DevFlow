<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function ownedTeam() :HasOne
    {
        return $this->hasOne(Team::class, 'user_id');
    }

    public function teams() : BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team__members', 'developer_id', 'team_id');
    }


    public function document(): HasMany
    {
        return $this->hasMany(documents::class);
    }

    public function report(): HasMany
    {
        return $this->hasMany(report::class);
    }

    public function task(): BelongsToMany
    {
        return $this->belongsToMany(tasks::class, 'user_tasks', 'developer_id', 'task_id');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
    public function githubAccount() : hasOne
    {
        return $this->hasOne(GitHubAccount::class);
    }

    public function googleAccount() : hasOne
    {
        return $this->hasOne(GoogleUser::class);
    }

    public function hasGithubAccount() : bool
    {
        return $this->githubAccount()->exists();
    }

    public function getGithubInfo()
    {
        return $this->githubAccount ? $this->githubAccount->getBasicInfo() : null;
    }

    public function messages() : HasMany
    {
        return $this->hasMany(Message::class);
    }
}
