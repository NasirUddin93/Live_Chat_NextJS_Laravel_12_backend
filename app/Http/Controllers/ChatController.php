<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'message' => 'required|string'
        ]);

        $ticket = Ticket::find($request->ticket_id);
        $user = Auth::user();

        // Determine receiver
        if ($user->role === 'admin') {
            $receiver = User::find($ticket->user_id);
        } else {
            $receiver = User::where('role', 'admin')->first();
        }

        if (!$receiver) {
            return response()->json(['message' => 'Receiver not found'], 404);
        }

        $chat = Chat::create([
            'ticket_id' => $request->ticket_id,
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message
        ]);

        $chat->load(['sender', 'receiver']);

        // Broadcast event for real-time (you can implement Pusher/Broadcasting here)
        // broadcast(new ChatMessage($chat))->toOthers();

        return response()->json($chat, 201);
    }

    public function getMessages($ticketId)
    {
        $ticket = Ticket::find($ticketId);
        $user = Auth::user();

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        if ($user->role === 'customer' && $ticket->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = Chat::with(['sender', 'receiver'])
            ->where('ticket_id', $ticketId)
            ->latest()
            ->get();

        return response()->json($messages);
    }
}
