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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->text('source_text');
            $table->text('translated_text');
            $table->unsignedBigInteger('source_language_id');
            $table->unsignedBigInteger('target_language_id');
            $table->unsignedBigInteger('region_id')->nullable(); // For region-specific translations
            $table->unsignedBigInteger('user_id')->nullable(); // User who requested the translation
            $table->string('translation_method')->default('api'); // 'api', 'manual', etc.
            $table->text('context')->nullable(); // Context for the translation
            $table->integer('rating')->nullable(); // User rating of the translation (1-5)
            $table->text('feedback')->nullable(); // User feedback on the translation
            $table->boolean('contains_idioms')->default(false);
            $table->timestamps();

            $table->foreign('source_language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->foreign('target_language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
