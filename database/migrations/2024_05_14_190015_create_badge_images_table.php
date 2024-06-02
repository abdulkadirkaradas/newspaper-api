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
        Schema::create('badge_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('ext');
            $table->string('fullpath');
            $table->foreignUuid('badge_id');
            $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_images');
    }
};
