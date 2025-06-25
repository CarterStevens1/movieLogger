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
        Schema::create('board_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            $table->integer('column_index'); // 0, 1, 2, etc.
            $table->string('label'); // A, B, C, AA, etc.
            $table->integer('position')->default(0); // For custom ordering
            $table->boolean('is_visible')->default(true);
            $table->json('sort_config')->nullable(); // Store sort direction, etc.
            $table->timestamps();

            $table->unique(['board_id', 'column_index']);
            $table->index(['board_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_columns');
    }
};
