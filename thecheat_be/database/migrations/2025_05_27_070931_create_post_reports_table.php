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
        Schema::create('post_reports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade'); 
            $table->foreignId('reported_by_user_id')->constrained('users')->onDelete('cascade'); 
            $table->text('comment');

            $table->index('post_id');  
            $table->index('reported_by_user_id');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reports');
    }
};
