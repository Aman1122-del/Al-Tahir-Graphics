<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('force_bot_only')->default(false);
            $table->boolean('force_live_only')->default(false);
            $table->json('business_hours')->nullable(); // per weekday: [{day:"mon", start:"09:00", end:"18:00"}, ...]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_settings');
    }
};


