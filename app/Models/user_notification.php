<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_notification extends Model
{
    use HasFactory;

    protected $table = 'user_notifications';

    protected $fillable = [
        'receiver_id',
        'notification_id',
    ];
}
