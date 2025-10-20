<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ChatController;


// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Tickets
    Route::apiResource('tickets', TicketController::class);

    // Comments
    Route::get('/tickets/{ticketId}/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);

    // Chat
    Route::get('/tickets/{ticketId}/chat', [ChatController::class, 'getMessages']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);

    // User
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
