<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('orders', function (User $user) {
    return in_array($user->role, ['admin', 'moderator']);
});
