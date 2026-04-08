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
        Schema::create('tr_internship_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('coordinator_id')->constrained('m_users');
            $table->foreignId('industry_id')->constrained('m_industries');
            $table->foreignId('pembimbing_id')->constrained('m_users');
            $table->date('suggested_start_date');
            $table->date('suggested_end_date');
            $table->date('departure_date')->nullable();
            $table->enum('duration_option', ['3_bulan', '6_bulan', 'custom'])->nullable();
            $table->date('final_end_date')->nullable();
            $table->enum('status', [
                'draft',
                'menunggu_acc_pengajuan',
                'pengajuan',
                'menunggu_acc_pengiriman',
                'pengiriman',
                'batal',
                'ditolak'
            ])->default('draft');
            $table->string('application_letter_path')->nullable();
            $table->string('placement_letter_path')->nullable();
            $table->string('ba_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_internship_applications');
    }
};
