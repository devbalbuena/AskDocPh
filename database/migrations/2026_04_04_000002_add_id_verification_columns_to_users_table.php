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
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_document_path')->nullable()->after('cover_photo');
            $table->timestamp('id_verified_at')->nullable()->after('id_document_path');
            $table->enum('id_verification_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('id_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'id_document_path',
                'id_verified_at',
                'id_verification_status'
            ]);
        });
    }
};
