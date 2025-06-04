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
        Schema::create('post_views', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->unsignedBigInteger('post_id'); 
            $table->timestamp('viewed_at')->useCurrent(); 

            $table->unique(['user_id', 'post_id'], 'unique_user_post');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('post_id')->references('id')->on('posts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_views');
    }
};
