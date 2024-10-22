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
        Schema::create('news', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->longText('content');
            $table->integer('priority')->default(3);
            $table->boolean('pinned')->default(false);
            $table->boolean('visibility')->default(true);
            $table->boolean('opposition')->default(false);
            $table->boolean('approved')->default(false);
            $table->uuid('approved_by')->nullable();
            $table->uuid('opposition_news_id')->nullable();
            $table->uuid('removed_by')->nullable();
            $table->foreignUuid('user_id');
            $table->foreignUuid('category_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('news_categories')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
