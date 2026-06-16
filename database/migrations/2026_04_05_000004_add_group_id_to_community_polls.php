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
        // Add group_id to community_polls
        Schema::table('community_polls', function (Blueprint $table) {
            $table->foreignId('group_id')
                ->nullable()
                ->after('user_id')
                ->constrained('groups')
                ->cascadeOnDelete();
            $table->timestamp('ends_at')->nullable()->after('question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('community_polls', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn(['group_id', 'ends_at']);
        });
    }
};
