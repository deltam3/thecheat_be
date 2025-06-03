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
        Schema::create('pinned_posts', function (Blueprint $table) {
            $table->timestamps();
            $table->unsignedBigInteger('community_id'); 
            $table->unsignedBigInteger('post_id');  

            $table->primary(['community_id', 'post_id']);

            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinned_posts');
    }
};
