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
        Schema::create('tr_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained('tr_internships')->cascadeOnDelete();
            $table->date('date');
            $table->enum('type', ['sick', 'leave']);
            $table->string('reason');
            $table->string('attachment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_permissions');
    }
};
