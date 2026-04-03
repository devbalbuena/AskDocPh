<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mood_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('entry_date');
            $table->string('mood'); // happy, sad, anxious, calm, angry, neutral
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'entry_date']); // one entry per user per day
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_entries');
    }
};
