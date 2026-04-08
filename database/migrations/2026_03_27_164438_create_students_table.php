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
        Schema::create('m_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('m_users')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('m_academic_years');
            $table->string('nis', 50)->unique();
            $table->string('name', 100);
            $table->string('jurusan', 100)->nullable();
            $table->string('kelas', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_pkl')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_students');
    }
};
