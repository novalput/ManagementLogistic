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
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->string('borrowing_code')->unique();
            $table->foreignId('borrower_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->dateTime('borrow_date');
            $table->dateTime('due_date');
            $table->enum('status', ['pending', 'borrowed', 'returned', 'overdue'])->default('pending');
            $table->dateTime('requested_at');
            $table->foreignId('authorized_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('authorized_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
