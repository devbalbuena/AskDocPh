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
        Schema::create('community_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('community_poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('community_polls')->cascadeOnDelete();
            $table->string('text');
            $table->timestamps();
        });

        Schema::create('community_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('community_polls')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('community_poll_options')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            // 1. One vote per user per poll enforced
            $table->unique(['poll_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_poll_votes');
        Schema::dropIfExists('community_poll_options');
        Schema::dropIfExists('community_polls');
    }
};
