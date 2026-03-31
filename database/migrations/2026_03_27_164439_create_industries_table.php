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
        Schema::create('m_industries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('address')->nullable();
            $table->string('hr_name', 100);
            $table->string('hr_email', 100)->nullable();
            $table->string('hr_phone', 20)->nullable();
            $table->integer('kuota_siswa');
            $table->boolean('is_active')->default(true);
            $table->string('mou_file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_industries');
    }
};
