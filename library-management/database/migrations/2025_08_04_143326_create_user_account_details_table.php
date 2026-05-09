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
        if(!Schema::hasTable('user_account_details')){
            Schema::create('user_account_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('users')->on('id')->onDelete('cascade');
                $table->string('account_holder_name', 100);
                $table->string('account_number', 50);
                $table->string('ifsc_code', 20);
                $table->string('branch_name', 100);
                $table->string('city_name', 100);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_account_details');
    }
};
