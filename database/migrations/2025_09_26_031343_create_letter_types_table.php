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
        Schema::create('letter_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama jenis surat
            $table->string('code')->unique(); // Kode jenis surat
            $table->text('description')->nullable(); // Deskripsi
            $table->string('number_format')->nullable(); // Format nomor surat, contoh: {number}/{code}/{month}/{year}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_types');
    }
};
