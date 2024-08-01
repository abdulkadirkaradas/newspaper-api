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
        Schema::create('opposite_news', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('source_user_id');
            $table->foreignUuid('opposite_user_id');
            $table->foreignUuid('source_news_id');
            $table->foreignUuid('opposite_news_id');
            $table->foreign('source_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('opposite_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('source_news_id')->references('id')->on('news')->onDelete('cascade');
            $table->foreign('opposite_news_id')->references('id')->on('news')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opposite_news');
    }
};
