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
        Schema::create('scam_report', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->foreign('user_id')->references('id')->on('users'); 

            $table->string('anon_session_id', 255)->nullable(); 
            $table->enum('report_type', ['physical', 'virtual', 'private', 'crypto']); 

            $table->unsignedBigInteger('site_type_id'); 
            $table->foreign('site_type_id')->references('id')->on('site_types')->onDelete('restrict'); 

            $table->unsignedBigInteger('item_type_id'); 
            $table->foreign('item_type_id')->references('id')->on('item_types')->onDelete('restrict'); 
            $table->string('item_name', 255); 

            $table->string('crypto_type', 50)->nullable(); 
            $table->string('crypto_wallet_address', 255)->nullable(); 
            $table->decimal('crypto_amount', 20, 8)->nullable(); 

            $table->unsignedBigInteger('scammer_bank_id')->nullable(); 
            $table->foreign('scammer_bank_id')->references('id')->on('bank_types'); 
            $table->string('scammer_bank_account_name', 255)->nullable(); 
            $table->string('scammer_bank_account_number', 50)->nullable(); 
            $table->decimal('scammer_bank_amount', 20, 2)->default(0); 
            $table->date('scammer_bank_sent_date')->nullable(); 

            $table->string('scammer_phone_number', 30)->nullable(); 
            $table->enum('scammer_sex', ['male', 'female', 'unknown']); 
            $table->string('scammer_id', 255)->nullable(); 

            $table->text('description'); 
            $table->string('victim_name', 255)->nullable(); 

            $table->softDeletes(); 

            
            $table->index('user_id');
            $table->index('scammer_id');
            $table->index('scammer_phone_number');
            $table->index('scammer_bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scam_report');
    }
};
