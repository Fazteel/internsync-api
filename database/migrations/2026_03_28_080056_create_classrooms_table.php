<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained('m_majors')->onDelete('cascade');
            $table->string('name', 50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_classrooms');
    }
};