<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_affirmations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('quote')->nullable();
            $table->string('author')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('publish_at')->nullable();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_affirmations');
    }
};
