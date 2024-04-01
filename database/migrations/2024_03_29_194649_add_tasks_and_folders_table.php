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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->string('folder_name');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
            // $table->foreign('folder_name')->references('folder_name')->on('files');
        });

        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('original_name');
            $table->string('directory');
            $table->string('folder_name');
            $table->string('url');
            $table->string('task_id');
            // $table->foreign('task_id')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //        
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('files');
    }
};
