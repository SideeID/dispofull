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
        Schema::table('letter_dispositions', function (Blueprint $table) {
            // Make to_user_id nullable to allow disposition to department only
            $table->foreignId('to_user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letter_dispositions', function (Blueprint $table) {
            // Revert to not nullable
            $table->foreignId('to_user_id')->nullable(false)->change();
        });
    }
};
