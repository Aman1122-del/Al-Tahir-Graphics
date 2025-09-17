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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('chat_email_notifications')->default(true);
            $table->string('chat_admin_email')->nullable();
            $table->string('chat_smtp_host')->nullable();
            $table->integer('chat_smtp_port')->nullable();
            $table->string('chat_smtp_username')->nullable();
            $table->string('chat_smtp_password')->nullable();
            $table->boolean('chat_smtp_encryption')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'chat_email_notifications',
                'chat_admin_email',
                'chat_smtp_host',
                'chat_smtp_port',
                'chat_smtp_username',
                'chat_smtp_password',
                'chat_smtp_encryption',
            ]);
        });
    }
};