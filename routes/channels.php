<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});*/

/*Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {

    Log::info('channel return .' . (int) $user->id === (int) $receiverId);
    return (int) $user->id === (int) $receiverId;
});*/


Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {
    return (int) $user->id === (int) $receiverId;
});
