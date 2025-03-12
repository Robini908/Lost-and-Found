<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('items', function ($user) {
    return true;
});

Broadcast::channel('items.{itemId}', function ($user, $itemId) {
    return true; // Add your authorization logic here if needed
});
