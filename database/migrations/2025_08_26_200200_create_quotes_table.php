<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->text('project_description');
            $table->text('requirements')->nullable();
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'expired'])->default('draft');
            $table->unsignedBigInteger('assigned_designer_id')->nullable();
            $table->date('valid_until');
            $table->text('admin_notes')->nullable();
            $table->text('designer_notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_designer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
