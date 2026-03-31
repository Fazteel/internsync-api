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
        Schema::create('tr_logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained('tr_internships')->cascadeOnDelete();
            $table->date('date');
            $table->text('activity');
            $table->string('file_path')->nullable();
            $table->enum('status', ['submitted', 'approved', 'revised'])->default('submitted');
            $table->foreignId('approved_by')->nullable()->constrained('m_users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('revision_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_logbooks');
    }
};
