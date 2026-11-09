<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Used by API (and optionally web) to track partial returns per borrowing_detail row.
     */
    public function up(): void
    {
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->unsignedInteger('returned_quantity')->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->dropColumn('returned_quantity');
        });
    }
};
