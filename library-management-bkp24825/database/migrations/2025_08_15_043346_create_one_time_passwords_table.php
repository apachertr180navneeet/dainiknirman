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
        if(!Schema::hasTable("one_time_passwords")){
            Schema::create('one_time_passwords', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->integer('one_time_password');
                $table->string('email', 225)->index()->nullable();
                $table->string('mobile_number',225)->index()->nullable();
                $table->string('request_token',225)->index()->nullable();
                $table->string('type', 100);
                $table->dateTime('expires_at');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('one_time_passwords');
    }
};
