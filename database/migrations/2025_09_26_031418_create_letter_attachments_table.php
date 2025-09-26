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
        Schema::create('letter_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained()->onDelete('cascade');
            $table->string('original_name'); // Nama file asli
            $table->string('file_name'); // Nama file yang disimpan
            $table->string('file_path'); // Path file
            $table->string('file_type'); // Tipe file (pdf, doc, jpg, dll)
            $table->bigInteger('file_size'); // Ukuran file dalam bytes
            $table->text('description')->nullable(); // Deskripsi lampiran
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            // Index
            $table->index(['letter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_attachments');
    }
};
