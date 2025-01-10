<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function team_member()
    {
        return $this->hasMany(Team_Members::class, 'developer_id');
    }

    public function team()
    {
        return $this->hasOne(Team::class);
    }

    public function document()
    {
        return $this->hasMany(documents::class);
    }

    public function report()
    {
        return $this->hasMany(report::class);
    }

    public function task()
    {
        return $this->belongsToMany(tasks::class, 'user_task', 'developer_id', 'task_id');
    }

    public function notification()
    {
        return $this->belongsToMany(Notifications::class, 'user_notification', 'receiver_id', 'notification_id');
    }
}
