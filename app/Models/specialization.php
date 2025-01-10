<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class specialization extends Model
{
    use HasFactory;

    protected $table = 'specializations';

    protected $fillable = [
        'name',
        'description',
    ];

    public function team_member()
    {
        return $this->hasMany(Team_Members::class);
    }
}
