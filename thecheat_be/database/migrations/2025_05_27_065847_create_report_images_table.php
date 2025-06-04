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
        Schema::create('report_images', function (Blueprint $table) {
            $table->timestamps();
            $table->id(); 

            $table->unsignedBigInteger('scam_report_id'); 
            $table->string('image_url', 255); 

            $table->foreign('scam_report_id')->references('id')->on('scam_report')->onDelete('cascade');
            $table->index('scam_report_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_images');
    }
};
