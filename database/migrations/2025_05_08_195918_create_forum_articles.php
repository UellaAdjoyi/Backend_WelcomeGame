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
        Schema::create('forum_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_feed_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('forum_content');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('media')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_articles');
    }
};
