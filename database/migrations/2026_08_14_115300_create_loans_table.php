<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();

            $table->timestamp('borrowed_at')->useCurrent();
            $table->date('due_at');
            $table->timestamp('returned_at')->nullable();

            // active: currently borrowed, not yet returned, not overdue
            // overdue: past due_at, not yet returned
            // returned: book has been given back
            $table->enum('status', ['active', 'overdue', 'returned'])->default('active');

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index('due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
