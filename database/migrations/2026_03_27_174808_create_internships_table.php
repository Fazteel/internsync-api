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
        Schema::create('tr_internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('tr_internship_applications');
            $table->foreignId('student_id')->constrained('m_students');
            $table->foreignId('industry_id')->constrained('m_industries');
            $table->foreignId('pembimbing_id')->constrained('m_users');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_extended')->default(false);
            $table->string('status', 50)->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_internships');
    }
};
