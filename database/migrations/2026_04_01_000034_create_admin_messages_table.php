<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('from_admin_id')->constrained('admins')->cascadeOnDelete();
            $table->foreignId('to_admin_id')->constrained('admins')->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_messages');
    }
};
