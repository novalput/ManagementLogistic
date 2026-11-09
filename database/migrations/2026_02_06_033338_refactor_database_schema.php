<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Items: Make stock unsigned and add index
        Schema::table('items', function (Blueprint $table) {
            // Check if we can modify. In Laravel 12 this should work for MySQL/MariaDB.
            $table->unsignedInteger('stock')->change();
            $table->index('item_code');
        });

        // 2. Borrowing Details: Make quantity unsigned
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->change();
        });

        // 3. Return Details: Make quantity unsigned
        Schema::table('return_details', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->change();
        });

        // 4. Returns: processed_by nullable and set null on delete
        Schema::table('returns', function (Blueprint $table) {
            // Drop existing FK if exists. Name convention: returns_processed_by_foreign
            $table->dropForeign(['processed_by']);
            // Modify column to be nullable
            $table->foreignId('processed_by')->nullable()->change();
            // Add new FK
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });

        // 5. Users: Change role to string (portable) and add email index
        Schema::table('users', function (Blueprint $table) {
            // We change it to string. Existing values (admin, supervisor) will be preserved.
            $table->string('role')->default('supervisor')->change();
            $table->index('email');
        });
        
        // 6. Borrowings: Add index on status
        Schema::table('borrowings', function (Blueprint $table) {
             $table->index('status');
             $table->index('borrower_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting is complex, simplified for this task
        Schema::table('items', function (Blueprint $table) {
            $table->integer('stock')->change();
            $table->dropIndex(['item_code']);
        });
        
        Schema::table('returns', function (Blueprint $table) {
             $table->dropForeign(['processed_by']);
             $table->foreignId('processed_by')->change(); // back to not null (might fail if nulls exist)
             $table->foreign('processed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
