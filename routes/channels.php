<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channel Definitions
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    // Example: allow only the ticket owner to listen
    // $ticket = \App\Models\Ticket::find($ticketId);
    // return $ticket && $user && $user->id === $ticket->user_id;

    // For now, allow authenticated users; change to your authorization logic
    return $user != null;
});
