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
        Schema::create('tr_application_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('tr_internship_applications')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('m_students');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_applications_students');
    }
};
