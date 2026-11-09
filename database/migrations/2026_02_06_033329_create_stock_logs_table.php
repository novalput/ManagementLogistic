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
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Who performed the action
            $table->integer('quantity_change'); // Positive for return/add, negative for borrow/remove
            $table->string('action'); // 'borrow', 'return', 'adjustment', 'correction'
            $table->string('description')->nullable();
            $table->json('meta_data')->nullable(); // For storing borrowing_id, return_id, etc.
            $table->timestamps();
            
            $table->index(['item_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
