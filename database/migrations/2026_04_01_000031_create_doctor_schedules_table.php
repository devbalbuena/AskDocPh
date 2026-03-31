<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->string('day_of_week')->nullable(); // enum from ERD
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('slot_duration_minutes')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};
