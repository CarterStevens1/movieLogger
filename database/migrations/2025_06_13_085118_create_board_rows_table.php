<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('board_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            $table->integer('row_index'); // 1, 2, 3, etc. (1-based for display)
            $table->string('label'); // 1, 2, 3, etc.
            $table->integer('position')->default(0); // For custom ordering
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique(['board_id', 'row_index']);
            $table->index(['board_id', 'position']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('board_rows');
    }
};
