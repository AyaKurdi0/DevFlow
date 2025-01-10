<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    protected $table = 'notifications';

    protected $fillable = [
        'type',
        'title',
        'content',
        'read_state',
        'receive_state'
    ];

    protected $date = [
        'sent_at',
        'receive_at'
    ];

    public function user_notification()
    {
        return $this->belongsToMany(User::class,'user_notification','notification_id','receiver_id');
    }
}
