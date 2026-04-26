<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('admin', function ($user) {
    return $user->role === 'admin' || $user->is_admin == 1; // Tùy auth
});
