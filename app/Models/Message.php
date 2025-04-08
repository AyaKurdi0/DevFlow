<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'team_id',
        'message',
    ];

    protected $appends = [
        'is_sent_by_me',
        'sender_avatar',
    ];
    protected $with = ['sender.githubAccount'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsSentByMeAttribute(): bool
    {
        return $this->sender_id === auth()->id();
    }

    public function getSenderAvatarAttribute(): ?string
    {
        return optional($this->sender->githubAccount)->avatar;
    }
}
