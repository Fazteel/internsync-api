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
            $table->string('nis', 50)->unique();
            $table->string('jurusan', 100)->nullable();
            $table->string('kelas', 50)->nullable();
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
