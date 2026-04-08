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
        Schema::create('m_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('m_users')->cascadeOnDelete();
            $table->string('nip', 50)->nullable()->unique();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('signature_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_teachers');
    }
};
