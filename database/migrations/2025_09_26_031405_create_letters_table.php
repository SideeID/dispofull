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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number')->unique(); // Nomor surat
            $table->string('subject'); // Perihal/subjek surat
            $table->text('content')->nullable(); // Isi surat
            $table->date('letter_date'); // Tanggal surat
            $table->enum('direction', ['incoming', 'outgoing']); // Surat masuk/keluar
            $table->enum('status', ['draft', 'pending', 'processed', 'archived', 'rejected', 'closed'])->default('draft');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // Pengirim/Penerima
            $table->string('sender_name')->nullable(); // Nama pengirim (untuk surat masuk)
            $table->string('sender_address')->nullable(); // Alamat pengirim
            $table->string('recipient_name')->nullable(); // Nama penerima (untuk surat keluar)
            $table->string('recipient_address')->nullable(); // Alamat penerima

            // Relasi
            $table->foreignId('letter_type_id')->constrained()->onDelete('restrict');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('from_department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('to_department_id')->nullable()->constrained('departments')->onDelete('set null');

            // File paths
            $table->string('original_file_path')->nullable(); // File asli surat
            $table->string('signed_file_path')->nullable(); // File yang sudah ditandatangani

            // Tracking
            $table->timestamp('received_at')->nullable(); // Kapan surat diterima
            $table->timestamp('processed_at')->nullable(); // Kapan surat diproses
            $table->timestamp('archived_at')->nullable(); // Kapan surat diarsipkan

            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();

            // Indexes
            $table->index(['direction', 'status']);
            $table->index(['letter_date']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
