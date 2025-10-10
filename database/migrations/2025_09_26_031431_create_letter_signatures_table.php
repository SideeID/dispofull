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
        Schema::create('letter_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // Yang menandatangani
            $table->enum('signature_type', ['digital', 'electronic'])->default('digital');
            $table->string('signature_path')->nullable(); // Path file tanda tangan
            $table->text('signature_data')->nullable(); // Data tanda tangan dalam format base64
            $table->timestamp('signed_at')->nullable(); // Kapan ditandatangani
            $table->string('ip_address')->nullable(); // IP address saat menandatangani
            $table->text('user_agent')->nullable(); // User agent saat menandatangani
            $table->enum('status', ['pending', 'signed', 'rejected'])->default('pending');
            $table->text('notes')->nullable(); // Catatan tanda tangan
            $table->timestamps();

            // Index
            $table->index(['letter_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_signatures');
    }
};
