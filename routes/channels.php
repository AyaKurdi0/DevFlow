<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    return $user->teams()->where('id', $teamId)->exists();
});
