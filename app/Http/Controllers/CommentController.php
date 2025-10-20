<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'message' => 'required|string'
        ]);

        $ticket = Ticket::find($request->ticket_id);
        $user = Auth::user();

        // Check if user has access to this ticket
        if ($user->role === 'customer' && $ticket->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment = Comment::create([
            'ticket_id' => $request->ticket_id,
            'user_id' => $user->id,
            'message' => $request->message
        ]);

        $comment->load('user');

        return response()->json($comment, 201);
    }

    public function index($ticketId)
    {
        $ticket = Ticket::find($ticketId);
        $user = Auth::user();

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        if ($user->role === 'customer' && $ticket->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comments = Comment::with('user')->where('ticket_id', $ticketId)->latest()->get();

        return response()->json($comments);
    }
}
