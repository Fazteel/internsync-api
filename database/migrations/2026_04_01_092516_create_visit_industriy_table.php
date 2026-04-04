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
        Schema::create('tr_visit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembimbing_id')->constrained('m_users')->cascadeOnDelete();
            $table->foreignId('industry_id')->constrained('m_industries')->cascadeOnDelete();
            $table->date('planned_date');
            $table->text('purpose');
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_visit_requests');
    }
};
