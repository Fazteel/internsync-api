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
        Schema::create('m_user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('m_users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('m_roles')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_user_roles');
    }
};
