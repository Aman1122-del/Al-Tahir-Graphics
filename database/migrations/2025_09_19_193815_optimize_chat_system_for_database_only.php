<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for better polling performance
        Schema::table('unified_chat_messages', function (Blueprint $table) {
            $table->index(['unified_chat_id', 'is_read', 'created_at']);
            $table->index(['sender_id', 'is_read', 'created_at']);
        });

        Schema::table('unified_chats', function (Blueprint $table) {
            $table->index(['status', 'last_message_at']);
            $table->index(['assigned_to', 'status', 'last_message_at']);
        });

        Schema::table('chat_participants', function (Blueprint $table) {
            $table->index(['user_id', 'is_active', 'last_read_at']);
        });

        // Migrate any existing Message data to UnifiedChat system
        $this->migrateLegacyMessages();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unified_chat_messages', function (Blueprint $table) {
            $table->dropIndex(['unified_chat_id', 'is_read', 'created_at']);
            $table->dropIndex(['sender_id', 'is_read', 'created_at']);
        });

        Schema::table('unified_chats', function (Blueprint $table) {
            $table->dropIndex(['status', 'last_message_at']);
            $table->dropIndex(['assigned_to', 'status', 'last_message_at']);
        });

        Schema::table('chat_participants', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_active', 'last_read_at']);
        });
    }

    /**
     * Migrate legacy Message data to UnifiedChat system.
     */
    private function migrateLegacyMessages()
    {
        // Check if messages table exists
        if (!Schema::hasTable('messages')) {
            return;
        }

        // Get all unique user pairs from legacy messages
        $userPairs = DB::table('messages')
            ->select('sender_id', 'receiver_id')
            ->whereNotNull('sender_id')
            ->whereNotNull('receiver_id')
            ->distinct()
            ->get();

        foreach ($userPairs as $pair) {
            // Create a unified chat for each user pair
            $chat = DB::table('unified_chats')->insertGetId([
                'type' => 'support',
                'title' => 'Legacy Chat Migration',
                'status' => 'active',
                'priority' => 'normal',
                'created_by' => $pair->sender_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add participants
            DB::table('chat_participants')->insert([
                [
                    'unified_chat_id' => $chat,
                    'user_id' => $pair->sender_id,
                    'role' => 'participant',
                    'is_active' => true,
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'unified_chat_id' => $chat,
                    'user_id' => $pair->receiver_id,
                    'role' => 'admin',
                    'is_active' => true,
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);

            // Migrate messages
            $messages = DB::table('messages')
                ->where('sender_id', $pair->sender_id)
                ->where('receiver_id', $pair->receiver_id)
                ->orWhere('sender_id', $pair->receiver_id)
                ->where('receiver_id', $pair->sender_id)
                ->orderBy('created_at')
                ->get();

            foreach ($messages as $message) {
                DB::table('unified_chat_messages')->insert([
                    'unified_chat_id' => $chat,
                    'sender_id' => $message->sender_id,
                    'message' => $message->message,
                    'message_type' => 'text',
                    'file_path' => $message->file_path,
                    'file_name' => $message->file_name,
                    'file_type' => $message->file_type,
                    'is_read' => $message->read_at ? true : false,
                    'read_at' => $message->read_at,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                ]);
            }

            // Update chat's last message time
            $lastMessage = $messages->last();
            if ($lastMessage) {
                DB::table('unified_chats')
                    ->where('id', $chat)
                    ->update(['last_message_at' => $lastMessage->created_at]);
            }
        }
    }
};