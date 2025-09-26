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
        Schema::create('letter_agendas', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul agenda
            $table->text('description')->nullable(); // Deskripsi agenda
            $table->date('agenda_date'); // Tanggal agenda
            $table->date('start_date'); // Tanggal mulai periode
            $table->date('end_date'); // Tanggal akhir periode
            $table->enum('type', ['daily', 'weekly', 'monthly'])->default('monthly'); // Tipe agenda
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('pdf_path')->nullable(); // Path file PDF agenda
            $table->json('filters')->nullable(); // Filter criteria (letter types, departments, etc)
            $table->timestamps();

            // Index
            $table->index(['agenda_date']);
            $table->index(['department_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_agendas');
    }
};
