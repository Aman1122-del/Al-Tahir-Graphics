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
        Schema::create('unified_chats', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('support'); // support, internal, group
            $table->string('title')->nullable();
            $table->string('status')->default('active'); // active, closed, archived
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->json('metadata')->nullable(); // Additional data like topic, tags, etc.
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // User who created the chat
            $table->unsignedBigInteger('assigned_to')->nullable(); // Admin/support assigned to chat
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['assigned_to', 'status']);
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unified_chats');
    }
};