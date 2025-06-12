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
        Schema::create('report_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // yang mengubah status
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null'); // posisi yang mengubah status
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable(); // bisa isi alasan perubahan status
            $table->foreignId('disposition_id')->nullable()->constrained('report_dispositions')->onDelete('set null'); // disposisi terkait jika ada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_status_logs');
    }
};
