<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'google_id',
        'token',
        'refresh_token',
        'data'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
