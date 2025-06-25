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
        Schema::create('board_cells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            $table->foreignId('board_row_id')->constrained()->onDelete('cascade');
            $table->foreignId('board_column_id')->constrained()->onDelete('cascade');
            $table->text('value')->nullable();
            $table->json('tag_config')->nullable(); // Store tag information
            $table->timestamps();

            // Ensure unique cell per row/column combination
            $table->unique(['board_row_id', 'board_column_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_cells');
    }
};
