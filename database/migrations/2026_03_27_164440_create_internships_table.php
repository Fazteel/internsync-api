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
            $table->foreignId('student_id')->constrained('m_students');
            $table->foreignId('industry_id')->nullable()->constrained('m_industries');
            $table->foreignId('pembimbing_id')->nullable()->constrained('m_users');
            $table->foreignId('coordinator_id')->nullable()->constrained('m_users');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_month')->nullable();
            $table->boolean('is_extended')->default(false);
            $table->string('status', 50)->default('pending');
            $table->text('cancelled_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
