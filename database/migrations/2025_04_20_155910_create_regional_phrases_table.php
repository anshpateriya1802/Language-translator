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
        Schema::create('regional_phrases', function (Blueprint $table) {
            $table->id();
            $table->text('source_phrase');
            $table->text('translation');
            $table->unsignedBigInteger('source_language_id');
            $table->unsignedBigInteger('target_language_id');
            $table->unsignedBigInteger('region_id')->nullable();
            $table->text('context')->nullable(); // Additional context information
            $table->boolean('is_idiom')->default(false);
            $table->boolean('is_slang')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('source_language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->foreign('target_language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regional_phrases');
    }
};
