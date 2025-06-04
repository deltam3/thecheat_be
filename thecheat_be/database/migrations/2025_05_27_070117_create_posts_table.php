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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('user_id'); 
            $table->unsignedBigInteger('community_id'); 
            $table->string('title'); 
            $table->text('content');
            $table->unsignedInteger('view_count')->default(0);
            $table->softDeletes(); 
        
            $table->index('community_id', 'idx_posts_communities_id');
    
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('community_id')->references('id')->on('communities');


            $table->boolean('is_flagged')->default(false);  
            $table->string('profile_image', 255)->nullable();  
            $table->string('thumbnail_image', 255)->nullable(); 
            $table->string('police_station_name', 255)->nullable();  
            $table->string('victim_site_url', 255)->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
