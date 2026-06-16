<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_referrals', function (Blueprint $table) {
            $table->id();
            // The doctor who sends the referral
            $table->foreignId('referring_doctor_id')->constrained('users')->cascadeOnDelete();
            // The doctor being referred to
            $table->foreignId('referred_to_doctor_id')->constrained('users')->cascadeOnDelete();
            // The patient being referred
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            // The completed appointment that triggered this referral
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->string('reason');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_referrals');
    }
};
