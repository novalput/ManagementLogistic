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
        if (DB::getDriverName() === 'sqlite') {
            try {
                DB::table('users')->where('role', 'borrower')->update(['role' => 'supervisor']);
            } catch (\Exception $e) {
            }
        } else {
            DB::table('users')->where('role', 'borrower')->update(['role' => 'supervisor']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'supervisor') DEFAULT 'supervisor'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            try {
            } catch (\Exception $e) {
            }
        } else {
            DB::table('users')->where('role', 'supervisor')->update(['role' => 'borrower']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'borrower') DEFAULT 'borrower'");
        }
    }
};
