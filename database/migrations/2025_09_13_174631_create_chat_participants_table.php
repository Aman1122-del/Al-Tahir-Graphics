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
        Schema::create('chat_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unified_chat_id')->constrained('unified_chats')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('participant_email')->nullable(); // For guest participants
            $table->string('participant_name')->nullable(); // For guest participants
            $table->string('role')->default('participant'); // participant, admin, support
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['unified_chat_id', 'user_id']);
            $table->unique(['unified_chat_id', 'participant_email']);
            $table->index(['user_id', 'is_active']);
            $table->index(['participant_email', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_participants');
    }
};