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
        Schema::create('comment_images', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('comment_id'); 
            $table->string('image_url', 500); 
            $table->integer('image_order')->default(0); 

            $table->index('comment_id', 'idx_comment_images_comment_id');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_images');
    }
};
