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
        Schema::create('tr_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained('tr_internships')->cascadeOnDelete();
            $table->foreignId('visit_request_id')->nullable()->constrained('tr_visit_requests')->nullOnDelete();
            $table->foreignId('evaluator_id')->constrained('m_users');
            $table->date('evaluation_date');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['monthly', 'final', 'custom'])->default('custom');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_evaluations');
    }
};
