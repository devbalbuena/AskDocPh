<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('rating'); // 1–5
            $table->text('review_text')->nullable();
            $table->timestamps();
            $table->unique(['appointment_id', 'patient_id']); // one review per appointment
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_reviews');
    }
};
