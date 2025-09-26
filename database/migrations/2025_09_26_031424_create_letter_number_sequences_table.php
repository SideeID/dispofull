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
        Schema::create('letter_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');
            $table->year('year'); // Tahun
            $table->unsignedInteger('last_number')->default(0); // Nomor terakhir yang digunakan
            $table->string('prefix')->nullable(); // Prefix nomor surat
            $table->string('suffix')->nullable(); // Suffix nomor surat
            $table->timestamps();

            // Unique constraint untuk kombinasi letter_type, department, dan year
            $table->unique(['letter_type_id', 'department_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_number_sequences');
    }
};
