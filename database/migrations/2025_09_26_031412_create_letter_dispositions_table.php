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
        Schema::create('letter_dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users')->onDelete('restrict'); // Yang mendisposisi
            $table->foreignId('to_user_id')->constrained('users')->onDelete('restrict'); // Yang menerima disposisi
            $table->foreignId('to_department_id')->nullable()->constrained('departments')->onDelete('set null');

            $table->text('instruction'); // Instruksi disposisi
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->date('due_date')->nullable(); // Batas waktu penyelesaian
            $table->enum('status', ['pending', 'in_progress', 'completed', 'returned'])->default('pending');

            $table->text('response')->nullable(); // Tanggapan dari penerima disposisi
            $table->timestamp('read_at')->nullable(); // Kapan disposisi dibaca
            $table->timestamp('completed_at')->nullable(); // Kapan disposisi diselesaikan

            $table->timestamps();

            // Indexes
            $table->index(['letter_id']);
            $table->index(['to_user_id', 'status']);
            $table->index(['due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_dispositions');
    }
};
