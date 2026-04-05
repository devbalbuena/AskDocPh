<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmark_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('bookmark_collections')->cascadeOnDelete();
            $table->foreignId('post_bookmark_id')->constrained('post_bookmarks')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmark_collection_items');
    }
};
