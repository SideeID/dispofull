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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama departemen/unit kerja
            $table->string('code')->unique(); // Kode departemen
            $table->text('description')->nullable(); // Deskripsi
            $table->enum('type', ['rektorat', 'unit_kerja'])->default('unit_kerja'); // Tipe departemen
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
