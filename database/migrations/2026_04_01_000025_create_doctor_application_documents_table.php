<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_application_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('doctor_application_id')->constrained('doctor_applications')->cascadeOnDelete();
            $table->foreignId('doctor_requirement_id')->constrained('doctor_requirements')->cascadeOnDelete();
            $table->string('document_type')->nullable(); // enum from ERD
            $table->string('file_path')->nullable();
            $table->text('text_value')->nullable();
            $table->string('status')->nullable(); // enum from ERD
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_application_documents');
    }
};
