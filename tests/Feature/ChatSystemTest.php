<?php

use App\Models\User;
use App\Models\UnifiedChat;
use App\Models\UnifiedChatMessage;
use App\Models\ChatParticipant;

test('user can start a chat', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->postJson('/chat/start', [
            'message' => 'Hello, I need help',
            'topic' => 'Support'
        ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('unified_chats', [
        'created_by' => $user->id,
        'status' => 'active'
    ]);
});

test('user can send a message', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    
    // Create a chat
    $chat = UnifiedChat::create([
        'type' => 'support',
        'title' => 'Test Chat',
        'status' => 'active',
        'priority' => 'normal',
        'created_by' => $user->id,
    ]);

    // Add participants
    $chat->addParticipant($user->id, null, null, 'participant');
    $chat->addParticipant($admin->id, null, null, 'admin');

    $response = $this->actingAs($user)
        ->postJson('/chat/send', [
            'chat_id' => $chat->id,
            'message' => 'Test message'
        ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('unified_chat_messages', [
        'unified_chat_id' => $chat->id,
        'sender_id' => $user->id,
        'message' => 'Test message'
    ]);
});

test('user can fetch messages', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    
    // Create a chat
    $chat = UnifiedChat::create([
        'type' => 'support',
        'title' => 'Test Chat',
        'status' => 'active',
        'priority' => 'normal',
        'created_by' => $user->id,
    ]);

    // Add participants
    $chat->addParticipant($user->id, null, null, 'participant');
    $chat->addParticipant($admin->id, null, null, 'admin');

    // Create a message
    UnifiedChatMessage::create([
        'unified_chat_id' => $chat->id,
        'sender_id' => $admin->id,
        'message' => 'Hello from admin',
        'message_type' => 'text',
    ]);

    $response = $this->actingAs($user)
        ->getJson("/chat/messages/{$chat->id}");

    $response->assertStatus(200)
        ->assertJson(['success' => true])
        ->assertJsonCount(1, 'messages');
});

test('user can get unread count', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    
    // Create a chat
    $chat = UnifiedChat::create([
        'type' => 'support',
        'title' => 'Test Chat',
        'status' => 'active',
        'priority' => 'normal',
        'created_by' => $user->id,
    ]);

    // Add participants
    $chat->addParticipant($user->id, null, null, 'participant');
    $chat->addParticipant($admin->id, null, null, 'admin');

    // Create unread messages
    UnifiedChatMessage::create([
        'unified_chat_id' => $chat->id,
        'sender_id' => $admin->id,
        'message' => 'Unread message 1',
        'message_type' => 'text',
        'is_read' => false,
    ]);

    UnifiedChatMessage::create([
        'unified_chat_id' => $chat->id,
        'sender_id' => $admin->id,
        'message' => 'Unread message 2',
        'message_type' => 'text',
        'is_read' => false,
    ]);

    $response = $this->actingAs($user)
        ->getJson('/chat/unread-count');

    $response->assertStatus(200)
        ->assertJson(['success' => true])
        ->assertJson(['count' => 2]);
});

test('user can mark messages as read', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    
    // Create a chat
    $chat = UnifiedChat::create([
        'type' => 'support',
        'title' => 'Test Chat',
        'status' => 'active',
        'priority' => 'normal',
        'created_by' => $user->id,
    ]);

    // Add participants
    $chat->addParticipant($user->id, null, null, 'participant');
    $chat->addParticipant($admin->id, null, null, 'admin');

    // Create unread messages
    UnifiedChatMessage::create([
        'unified_chat_id' => $chat->id,
        'sender_id' => $admin->id,
        'message' => 'Unread message',
        'message_type' => 'text',
        'is_read' => false,
    ]);

    $response = $this->actingAs($user)
        ->postJson('/chat/mark-read', [
            'chat_id' => $chat->id
        ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('unified_chat_messages', [
        'unified_chat_id' => $chat->id,
        'sender_id' => $admin->id,
        'is_read' => true
    ]);
});
