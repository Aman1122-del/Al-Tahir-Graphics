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
        Schema::create('unified_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unified_chat_id')->constrained('unified_chats')->onDelete('cascade');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('sender_email')->nullable(); // For guest users
            $table->string('sender_name')->nullable(); // For guest users
            $table->text('message');
            $table->string('message_type')->default('text'); // text, file, image, system
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->json('metadata')->nullable(); // Additional message data
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            $table->index(['unified_chat_id', 'created_at']);
            $table->index(['sender_id', 'created_at']);
            $table->index(['is_read', 'created_at']);
            $table->index('sender_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unified_chat_messages');
    }
};