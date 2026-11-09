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
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->enum('condition_out', ['good', 'defect', 'damage'])->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->dropColumn('condition_out');
        });
    }
};
