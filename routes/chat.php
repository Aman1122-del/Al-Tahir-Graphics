<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatController;

// Public chatbot endpoints
Route::prefix('chatbot')->group(function () {
    Route::get('settings', [ChatbotController::class, 'settings'])->name('chatbot.settings');
    Route::post('start', [ChatbotController::class, 'start'])->name('chatbot.start');
    Route::post('{session}/menu', [ChatbotController::class, 'menu'])->name('chatbot.menu');
    Route::post('{session}/message', [ChatbotController::class, 'message'])->name('chatbot.message');
    Route::post('{session}/contact', [ChatbotController::class, 'contact'])->name('chatbot.contact');
    Route::post('{session}/upload', [ChatbotController::class, 'upload'])->name('chatbot.upload');
    Route::post('{session}/ticket', [ChatbotController::class, 'ticket'])->name('chatbot.ticket');
    Route::post('{session}/offer-agent', [ChatbotController::class, 'offerAgent'])->name('chatbot.offerAgent');
    Route::post('{session}/accept-agent', [ChatbotController::class, 'acceptAgent'])->name('chatbot.acceptAgent');
    Route::get('{session}/poll', [ChatbotController::class, 'poll'])->name('chatbot.poll');
});

// Authenticated internal chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/start', [ChatController::class, 'startChat'])->name('chat.start');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/messages/{chat_id}', [ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/chat/mark-read', [ChatController::class, 'markAsRead'])->name('chat.mark-read');
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
    Route::get('/chat/users', [ChatController::class, 'getChatUsers'])->name('chat.users');
});
