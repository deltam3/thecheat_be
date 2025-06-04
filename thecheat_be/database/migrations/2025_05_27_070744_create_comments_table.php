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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('post_id'); 
            $table->unsignedBigInteger('user_id'); 
            $table->unsignedBigInteger('parent_comment_id')->nullable(); 

            $table->text('content'); 
            $table->string('username')->nullable(); 
            $table->string('profile_image')->nullable(); 

            $table->softDeletes(); 

            $table->index('post_id', 'idx_comments_post_id');
            $table->index('user_id', 'idx_comments_user_id');

            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('parent_comment_id')->references('id')->on('comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
