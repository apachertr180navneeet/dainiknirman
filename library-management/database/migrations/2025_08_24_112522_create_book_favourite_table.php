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
        if(!Schema::hasTable('book_favourite')){
            Schema::create('book_favourite', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->foreign('user_id')->references('id')->on('users');
                $table->unsignedBigInteger('book_id')->index();
                $table->foreign('book_id')->references('id')->on('books');
                $table->unsignedBigInteger('created_by')->index()->nullable();
                $table->foreign('created_by')->references('id')->on('users');
                $table->unsignedBigInteger('updated_by')->index()->nullable();
                $table->foreign('updated_by')->references('id')->on('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_favourite');
    }
};
